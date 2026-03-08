<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой профиль — Волшебный Лес 🌲</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .forest-bg {
            background: linear-gradient(135deg, #1a4314 0%, #2d5a27 50%, #4a7c3a 100%);
        }
    </style>
</head>
<body class="forest-bg min-h-screen">
    
    <!-- Шапка -->
    <header class="bg-green-900/80 backdrop-blur-sm shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-white hover:text-green-200 transition">
                🌲 Волшебный Лес
            </a>
            
            <nav class="flex items-center gap-4">
                <span class="text-green-100">
                    Привет, <strong class="text-white">@{{ $user->nickname }}</strong>! 🦊
                </span>
                
                <form method="POST" action="{{ route('forest.logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Выйти
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        
        {{-- Сообщения об успехе --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-6">
            
            <!-- Карточка профиля -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <!-- Аватар -->
                    <div class="bg-gradient-to-r from-green-400 to-green-600 p-6 text-center">
                        <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center text-4xl mb-3">
                            @php
                                $avatars = ['🦊', '🐻', '🦉', '🐰', '🦔', '🐺', '🦌', '🐿️'];
                                $avatar = $avatars[$user->animal_type_id - 1] ?? '🦊';
                            @endphp
                            {{ $avatar }}
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-green-100">@{{ $user->nickname }}</p>
                    </div>
                    
                    <!-- Информация -->
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3 text-gray-700">
                            <span class="text-xl">📧</span>
                            <span class="text-sm">{{ $user->email }}</span>
                        </div>
                        
                        <div class="flex items-center gap-3 text-gray-700">
                            <span class="text-xl">⚧</span>
                            <span class="text-sm">{{ $user->gender === 'F' ? 'Девочка' : 'Мальчик' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-3 text-gray-700">
                            <span class="text-xl">🎂</span>
                            <span class="text-sm">{{ $user->birth_date?->format('d.m.Y') }}</span>
                        </div>
                        
                        <div class="flex items-center gap-3 text-gray-700">
                            <span class="text-xl">🤝</span>
                            <span class="text-sm">Лучший друг: <strong>{{ $user->best_friend_name }}</strong></span>
                        </div>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="px-6 pb-6 space-y-2">
                        <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium transition">
                            ✏️ Редактировать профиль
                        </button>
                        <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded-lg font-medium transition">
                            ⚙️ Настройки
                        </button>
                    </div>
                </div>
            </div>

            <!-- Основной контент -->
            <div class="md:col-span-2 space-y-6">
                
                <!-- Статистика -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl p-4 shadow text-center">
                        <div class="text-3xl font-bold text-green-600">0</div>
                        <div class="text-sm text-gray-500">Друзья