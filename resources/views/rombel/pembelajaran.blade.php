@extends('layouts.modal')

@section('title'){{ $title }} @stop



@section('content')
	{{--dd($all_pembelajaran)--}}
	<form id="pembelajaran_post" method="post">
	@csrf
	<table class="table table-bordered table-hover" id="pembelajaran">
		<thead>
			<th class="text-center" width="5%">No</th>
			<th class="text-center" width="30%">Mata Pelajaran</th>
			<th class="text-center" width="20%">Guru Mata Pelajaran (Dapodik)</th>
			<th class="text-center" width="20%">Guru Pengajar</th>
			<th class="text-center" width="15%">Kelompok</th>
			<th class="text-center" width="10%">No. Urut</th>
		</thead>
		<tbody id="editable">
		<?php 
		$i=1;
		if($all_pembelajaran->pembelajaran){
			foreach($all_pembelajaran->pembelajaran as $pembelajaran){
			//dd($pembelajaran->pengajar);
			$guru_id = '';
			$nama_guru = '-';
			$guru_pengajar_id = '';
			$nama_pengajar = '-';
			if($pembelajaran->guru){
				$guru_id = $pembelajaran->guru->guru_id;
				$nama_guru = $pembelajaran->guru->nama;
			}
			if($pembelajaran->pengajar){
				$guru_pengajar_id = $pembelajaran->pengajar->guru_id;
				$nama_pengajar = $pembelajaran->pengajar->nama;
			}
		?>
		<tr>
			<td class="text-center">
				<?php echo $i; ?>
				<input type="hidden" name="pembelajaran_id" value="{{$pembelajaran->pembelajaran_id}}" />
			</td>
			<td>
				<a href="#" class="nama_mapel" data-type="text" data-value="{{$pembelajaran->nama_mata_pelajaran}}" data-name="{{$pembelajaran->pembelajaran_id}}" data-pk="{{$pembelajaran->mata_pelajaran_id}}" data-url="<?php echo url('rombel/tambah_alias'); ?>" data-title="Edit Nama Mapel" data-token="{{ Session::token() }}">{{$pembelajaran->nama_mata_pelajaran}} ({{$pembelajaran->mata_pelajaran_id}})</a>
			</td>
			<td>{{$nama_guru}}</td>
			<td><span style="display: none;">{{strtoupper($nama_pengajar)}}</span>
				<select class="select2 form-control" name="guru_pengajar_id" style="width:100%">
					<option value="" data-description="Kosongkan Guru Pengajar">== Pilih Pengajar ==</option>
					@foreach($all_pengajar as $pengajar)
					<option value="{{$pengajar->guru_id}}" title="{{$pengajar->nuptk}}"{!!($pengajar->guru_id == $pembelajaran->guru_pengajar_id) ? ' selected="selected"' : '' !!}>{{strtoupper($pengajar->nama)}}</option>
					@endforeach
				</select>
			</td>
			<td><span style="display: none;">{{$pembelajaran->kelompok_id}}</span>
				<select name="kelompok_id" class="form-control select2" style="width:100%">
					<option value="">== Pilih Kelompok ==</option>
					@foreach($all_kelompok as $kelompok)
					<option value="{{$kelompok->kelompok_id}}"{!!($kelompok->kelompok_id == $pembelajaran->kelompok_id) ? ' selected="selected"' : '' !!}>{{$kelompok->nama_kelompok}}</option>
					@endforeach
				</select>
			</td>
			<td class="text-center">
				<span style="display: none;">{{str_pad($pembelajaran->no_urut, 2, 0, STR_PAD_LEFT)}}</span>
				<input type="text" name="no_urut" value="{{$pembelajaran->no_urut}}" class="nomor_urut form-control" style="width: 100%" />
			</td>
		</tr>
		<?php 
		$i++;}
	} else { ?>
		<tr class="tr_a">
			<td colspan="6">Mata Pelajaran belum tersedia. Silahkan tambah mata pelajaran di menu referensi mata pelajaran</td>
		</tr>
	<?php } ?>
		</tbody>
	</table>
	</form>
@stop

@section('footer')
	<span class="text-left" style="float:left;">Kurikulum {{$all_pembelajaran->kurikulum->nama_kurikulum}} ({{$all_pembelajaran->kurikulum->kurikulum_id}})</span>
	<a class="btn btn-default btn-sm" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
	<a href="javascript:void(0)" class="btn btn-success btn-sm simpan_pembelajaran"><i class="fa fa-plus-circle"></i> Simpan</a>
@endsection
@section('js')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/bootstrap-editable/css/bootstrap-editable.css') }}">
<script src="{{ asset('vendor/adminlte/plugins/bootstrap-editable/js/jquery.mockjax.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/bootstrap-editable/js/bootstrap-editable.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/jquery-noty/packaged/jquery.noty.packaged.js') }}"></script>
<script type="text/javascript">
$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
$.fn.inputFilter = function(inputFilter) {
	return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
		if (inputFilter(this.value)) {
			this.oldValue = this.value;
			this.oldSelectionStart = this.selectionStart;
			this.oldSelectionEnd = this.selectionEnd;
		} else if (this.hasOwnProperty("oldValue")) {
			this.value = this.oldValue;
			this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
		} else {
			this.value = "";
		}
	});
};
function formatState (state) {
	if (!state.id) {
		return state.text;
	}
	var $state = $(
		'<span> ' + state.text + '<br /> '+ state.title +'</span>'
	);
  return $state;
};
$(function(){
	$('#pembelajaran').DataTable({
		"lengthChange": false,
		"searching": false,
		"paging": false,
		"info": false,
		"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]]
	});
	$.fn.editable.defaults.mode = 'inline';
	$.fn.editable.defaults.params = function (params) {
        params._token = $("input[name=_token]").val();
        return params;
    };
	$('tbody#editable tr td a.nama_mapel').editable({
		success: function(response, newValue) {
			$(this).prev().val(newValue);
		}
	});
	$('.select2').select2({
		templateResult: formatState,
		dropdownParent: $('#modal_content')
	});
	$(".nomor_urut").inputFilter(function(value) {
		return /^\d*$/.test(value);    // Allow digits only, using a RegExp
	});
	$('a.simpan_pembelajaran').click(function(){
		var data = $("form#pembelajaran_post").serializeObject();
		var result = $.parseJSON(JSON.stringify(data));
		console.log(result);
		var set_pengajar;
		$.each(result.pembelajaran_id, function (i, item) {
			$.ajax({
				url: '<?php echo url('rombel/simpan_pembelajaran/'); ?>',
				type: 'post',
				data: {pembelajaran_id:item, guru_pengajar_id:result.guru_pengajar_id[i], kelompok_id:result.kelompok_id[i], nomor_urut:result.no_urut[i],_token:result._token},
				success: function(view){
					noty({
						text        : view.text,
						type        : view.type,
						timeout		: 1500,
						dismissQueue: true,
						layout      : 'topLeft',
						animation: {
							open: {height: 'toggle'},
							close: {height: 'toggle'}, 
							easing: 'swing', 
							speed: 500 
						}
					});
				}
			});
		});
	});
});
</script>
@endsection
