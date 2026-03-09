<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ForestUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ForestAuthController extends Controller
{
    /**
     * Показать форму регистрации
     */
    public function showRegisterForm()
    {
        // Если пользователь уже авторизован в соцсети — перенаправляем в дашборд
        if (Auth::guard('forest')->check()) {
            return redirect()->route('forest.dashboard');
        }

        return view('forest.register');
    }

    /**
     * Обработать регистрацию нового пользователя
     */
    public function register(Request $request)
    {
        // 1. Валидация данных
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:50|unique:forest_users,nickname',
            'email' => 'required|string|email|max:255|unique:forest_users,email',
            'password' => 'required|string|min:8|confirmed',
            'animal_type_id' => 'required|integer|exists:animal_types,id',
            'gender' => 'required|in:M,F',
            'birth_date' => 'required|date|before:today',
            'best_friend_name' => 'required|string|max:255',
        ], [
            // Кастомные сообщения об ошибках (опционально)
            'nickname.unique' => 'Этот псевдоним уже занят. Попробуйте другой.',
            'email.unique' => 'Этот email уже зарегистрирован.',
            'animal_type_id.exists' => 'Неверный тип животного.',
            'birth_date.before' => 'Дата рождения должна быть в прошлом.',
        ]);

        try {
            // 2. Создание пользователя
            $user = ForestUser::create([
                'name' => $validated['name'],
                'nickname' => $validated['nickname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'animal_type_id' => $validated['animal_type_id'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'best_friend_name' => $validated['best_friend_name'],
                'remember_token' => Str::random(10), // Опционально: для функции "Запомнить меня"
            ]);

            // 3. Автоматическая авторизация через guard 'forest'
            Auth::guard('forest')->login($user);

            // 4. Регенерация сессии (защита от фиксации сессии)
            $request->session()->regenerate();

            // 5. Перенаправление с сообщением
            return redirect()->route('forest.dashboard')
                ->with('success', "🌲 Добро пожаловать, @{$user->nickname}!");

        } catch (\Illuminate\Database\QueryException $e) {
            // Обработка ошибок базы данных (например, дубликаты)
            if ($e->getCode() === '23000') {
                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Произошла ошибка при регистрации. Возможно, данные уже существуют.']);
            }
            throw $e;
        }
    }

    /**
     * Показать форму входа
     */
    public function showLoginForm()
    {
        // Если уже авторизован — в дашборд
        if (Auth::guard('forest')->check()) {
            return redirect()->route('forest.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Обработать вход пользователя
     */
    public function login(Request $request)
    {
        // 1. Валидация
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Введите email',
            'password.required' => 'Введите пароль',
        ]);

        // 2. Попытка аутентификации через guard 'forest'
        if (Auth::guard('forest')->attempt($credentials, $request->boolean('remember'))) {
            
            // 3. Регенерация сессии после успешного входа
            $request->session()->regenerate();

            // 4. Перенаправление туда, куда пользователь хотел попасть, или в дашборд
            return redirect()->intended(route('forest.dashboard'))
                ->with('success', 'С возвращением в лес! 🦊');
        }

        // 5. Если вход не удался
        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Неверный email или пароль. Попробуйте ещё раз.',
            ]);
    }

    /**
     * Обработать выход из системы
     */
    public function logout(Request $request)
    {
        // Выход только из гварда 'forest' (не затрагивает админку)
        Auth::guard('forest')->logout();

        // Инвалидация сессии
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Вы вышли из аккаунта. Возвращайтесь скорее! 🌲');
    }
}