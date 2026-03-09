@extends('forest.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-green-800 mb-6">
        🌟 Рекомендации друзей
    </h1>

    {{-- Информация об алгоритме --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800">
            <strong>📊 Уровень алгоритма:</strong> {{ $meta['level_applied'] ?? 'N/A' }}<br>
            <strong>📝 Правило:</strong> {{ $meta['rule'] ?? 'N/A' }}<br>
            <strong>🎯 Имя лучшего друга:</strong> {{ $meta['best_friend_name'] ?? 'N/A' }}
        </p>
    </div>

    @if(empty($recommendations))
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <p class="text-4xl mb-3">🍃</p>
            <p class="text-gray-500">К сожалению, нет рекомендаций для вас</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($recommendations as $userFound)
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl">
                        {{ $userFound['animalType']['icon'] ?? '🦊' }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $userFound['name'] }}</p>
                        <p class="text-sm text-gray-500">
                            @{{ $userFound['nickname'] ?? 'без никнейма' }} • {{ $userFound['animalType']['name'] ?? 'Неизвестно' }}
                        </p>
                        @if(isset($userFound['similarity']))
                            <p class="text-xs text-purple-600">
                                🎯 Схожесть: {{ round($userFound['similarity'] * 100) }}%
                            </p>
                        @endif
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('forest.friends.send') }}">
                        @csrf
                        <input type="hidden" name="friend_id" value="{{ $userFound['id'] }}">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                            + В друзья
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