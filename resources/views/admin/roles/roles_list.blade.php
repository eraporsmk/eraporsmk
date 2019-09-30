@extends('adminlte::page')

@section('content_header')
    <h1>{{$title}}</h1>
@stop
@section('content_header_right')
    <?php echo $content_header_right; ?>
@stop
@section('content')
<div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Role Display</th>
              <th>Role Description</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
              @foreach($roles as $role)
              <tr>
                <td>{{ $role->display_name }}</td>
                <td>{{ $role->description }}</td>
                <td>{{ $role->name }}</td>
                <td>
                  <div class="btn-group">
                    <a class="btn btn-primary" href="{{ route('roles.edit', ['id' => $role->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil" title="Edit"></i> </a>
                    <a class="btn btn-danger" href="{{ route('roles.show', ['id' => $role->id]) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o" title="Delete"></i> </a>
                  </div>
                </td>
              </tr>
              @endforeach
          </tbody>
        </table>
        {{ $roles->links() }}
      </div>
@endsection