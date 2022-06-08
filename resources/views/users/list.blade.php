@extends('adminlte::page')

@section('title', 'eRaporSMK')

@section('content_header')
    <h1>Data Pengguna</h1>
@stop
@section('content_header_right')
    <a href="javascript:void(0)" class="btn btn-danger pull-right atur_pengguna">Atur Ulang Pengguna</a>
@stop
@section('content')
<style>
.swal-footer{text-align:center !important;}
</style>
	@if ($message = Session::get('success'))
	<div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<strong>Sukses!</strong> {!! $message !!}
	</div>
	@endif
	<div class="row" style="margin-bottom:10px;">
		<div class="col-md-6">
			<select id="filter_akses" class="form-control select2" style="width:100%;">
				<option value="">==Filter Berdasar Hak Akses==</option>
				@foreach($roles as $role){
				<option value="{{$role->id}}">{{$role->display_name}}</option>
				@endforeach
			</select>
		</div>
	</div>
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th>Nama</th>
				<th>Email</th>
				<th>Jenis Pengguna</th>
				<th>Terakhir Login</th>
				<th class="text-center">Status Sandi</th>
				<th class="text-center">Tindakan</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
@stop

@section('js')
@include('sweet::alert')
<script type="text/javascript">
function turn_on_icheck(){
	$('a.confirm').bind('click',function(e) {
		var ini = $(this).parents('tr');
		e.preventDefault();
		var url = $(this).attr('href');
		swal({
			title: "Apakah Anda yakin?",
			text: "Tindakan ini tidak dapat dikembalikan!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((willDelete) => {
			if (willDelete) {
				$.get(url).done(function(response) {
					var data = $.parseJSON(response);
					swal(data.text, {icon: data.icon,}).then((result) => {
						window.location.replace('{{url('users')}}');
					});
				});
			}
		});
	});
}
$(document).ready( function () {
	var oTable = $('#datatable').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
			"url": "{{ url('users/list_user') }}",
			"data": function (d) {
				var filter_akses = $('#filter_akses').val();
				if(filter_akses){
					d.filter_akses = filter_akses;
				}
			}
		},
		"columns": [
			{ "data": "name", "name": 'users.name' },
            { "data": "email", "name": 'users.email' },
			{ "data": "jenis_pengguna" },
			{ "data": "last_login", "orderable": false, "searchable": false },
            { "data": "hashedPassword", "orderable": false, "searchable": false },
            { "data": "actions", "orderable": false, "searchable": false},
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    } );
	$('.atur_pengguna').click(function(e){
		e.preventDefault();
		swal({
			title: "Apakah Anda yakin?",
			text: "Tindakan ini tidak dapat dikembalikan!",
			buttons: {
				cancel: "Batal",
				catch: {
					text: "Akun PTK",
					value: "ptk",
				},
				pd: 'Akun PD',
				closeModal: false,
			},
		}).then((value) => {
			if(value){
				$("#spinner").show();
				return fetch('{{url('users/generate')}}/'+value).then(response => {
					return response.json()
				}).catch(error => {
					swal("Error!", `Request failed: ${error}`, "error");
				})
			}
		}).then(response => {
			if(response){
				$("#spinner").hide();
				swal(response.title, response.text, response.icon).then(function(){
					swal.stopLoading();
					swal.close();
					oTable.draw();
				});
			}
		});
	})
	$('.select2').select2();
	$('#filter_akses').change(function(e){
        oTable.draw();
        e.preventDefault();
    });
});
</script>
@Stop