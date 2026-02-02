@extends('layouts.main')

@section('header-title')
Страница контакты
@endsection

@section('content')
@if($errors->any())
    <div class="block-error">
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="main-container">
    <div class="main-block">
        <h1>Contact page</h1>
        <form action="{{ route('contact.post') }}" method="POST">
            @csrf

            <label for="name">Имя</label>
            <input type="text" placeholder="Введите имя" name="name" id="name" value="{{ old('name') }}">

            <label for="email">Email</label>
            <input type="email" placeholder="Введите email" name="email" id="email" value="{{ old('email') }}">

            <label for="subject">Тема сообщения</label>
            <input type="text" placeholder="Введите тему" name="subject" id="subject" value="{{ old('subject') }}">

            <label for="message">Сообщение</label>
            <textarea name="message" id="message" placeholder="Введите сообщение">{{ old('message') }}</textarea>

            <button type="submit">Отправить</button>
        </form>
    </div>

    @include('includes.aside')
</div>
@endsection