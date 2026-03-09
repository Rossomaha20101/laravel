<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Волшебный Лес')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen">
    {{-- Шапка --}}
    <header class="bg-green-700 text-white py-4">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <a href="{{ route('forest.dashboard') }}" class="text-xl font-bold">🌲 Волшебный Лес</a>
            <nav class="flex gap-4">
                <a href="{{ route('forest.chat.index') }}" class="hover:text-green-200">💬 Чат</a>
                <a href="{{ route('forest.dashboard') }}" class="hover:text-green-200">Профиль</a>
                <a href="{{ route('forest.friends.index') }}" class="hover:text-green-200">Друзья</a>
                <form method="POST" action="{{ route('forest.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-green-200">Выйти</button>
                </form>
            </nav>
        </div>
    </header>

    {{-- Контент --}}
    <main>
        @yield('content')
    </main>
</body>
</html>