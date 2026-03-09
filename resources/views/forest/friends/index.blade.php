@extends('forest.layouts.app') {{-- Или ваш основной лейаут --}}

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-800">🤝 Мои друзья</h1>
        <a href="{{ route('forest.friends.search') }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            🔍 Найти друзей
        </a>
    </div>

    {{-- Сообщения --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Список друзей --}}
    @if($friends->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <p class="text-4xl mb-3">🍃</p>
            <p class="text-gray-500">Пока нет друзей. <a href="{{ route('forest.friends.search') }}" class="text-green-600 hover:underline">Найдите новых!</a></p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($friends as $friend)
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">
                        {{ $friend->animalType->icon ?? '🦊' }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $friend->name }}</p>
                        <p class="text-sm text-gray-500">{{ $friend->nickname }} • {{ $friend->animalType->name ?? 'Неизвестно' }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('forest.chat.conversation', $friend->id) }}" class="text-sm text-green-600 hover:underline">💬 Написать</a>
                    <form method="POST" action="{{ route('forest.friends.remove', $friend->id) }}" class="inline" onsubmit="return confirm('Удалить из друзей?');">
                        @csrf
                        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Удалить</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Навигация --}}
    <div class="mt-6 flex gap-4">
        <a href="{{ route('forest.friends.requests') }}" class="text-green-600 hover:underline">
            📥 Входящие заявки ({{ \App\Models\ForestFriendship::where('friend_id', $user->id)->where('status', 'pending')->count() }})
        </a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('forest.dashboard') }}" class="text-gray-500 hover:text-gray-700">← Назад в профиль</a>
    </div>
</div>
@endsection