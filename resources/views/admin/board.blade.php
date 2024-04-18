@extends(backpack_view('blank'))

@section('content')

<div class="jumbotron" id="app">
@if($board)
    <Board :board="{{ $board }}" />
@else
    <h5>Досок пока нет</h5>
@endif
</div>
@endsection