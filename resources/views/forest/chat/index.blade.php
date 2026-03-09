@extends('forest.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-800">💬 Мои диалоги</h1>
        <div class="flex gap-3">
            <a href="{{ route('forest.chat.groups') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                👥 Групповые чаты
            </a>
            {{-- Кнопка открывает модальное окно --}}
            <button onclick="document.getElementById('newChatModal').classList.remove('hidden')" 
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                + Новый диалог
            </button>
        </div>
    </div>

    @if(empty($conversations))
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <p class="text-4xl mb-3">💭</p>
            <p class="text-gray-500">Пока нет диалогов. Начните общение!</p>
            <button onclick="document.getElementById('newChatModal').classList.remove('hidden')" 
                    class="inline-block mt-4 text-green-600 hover:underline cursor-pointer">
                Выбрать друга →
            </button>
        </div>
    @else
        <div class="grid gap-3">
            @foreach($conversations as $conv)
            <a href="{{ route('forest.chat.conversation', $conv['user']->id) }}" 
               class="bg-white hover:bg-green-50 rounded-xl shadow p-4 flex items-center justify-between transition group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">
                        {{ $conv['user']->animalType->icon ?? '🦊' }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 group-hover:text-green-700">
                            {{ $conv['user']->name }}
                        </p>
                        <p class="text-sm text-gray-500 truncate max-w-md">
                            {{ \Illuminate\Support\Str::limit($conv['last_message']->content, 50) }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">
                        {{ $conv['last_message']->created_at->format('H:i') }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>

{{-- 🔹 Модальное окно "Новый диалог" --}}
<div id="newChatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">💬 Новый диалог</h2>
            <button onclick="document.getElementById('newChatModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        @if($user->getFriendsList()->isEmpty())
            <p class="text-gray-500 text-center py-6">
                У вас пока нет друзей.<br>
                <a href="{{ route('forest.friends.search') }}" class="text-green-600 hover:underline">
                    Найти друзей →
                </a>
            </p>
        @else
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($user->getFriendsList() as $friend)
                <a href="{{ route('forest.chat.conversation', $friend->id) }}" 
                   class="flex items-center gap-3 p-3 hover:bg-green-50 rounded-lg transition">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-xl">
                        {{ $friend->animalType->icon ?? '🦊' }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $friend->name }}</p>
                        <p class="text-xs text-gray-500">{{ $friend->nickname ?? 'без никнейма' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection