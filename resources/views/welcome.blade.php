@extends('app')

@section('content')
<div style="margin-top: 15%">
    <div class="top-right links">
        <a href="https://github.com/nbulian/create-fixture">GitHub repositry</a>
        |
        <a href="https://linkedin.com/in/nbulian">Nahuel Bulian</a>
    </div>
    <div class="title m-b-md">
        Selectra
    </div>
    <h1>NBA TEST</h1>
    <h2>Using Laravel 6.</h2>

    <div class="links">
        <a href="{{ route('teams.index') }}">Part 1</a>
        <a href="{{ route('fixture.index') }}">Part 2</a>
    </div>
</div>
@endsection