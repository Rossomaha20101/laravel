@extends('forest.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-green-800 mb-6">📥 Входящие заявки</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <p class="text-4xl mb-3">✨</p>
            <p class="text-gray-500">Нет новых заявок. <a href="{{ route('forest.friends.search') }}" class="text-green-600 hover:underline">Найдите друзей сами!</a></p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($requests as $sender)
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">
                        {{ $sender->animalType->icon ?? '🦊' }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $sender->name }}</p>
                        <p class="text-sm text-gray-500">{{ $sender->nickname }} хочет добавить вас в друзья</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('forest.friends.accept', $sender->id) }}">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                            ✅ Принять
                        </button>
                    </form>
                    <form method="POST" action="{{ route('forest.friends.reject', $sender->id) }}">
                        @csrf
                        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm font-medium transition">
                            ❌ Отклонить
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('forest.friends.index') }}" class="text-green-600 hover:underline">← Мои друзья</a>
    </div>
</div>
@endsection