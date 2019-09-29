@extends('app')

@section('content')
    @if (count($teams) > 0)
        <h1>Team list</h1>
        @foreach ($teams as $team)
            <h2>{{ $team->name }}</h2>
            @if ( count($team->image) > 0)
                @foreach ($team->image as $i)
                    <img src="{{asset($i->source)}}" alt="{{$team->name}}">
                @endforeach
            @else
                <p>No image for this team.</p>
            @endif
        @endforeach
    @else
        <p>There aren't teams availables.</p>
    @endif
    <div class="links">
        <a href="/">Go back</a>
    </div>
@endsection