@extends('layouts.modal')
@section('title'){{ $title }} @stop
@section('content')
	{{--dd($all_pembelajaran)--}}
	<form id="catatan_post" method="post">
	@csrf
	<input type="hidden" name="anggota_rombel_id" value="{{$anggota_rombel_id}}">
	<textarea name="catatan" class="form-control" rows="10" placeholder="Tambah catatan disini....">{{($catatan) ? $catatan->catatan : ''}}</textarea>
	</form>
@stop
@section('footer')
	<a class="btn btn-default btn-sm" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
	<a href="javascript:void(0)" class="btn btn-success btn-sm simpan_catatan"><i class="fa fa-plus-circle"></i> Simpan</a>
@endsection
@section('js')
<script type="text/javascript">
	$('a.simpan_catatan').click(function(){
		$.ajax({
			url: '{{route('ajax.simpan_catatan_p5bk')}}',
			type: 'post',
			data: $("form#catatan_post").serialize(),
			success: function(data){
				console.log(data);
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(results => {
				window.location.replace('{{route('laporan.budaya_kerja')}}');
				});
			}
		});
	});
</script>
@endsection
