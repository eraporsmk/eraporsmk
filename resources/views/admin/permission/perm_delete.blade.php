@extends('adminlte::page')

@section('content_header')
    <h1>{{$title}}</h1>
@stop
@section('content_header_right')
    <?php echo $content_header_right; ?>
@stop

@section('content')
	
	<p>Are you sure you want to delete
        <strong>{{$permission->name}}</strong>
    </p>

    <form method="POST" action="{{ route('permission.destroy', ['id' => $permission->id]) }}">
        <input type="hidden" name="_token" value="{{ Session::token() }}">
        <input name="_method" type="hidden" value="DELETE">
        <button type="submit" class="btn btn-danger">Yes I'm sure. Delete</button>
    </form>
	
@endsection