@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
	@role('administrator')
		<p>This is visible to users with the admin role. Gets translated to
		\Laratrust::hasRole('administrator')</p>
	@endrole
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th>name</th>
				<th>description</th>
				<th>aksi</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
@stop

@section('js')
<script type="text/javascript">
$(document).ready( function () {
	$('#datatable').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ url('users/list_permission_role') }}",
		"columns": [
            { "data": "name" },
            { "data": "description" },
			{ data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    } );
});
</script>
@Stop