@extends('app')

@section('content')
    @if ( $error !== null )
        <p>{{$error}}</p>
    @else
        @if (count($fixture) > 0)
            <h1>Fixture</h1>
            @foreach ($fixture as $phases)
                <h2>Phase {{ $phases['phase'] }}</h2>
                @foreach ($phases['matches'] as $match)
                    <h3>Match day {{ $match['day'] }}</h3>
                    @foreach ($match['events'] as $event)
                        <p>{{ $event['h']->team }} Vs. {{ $event['a']->team }}<p>
                    @endforeach
                @endforeach
            @endforeach
        @else
            <p>Fixture is empty</p>
        @endif
    @endif
    <div class="links">
        <a href="/">Go back</a>
    </div>
@endsection