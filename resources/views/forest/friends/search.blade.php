@extends('forest.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-green-800 mb-6">🔍 Найти друзей</h1>

    {{-- ✅ БЛОК СООБЩЕНИЙ (добавлен) --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <span class="text-xl">❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    
    @if (session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <span class="text-xl">ℹ️</span>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    {{-- ✅ КОНЕЦ БЛОКА СООБЩЕНИЙ --}}

    {{-- Форма поиска --}}
    <form method="GET" action="{{ route('forest.friends.search') }}" class="mb-6 flex gap-2">
        <input type="text" name="q" value="{{ $query }}" 
               placeholder="Поиск по имени или никнейму..." 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition">
            Найти
        </button>
    </form>

    {{-- Результаты --}}
    @if(isset($users) && $users->isEmpty() && $query)
        <p class="text-gray-500 text-center py-8">Никто не найден по запросу "{{ $query }}"</p>
    @endif

    <div class="grid gap-4">
        @foreach($users as $userFound)
        <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl">
                    {{ $userFound->animalType->icon ?? '🦊' }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $userFound->name }}</p>
                    <p class="text-sm text-gray-500">
                        {{-- ✅ Никнейм с символом @ --}}
                        {{ $userFound->nickname ?? 'без никнейма' }} • {{ $userFound->animalType->name ?? 'Неизвестно' }}
                    </p>
                </div>
            </div>
            <div>
                @if($userFound->friendship_status === 'accepted')
                    {{-- Уже друзья --}}
                    <span class="text-green-600 text-sm font-medium">✓ Уже друзья</span>
                    
                @elseif($userFound->friendship_status === 'pending' && $userFound->friendship_direction === 'outgoing')
                    {{-- Я отправил заявку этому пользователю --}}
                    <span class="text-yellow-600 text-sm font-medium">⏳ Заявка отправлена</span>
                    
                @elseif($userFound->friendship_status === 'pending' && $userFound->friendship_direction === 'incoming')
                    {{-- Этот пользователь отправил заявку МНЕ — показываем кнопки --}}
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('forest.friends.accept', $userFound->id) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-sm font-medium transition">
                                ✅ Принять
                            </button>
                        </form>
                        <form method="POST" action="{{ route('forest.friends.reject', $userFound->id) }}">
                            @csrf
                            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm font-medium transition">
                                ❌ Отклонить
                            </button>
                        </form>
                    </div>
                    
                @else
                    {{-- Нет заявки — можно отправить --}}
                    <form method="POST" action="{{ route('forest.friends.send') }}">
                        @csrf
                        <input type="hidden" name="friend_id" value="{{ $userFound->id }}">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                            + В друзья
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        <a href="{{ route('forest.friends.index') }}" class="text-green-600 hover:underline">← Мои друзья</a>
    </div>
</div>
@endsection