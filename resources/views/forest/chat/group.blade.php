@extends('forest.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Шапка -->
    <div class="bg-white rounded-t-xl shadow p-4 flex items-center justify-between border-b">
        <div class="flex items-center gap-4">
            <a href="{{ route('forest.chat.groups') }}" class="text-gray-500 hover:text-gray-700">
                ← Назад
            </a>
            <div>
                <p class="font-semibold text-gray-800">{{ $group->name ?? 'Групповой чат' }}</p>
                <p class="text-xs text-gray-500">{{ $group->users->count() }} участников</p>
            </div>
        </div>
    </div>

    <!-- Сообщения -->
    <div class="bg-gray-50 p-6 min-h-[400px] max-h-[600px] overflow-y-auto">
        @foreach($messages as $message)
            <div class="flex justify-start mb-4">
                <div class="max-w-[70%] bg-white text-gray-800 rounded-2xl px-4 py-3 shadow">
                    <p class="text-xs font-semibold text-purple-600 mb-1">
                        {{ $message->sender->name }}
                    </p>
                    <p class="text-sm">{{ $message->content }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $message->created_at->format('H:i') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Форма -->
    <form method="POST" action="{{ route('forest.chat.group.send', $group->id) }}" class="bg-white rounded-b-xl shadow p-4">
        @csrf
        <div class="flex gap-3">
            <input type="text" 
                   name="content" 
                   maxlength="128"
                   placeholder="Введите сообщение..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                   required>
            <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg font-medium transition">
                Отправить →
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const messagesDiv = document.querySelector('.overflow-y-auto');
    if (messagesDiv) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
</script>
@endpush
@endsection