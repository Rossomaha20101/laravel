@extends('forest.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Шапка диалога -->
    <div class="bg-white rounded-t-xl shadow p-4 flex items-center justify-between border-b">
        <div class="flex items-center gap-4">
            <a href="{{ route('forest.chat.index') }}" class="text-gray-500 hover:text-gray-700">
                ← Назад
            </a>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-xl">
                {{ $otherUser->animalType->icon ?? '🦊' }}
            </div>
            <div>
                <p class="font-semibold text-gray-800">{{ $otherUser->name }}</p>
                <p class="text-xs text-gray-500">{{ $otherUser->nickname ?? 'без никнейма' }}</p>
            </div>
        </div>
    </div>

    <!-- Сообщения -->
    <div class="bg-gray-50 p-6 min-h-[400px] max-h-[600px] overflow-y-auto">
        @if($messages->isEmpty())
            <p class="text-center text-gray-500 py-12">Нет сообщений. Напишите первым! 💬</p>
        @else
            @foreach($messages as $message)
                @php
                    $isMe = $message->sender_id === $user->id;
                @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} mb-4">
                    <div class="max-w-[70%] {{ $isMe ? 'bg-green-500 text-white' : 'bg-white text-gray-800' }} rounded-2xl px-4 py-3 shadow">
                        <p class="text-sm">{{ $message->content }}</p>
                        <p class="text-xs {{ $isMe ? 'text-green-100' : 'text-gray-400' }} mt-1">
                            {{ $message->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Форма отправки -->
    <form method="POST" action="{{ route('forest.chat.send', $otherUser->id) }}" class="bg-white rounded-b-xl shadow p-4">
        @csrf
        <div class="flex gap-3">
            <input type="text" 
                   name="content" 
                   maxlength="128"
                   placeholder="Введите сообщение (макс. 128 символов)..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                   required>
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition">
                Отправить →
            </button>
        </div>
        <p class="text-xs text-gray-400 mt-2">Осталось символов: <span id="charCount">128</span>/128</p>
    </form>
</div>

@push('scripts')
<script>
    // Счётчик символов
    const input = document.querySelector('input[name="content"]');
    const count = document.getElementById('charCount');
    
    input.addEventListener('input', function() {
        count.textContent = 128 - this.value.length;
    });
    
    // Автопрокрутка вниз
    const messagesDiv = document.querySelector('.overflow-y-auto');
    if (messagesDiv) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
</script>
@endpush
@endsection