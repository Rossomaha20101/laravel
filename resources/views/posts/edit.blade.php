@extends('layouts.main')

@section('header-title')
Редактировать пост
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
        <h1>Редактировать пост</h1>
        <form action="{{ route('posts.edit', $post->id) }}" method="POST">
            @csrf

            <label for="title">Название</label>
            <input type="text" placeholder="Введите название" name="title" id="title" value="{{ $post->title }}">

            <label for="anons">Анонс</label>
            <input type="text" placeholder="Введите анонс" name="anons" id="anons" value="{{ $post->anons }}">

            <label for="text">Текст</label>
            <textarea name="text" id="text" placeholder="Введите текст">{{ $post->text }}</textarea>

            <button type="submit">Редактировать</button>
        </form>
    </div>

    @include('includes.aside')
</div>
@endsection