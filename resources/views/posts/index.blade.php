@extends('layouts.main')

@section('header-title')
Все посты сайта
@endsection

@section('content')
<div class="main-container">
    <div class="main-block">
        <h1>Все посты сайта</h1>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Asperiores incidunt voluptate doloremque. Similique quae ipsam dignissimos ipsa quidem voluptates. Mollitia rerum quisquam suscipit officia delectus sit minima asperiores inventore consequatur!</p>
    </div>

    @include('includes.aside')
</div>

<div class="posts">
    @foreach($posts as $el)
        <div class="post">
            <h2>{{ $el->title }}</h2>
            <p>{{ $el->anons }}</p>
            <p><a href="{{ route('posts.one', $el->id) }}">Детальнее</a></p>
        </div>
    @endforeach
</div>

@endsection