@extends('forest.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-800">👥 Групповые чаты</h1>
        <button onclick="document.getElementById('createGroupModal').classList.remove('hidden')" 
                class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + Создать группу
        </button>
    </div>

    @if($groups->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow">
            <p class="text-4xl mb-3">🌲</p>
            <p class="text-gray-500">У вас пока нет групповых чатов</p>
        </div>
    @else
        <div class="grid gap-3">
            @foreach($groups as $group)
            <a href="{{ route('forest.chat.group', $group->id) }}" 
               class="bg-white hover:bg-purple-50 rounded-xl shadow p-4 flex items-center justify-between transition group">
                <div>
                    <p class="font-semibold text-gray-800 group-hover:text-purple-700">
                        {{ $group->name ?? 'Групповой чат' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $group->users->count() }} участников • Создал: {{ $group->creator->name }}
                    </p>
                </div>
                <span class="text-purple-600">→</span>
            </a>
            @endforeach
        </div>
    @endif
</div>

<!-- Модальное окно создания группы -->
<div id="createGroupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">👥 Создать групповой чат</h2>
            <button onclick="document.getElementById('createGroupModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        {{-- Сообщения об ошибках --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('forest.chat.groups.create') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Название группы (необязательно)</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       placeholder="Например: Лесная тусовка 🌲"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Участники 
                    <span class="text-xs text-gray-500">(выберите друзей)</span>
                </label>
                
                @if($user->getFriendsList()->isEmpty())
                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-sm mb-2">😔 У вас пока нет друзей</p>
                        <a href="{{ route('forest.friends.search') }}" 
                           class="text-green-600 hover:underline text-sm">
                            Найти друзей →
                        </a>
                    </div>
                @else
                    <div class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                        @foreach($user->getFriendsList() as $friend)
                        <label class="flex items-center gap-3 p-3 hover:bg-purple-50 cursor-pointer border-b last:border-b-0 transition">
                            <input type="checkbox" 
                                   name="user_ids[]" 
                                   value="{{ $friend->id }}"
                                   class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-sm">
                                {{ $friend->animalType->icon ?? '🦊' }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $friend->name }}</p>
                                <p class="text-xs text-gray-500">{{ $friend->nickname ?? 'без никнейма' }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        ✅ Выберите хотя бы одного друга
                    </p>
                @endif
            </div>
            
            {{-- Счётчик выбранных --}}
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    Выбрано: <span id="selectedCount" class="font-bold text-purple-600">0</span>
                </p>
            </div>
            
            <div class="flex gap-3 justify-end">
                <button type="button" 
                        onclick="document.getElementById('createGroupModal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Отмена
                </button>
                <button type="submit" 
                        id="submitBtn"
                        disabled
                        class="bg-purple-500 hover:bg-purple-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg font-medium transition">
                    Создать группу
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Скрипт напрямую, без @push --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
    const selectedCount = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBtn');
    
    // Отладка в консоли
    console.log('🔍 Чекбоксов найдено:', checkboxes.length);
    console.log('🔍 selectedCount:', selectedCount);
    console.log('🔍 submitBtn:', submitBtn);
    
    if (!checkboxes.length || !selectedCount || !submitBtn) {
        console.error('❌ Не найдены элементы для скрипта!');
        return;
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const count = document.querySelectorAll('input[name="user_ids[]"]:checked').length;
            selectedCount.textContent = count;
            submitBtn.disabled = count === 0;
            console.log('✅ Выбрано:', count, '| Кнопка disabled:', submitBtn.disabled);
        });
    });
});
</script>
@endsection