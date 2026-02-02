@extends('layouts.main')

@section('header-title')
Главная страница
@endsection

@section('content')
<div class="hero">
    <div class="hero-overlay">
        <h1>Добро пожаловать на сайт</h1>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Asperiores incidunt voluptate doloremque. Similique quae ipsam dignissimos ipsa quidem voluptates. Mollitia rerum quisquam suscipit officia delectus sit minima asperiores inventore consequatur!</p>
        <a href="#" class="hero-btn">Перейти</a>
    </div>
</div>

<div class="maincontainer">
    <div class="main-block">
        <h1>Home page</h1>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Asperiores incidunt voluptate doloremque. Similique quae ipsam dignissimos ipsa quidem voluptates. Mollitia rerum quisquam suscipit officia delectus sit minima asperiores inventore consequatur!</p>
    </div>

    @include('includes.aside')
</div>

@endsection