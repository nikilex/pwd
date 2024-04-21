@extends(backpack_view('blank'))

@section('content')

<div class="jumbotron pb-5" id="app">
@if($board)
    <Board :board="{{ $board }}" />
@else
    <h5>Досок пока нет</h5>
@endif
</div>
@endsection