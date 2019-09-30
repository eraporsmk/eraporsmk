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
              <th>Name</th>
              <th>Display Name</th>
              <th>Description</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
		  	@if(count($permissions))
              @foreach($permissions as $row)
              <tr>
                <td>{{ $row->name }}</td>
                <td>{{ $row->display_name }}</td>
                <td>{{ $row->description }}</td>
                <td class="text-center">
                  <div class="btn-group">
                    <a class="btn btn-primary" href="{{ route('permission.edit', ['id' => $row->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil" title="Edit"></i> </a>
                    <a class="btn btn-danger" href="{{ route('permission.show', ['id' => $row->id]) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o" title="Delete"></i> </a>
                  </div>
                </td>
              </tr>
              @endforeach
			  @else
			  <tr>
			  	<td class="text-center" colspan="4">No data to display</td>
			  </tr>
			  @endif
          </tbody>
        </table>
        {{ $permissions->links() }}
      </div>

@endsection