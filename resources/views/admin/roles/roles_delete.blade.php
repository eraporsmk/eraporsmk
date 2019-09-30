@extends('layouts.adminpages') 
@section('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Roles</h1>
    </div>
    <a class="btn btn-sm btn-primary" href="{{route('roles.index')}}">Back</a>
    <h2>{{$title}}</h2>
    <div class="clearfix"></div>
    <p>Are you sure you want to delete
        <strong>{{$role->name}}</strong>
    </p>

    <form method="POST" action="{{ route('roles.destroy', ['id' => $role->id]) }}">
        <input type="hidden" name="_token" value="{{ Session::token() }}">
        <input name="_method" type="hidden" value="DELETE">
        <button type="submit" class="btn btn-danger">Yes I'm sure. Delete</button>
    </form>
</main>
</div>
</div>

@endsection