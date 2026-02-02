@extends('layouts.main')

@section('header-title')
{{ $post->title }}
@endsection

@section('content')
<div class="main-container">
    <div class="main-block">
        <h1>{{ $post->title }}</h1>
        <p>{{ $post->text }}</p>
        <a href="{{ route('posts.one.edit', $post->id) }}">Редактировать</a>
        <a href="{{ route('posts.one.delete', $post->id) }}">Удалить</a>
    </div>

    @include('includes.aside')
</div>

@endsection