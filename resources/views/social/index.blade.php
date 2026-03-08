@extends('layouts.main')

@section('header-title')
Социальная сеть волшебного леса
@endsection

@section('content')

<!-- Заголовок -->
<div class="text-center mb-8">
    <h1 class="text-4xl font-bold mb-2">
        🦉 Социальная сеть для жителей волшебного леса
    </h1>
    <p>
        Место встречи жителей леса: сов, зайцев, волков и мышей
    </p>
</div>

<!-- Основные разделы -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- Блок 1: Регистрация -->
    <div class="border border-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-2">Регистрация 📝</h2>
        <p class="mb-4">
            Создайте аккаунт, чтобы заводить друзей и общаться
        </p>
        <a href="{{ route('register') }}" class="block text-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition">
            Зарегистрироваться
        </a>
    </div>

    <!-- Блок 2: Войти -->
    <div class="border border-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-2">Вход 🔐</h2>
        <p class="mb-4">
            Уже есть аккаунт? Войдите в систему
        </p>
        <a href="{{ route('login') }}" class="block text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition">
            Войти
        </a>
    </div>

    <!-- Блок 3: О проекте -->
    <div class="border border-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-2">О проекте 🌲</h2>
        <p class="mb-4">
            Узнайте больше о возможностях сети
        </p>
        <a href="#about" class="block text-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition">
            Подробнее
        </a>
    </div>
</div>

<!-- Описание возможностей -->
<div id="about" class="mt-12 border border-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold mb-4">Возможности сети</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-start">
            <div>
                <h3 class="font-semibold">Заводите друзей 🦊</h3>
                <p class="text-sm">Находите друзей по рекомендациям системы</p>
            </div>
        </div>
        <div class="flex items-start">
            <div>
                <h3 class="font-semibold">Общение в чате 💬</h3>
                <p class="text-sm">Обменивайтесь сообщениями с друзьями</p>
            </div>
        </div>
        <div class="flex items-start">
            <div>
                <h3 class="font-semibold">Умные рекомендации 🎯</h3>
                <p class="text-sm">Система подберёт лучших друзей для вас</p>
            </div>
        </div>
        <div class="flex items-start">
            <div>
                <h3 class="font-semibold">4 вида животных 🦉</h3>
                <p class="text-sm">Сова, Заяц, Волк, Мышь</p>
            </div>
        </div>
    </div>
</div>

@endsection