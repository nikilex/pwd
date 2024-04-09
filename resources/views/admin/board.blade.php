@extends(backpack_view('blank'))

@section('content')

<div class="jumbotron" id="app">
@if($board)
    <h1 class="mb-4">{{ $board->title }}</h1>

    <Board board-id="{{ $board->id }}" />
@else
    <h5>Досок пока нет</h5>
@endif
</div>
@endsection