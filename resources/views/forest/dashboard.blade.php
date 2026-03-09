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
                    Привет, <strong class="text-white">{{ $user->nickname ?? 'лесной_зверь' }}</strong>! 🦊
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
                                $avatars = ['🦊', '🐻', '', '🐰', '🦔', '🐺', '', '🐿️'];
                                $avatar = $avatars[$user->animal_type_id - 1] ?? '🦊';
                            @endphp
                            {{ $avatar }}
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-green-100">{{ $user->nickname ?? 'без никнейма' }}</p>
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
                    <!-- Друзья -->
                    <a href="{{ route('forest.friends.index') }}" class="block bg-white rounded-xl p-4 shadow text-center hover:shadow-lg transition">
                        <div class="text-3xl font-bold text-green-600">{{ $user->getFriendsList()->count() }}</div>
                        <div class="text-sm text-gray-500">Друзья</div>
                    </a>
                    
                    <!-- Сообщения (личные) -->
                    <a href="{{ route('forest.chat.index') }}" class="block bg-white rounded-xl p-4 shadow text-center hover:shadow-lg transition">
                        @php
                            // Считаем количество диалогов (уникальных собеседников)
                            $dialogsCount = \App\Models\ForestMessage::where('type', 'personal')
                                ->where(function($q) use ($user) {
                                    $q->where('sender_id', $user->id)
                                    ->orWhere('recipient_id', $user->id);
                                })
                                ->distinct('recipient_id', 'sender_id')
                                ->count();
                            // Упрощённый вариант: просто количество последних сообщений
                            $messagesCount = \App\Models\ForestMessage::where('type', 'personal')
                                ->where('recipient_id', $user->id)
                                ->count();
                        @endphp
                        <div class="text-3xl font-bold text-blue-600">{{ $dialogsCount }}</div>
                        <div class="text-sm text-gray-500">Диалоги</div>
                    </a>
                    
                    <!-- Групповые чаты -->
                    <a href="{{ route('forest.chat.groups') }}" class="block bg-white rounded-xl p-4 shadow text-center hover:shadow-lg transition">
                        @php
                            $groupsCount = $user->messageGroups()->count();
                        @endphp
                        <div class="text-3xl font-bold text-purple-600">{{ $groupsCount }}</div>
                        <div class="text-sm text-gray-500">Группы</div>
                    </a>
                </div>

                <!-- 🔗 НАВИГАЦИЯ: ДРУЗЬЯ И ЧАТ -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">🤝 Друзья и общение</h3>
                    
                    <div class="space-y-3">
                        <!-- 💬 Чат: Список диалогов -->
                        <a href="{{ route('forest.chat.index') }}" 
                        class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl group-hover:scale-110 transition">💬</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Мои диалоги</p>
                                    <p class="text-sm text-gray-500">Личные сообщения</p>
                                </div>
                            </div>
                            <span class="text-gray-400 group-hover:translate-x-1 transition">→</span>
                        </a>

                        <!-- 👥 Мои друзья -->
                        <a href="{{ route('forest.friends.index') }}" 
                        class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-xl transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl group-hover:scale-110 transition">👥</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Мои друзья</p>
                                    <p class="text-sm text-gray-500">Список ваших друзей в лесу</p>
                                </div>
                            </div>
                            <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                            {{ $user->getFriendsList()->count() }}
                            </span>
                        </a>
                        
                        <!-- 📥 Входящие заявки -->
                        <a href="{{ route('forest.friends.requests') }}" 
                        class="flex items-center justify-between p-4 bg-yellow-50 hover:bg-yellow-100 rounded-xl transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl group-hover:scale-110 transition">📥</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Входящие заявки</p>
                                    <p class="text-sm text-gray-500">Кто хочет добавить вас в друзья</p>
                                </div>
                            </div>
                            @php
                                $pendingCount = \App\Models\ForestFriendship::where('friend_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold animate-pulse">
                                    {{ $pendingCount }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">0</span>
                            @endif
                        </a>
                        
                        <!-- 🔍 Поиск друзей -->
                        <a href="{{ route('forest.friends.search') }}" 
                        class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl group-hover:scale-110 transition">🔍</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Найти новых друзей</p>
                                    <p class="text-sm text-gray-500">Поиск по имени или никнейму</p>
                                </div>
                            </div>
                            <span class="text-gray-400 group-hover:translate-x-1 transition">→</span>
                        </a>

                        <!-- 👥 Групповые чаты -->
                        <a href="{{ route('forest.chat.groups') }}" 
                        class="flex items-center justify-between p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl group-hover:scale-110 transition">👥</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Групповые чаты</p>
                                    <p class="text-sm text-gray-500">Общение с несколькими друзьями</p>
                                </div>
                            </div>
                            @php
                                $myGroups = $user->messageGroups()->count();
                            @endphp
                            @if($myGroups > 0)
                                <span class="bg-indigo-200 text-indigo-800 px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $myGroups }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">0</span>
                            @endif
                        </a>
                    </div>
                </div>
                <!-- 🔗 КОНЕЦ НАВИГАЦИИ -->

                <!-- Лента активности -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">🌟 Ваша активность</h3>
                    
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-4xl mb-3">🍃</p>
                        <p>Пока нет активности. Начните общаться в лесу!</p>
                        <a href="{{ route('forest.friends.search') }}" 
                        class="inline-block mt-4 bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition">
                            Найти друзей
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Подвал -->
    <footer class="bg-green-900/80 text-green-100 py-6 mt-12">
        <div class="max-w-6xl mx-auto px-4 text-center text-sm">
            <p>© {{ date('Y') }} Волшебный Лес 🌿 Все права защищены</p>
            <p class="mt-2 text-green-200">
                <a href="{{ route('home') }}" class="hover:text-white transition">Главная</a>
                • 
                <a href="#" class="hover:text-white transition">Правила</a>
                • 
                <a href="{{ route('contact') }}" class="hover:text-white transition">Поддержка</a>
            </p>
        </div>
    </footer>

</body>
</html>