@extends(backpack_view('blank'))

@section('content')
<div class="jumbotron" id="app">
    <h1 class="mb-4">{{ $board->title }}</h1>

    <Board board-id="{{ $board->id }}" />

</div>
@endsection