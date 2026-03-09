@extends('layouts.main')

@section('header-title')
Регистрация в Волшебном Лесу 🌲
@endsection

@section('content')
 
<!-- Логотип / Заголовок -->
<div class="text-center mb-8">
<h2 class="text-3xl font-bold text-white">Волшебный Лес</h2>
<p class="text-green-100 mt-2">Социальная сеть для лесных жителей</p>
</div>

<!-- Форма регистрации -->
<div class="rounded-2xl shadow-2xl p-8">
<h3 class="text-2xl font-bold mb-6 text-center">
    🦊 Создать аккаунт
</h3>

    {{-- Сообщения об успехе --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Ошибки валидации --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <h4 class="text-red-800 font-semibold mb-2">⚠️ Ошибки заполнения:</h4>
            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Имя --}}
        <div>
            <label for="name" class="block text-sm font-medium mb-1">
                🐾 Ваше имя *
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name') }}"
                required 
                autofocus
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('name') border-red-500 @enderror"
                placeholder="Лесной Зверь"
            >
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Псевдоним (nickname) --}}
        <div>
            <label for="nickname" class="block text-sm font-medium mb-1">
                🏷️ Псевдоним
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-400">@</span>
                <input 
                    type="text" 
                    name="nickname" 
                    id="nickname" 
                    value="{{ old('nickname') }}"
                    maxlength="50"
                    class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('nickname') border-red-500 @enderror"
                    placeholder="lisichka"
                >
            </div>
            <p class="text-xs mt-1">Имя для профиля (например: @lisichka)</p>
            @error('nickname')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium mb-1">
                📧 Email *
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email') }}"
                required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('email') border-red-500 @enderror"
                placeholder="you@forest.com"
            >
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Пароль --}}
        <div>
            <label for="password" class="block text-sm font-medium mb-1">
                🔒 Пароль *
            </label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                required 
                minlength="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('password') border-red-500 @enderror"
                placeholder="Минимум 8 символов"
            >
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Подтверждение пароля --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">
                🔑 Подтвердите пароль *
            </label>
            <input 
                type="password" 
                name="password_confirmation" 
                id="password_confirmation" 
                required 
                minlength="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                placeholder="Повторите пароль"
            >
        </div>

        {{-- Тип животного --}}
        <div>
            <label for="animal_type_id" class="block text-sm font-medium mb-1">
                🦉 Кто вы в лесу? *
            </label>
            <select 
                name="animal_type_id" 
                id="animal_type_id" 
                required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('animal_type_id') border-red-500 @enderror"
            >
                <option value="">Выберите тип животного</option>
                <option value="1" {{ old('animal_type_id') == 1 ? 'selected' : '' }}>🦊 Лиса</option>
                <option value="2" {{ old('animal_type_id') == 2 ? 'selected' : '' }}>🐻 Медведь</option>
                <option value="3" {{ old('animal_type_id') == 3 ? 'selected' : '' }}>🦉 Сова</option>
                <option value="4" {{ old('animal_type_id') == 4 ? 'selected' : '' }}>🐰 Заяц</option>
                <option value="5" {{ old('animal_type_id') == 5 ? 'selected' : '' }}>🦔 ж</option>
                <option value="6" {{ old('animal_type_id') == 6 ? 'selected' : '' }}>🐺 Волк</option>
                <option value="7" {{ old('animal_type_id') == 7 ? 'selected' : '' }}>🦌 Олень</option>
                <option value="8" {{ old('animal_type_id') == 8 ? 'selected' : '' }}>🐿️ Белка</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Убедитесь, что выбранный тип существует в базе данных</p>
            @error('animal_type_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Пол --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                ⚧ Пол *
            </label>
            <div class="flex gap-4">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input 
                        type="radio" 
                        name="gender" 
                        value="F" 
                        {{ old('gender') === 'F' ? 'checked' : '' }}
                        required
                        class="text-green-600 focus:ring-green-500"
                    >
                    <span> Девочка</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input 
                        type="radio" 
                        name="gender" 
                        value="M" 
                        {{ old('gender') === 'M' ? 'checked' : '' }}
                        required
                        class="text-green-600 focus:ring-green-500"
                    >
                    <span>🎩 Мальчик</span>
                </label>
            </div>
            @error('gender')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Дата рождения --}}
        <div>
            <label for="birth_date" class="block text-sm font-medium mb-1">
                📅 Дата рождения *
            </label>
            <input 
                type="date" 
                name="birth_date" 
                id="birth_date" 
                value="{{ old('birth_date') }}"
                required 
                max="{{ date('Y-m-d', strtotime('-1 year')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('birth_date') border-red-500 @enderror"
            >
            @error('birth_date')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Имя лучшего друга --}}
        <div>
            <label for="best_friend_name" class="block text-sm font-medium mb-1">
                🤝 Имя лучшего друга *
            </label>
            <input 
                type="text" 
                name="best_friend_name" 
                id="best_friend_name" 
                value="{{ old('best_friend_name') }}"
                required 
                maxlength="255"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('best_friend_name') border-red-500 @enderror"
                placeholder="Кто ваш лучший друг в лесу?"
            >
            @error('best_friend_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Кнопка отправки --}}
        <button 
            type="submit" 
            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 shadow-lg"
        >
            🌲 Вступить в Волшебный Лес
        </button>
    </form>

    {{-- Ссылка на вход --}}
    <div class="mt-6 text-center">
        <p class="text-gray-600">
            Уже есть аккаунт?
            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-semibold">
                Войти в лес 🦊
            </a>
        </p>
    </div>

    {{-- Ссылка на админку (для разработчиков) --}}
    <div class="mt-4 text-center">
        <a href="{{ url('/admin') }}" class="text-xs text-gray-400 hover:text-gray-600">
            🔐 Вход для администраторов
        </a>
    </div>
</div>

@endsection       