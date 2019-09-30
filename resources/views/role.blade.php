@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
	@role('admin')
		<p>This is visible to users with the admin role. Gets translated to
		\Laratrust::hasRole('administrator')</p>
	@endrole
	{{-- menampilkan error validasi --}}
	@if (count($errors) > 0)
		<div class="alert alert-danger">
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	@endif
	<form action="/proses" method="post">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="name">Name</label>
			<input class="form-control" type="text" name="name" value="{{ old('name') }}">
		</div>
		<div class="form-group">
			<label for="description">Description</label>
			<input class="form-control" type="text" name="description" value="{{ old('description') }}">
		</div>
		<div class="form-group">
			<input class="btn btn-primary" type="submit" value="Proses">
		</div>
	</form>
    <p>You are logged in!</p>
@stop