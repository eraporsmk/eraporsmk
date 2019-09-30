@extends('layouts.modal')

@section('title'){{ $title }} @stop



@section('content')
	{{--dd($all_pembelajaran)--}}
	<form id="pembelajaran" method="post">
	<input type="hidden" name="rombongan_belajar_id" value="{{$all_pembelajaran->rombongan_belajar_id}}" />
	<input type="hidden" name="kurikulum_id" id="kurikulum_id" value="{{$all_pembelajaran->kurikulum_id}}" />
	<table class="table table-bordered table-hover" id="pembelajaran">
		<thead>
			<th class="text-center" width="5%">No</th>
			<th class="text-center" width="30%">Mata Pelajaran</th>
			<th class="text-center" width="20%">Guru Mata Pelajaran (Dapodik)</th>
			<th class="text-center" width="20%">Guru Pengajar</th>
			<th class="text-center" width="15%">Kelompok</th>
			<th class="text-center" width="15%">No. Urut</th>
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
			<td><div class="text-center"><?php echo $i; ?></div></td>
			<td>
				<input type="hidden" class="token" name="token" value="{{ Session::token() }}" />
				<input type="hidden" class="nama_mapel_alias" name="nama_mapel_alias" value="{{$pembelajaran->nama_mata_pelajaran}}" />
				<input type="hidden" class="pembelajaran_id" name="pembelajaran_id" value="{{$pembelajaran->pembelajaran_id}}" />
				<a href="#" class="nama_mapel" data-type="text" data-value="{{$pembelajaran->nama_mata_pelajaran}}" data-name="{{$pembelajaran->pembelajaran_id}}" data-pk="{{$pembelajaran->mata_pelajaran_id}}" data-url="<?php echo url('rombel/tambah_alias'); ?>" data-title="Edit Nama Mapel" data-token="{{ Session::token() }}">{{$pembelajaran->nama_mata_pelajaran}} ({{$pembelajaran->mata_pelajaran_id}})</a>
				<input type="hidden" name="mapel" id="mapel" value="{{$pembelajaran->mata_pelajaran_id}}" class="form-control" />
 			</td>
			<td>
				<input type="hidden" class="guru" name="guru" value="{{$guru_pengajar_id}}" />
				{{$nama_guru}}
			</td>
			<td>
				<input type="hidden" class="pengajar" name="pengajar" value="{{$guru_pengajar_id}}" />
				<a class="pengajar" href="javascript:void(0)" id="guru_pengajar_id" data-type="select2" data-name="pengajar_id" data-value="{{$guru_pengajar_id}}" title="Pilih Guru Pengajar">{{($guru_pengajar_id) ? $nama_pengajar : 'Pilih Guru Pengajar'}}</a>
			</td>
			<td>
				<input type="hidden" class="kelompok_id" name="kelompok_id" value="{{$pembelajaran->kelompok_id}}" />
				<a class="kelompok_id" href="javascript:void(0)" id="kelompok_id" data-type="select2" data-name="kelompok_id" data-value="{{$pembelajaran->kelompok_id}}" title="Pilih Kelompok"></a>
			</td>
			<td>
				<input type="number" class="form-control input-sm" name="nomor_urut" value="{{$pembelajaran->no_urut}}" />
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
$(function(){
	$.fn.editable.defaults.mode = 'inline';
	$.fn.editable.defaults.params = function (params) {
        params._token = $("input[name=_token]").val();
        return params;
    };
	$.fn.editable.defaults.url = '<?php echo url('rombel/tambah_alias'); ?>';
	$.get('<?php echo url('rombel/pengajar/'); ?>', function( response ) {
		var data = $.parseJSON(response);
		var guru = [];
		$.each(data, function(i, item) {
        	guru.push({id: item.id, text: item.text});
    	});
		$('tbody#editable tr td a.nama_mapel').editable({
			success: function(response, newValue) {
				$(this).prev().val(newValue);
			}
		});
		$('tbody#editable tr td a.pengajar').editable({
	        source: guru,
			emptytext : 'Pilih Guru Pengajar',
    	    select2: {
				dropdownAutoWidth : true,
        	    width: 300,
            	placeholder: '== Pilih Guru Pengajar ==',
	            allowClear: true
    	    },
		    success: function(response, newValue) {
				$(this).prev().val(newValue);
    		}
	    });   
	});
	$.get('<?php echo url('rombel/kelompok/'.$all_pembelajaran->kurikulum_id); ?>', function( response ) {
		var data = $.parseJSON(response);
		var kelompok = [];
		$.each(data, function(i, item) {
        	kelompok.push({id: item.id, text: item.text});
    	});
		$('tbody#editable tr td a.kelompok_id').editable({
	        source: kelompok,
			emptytext : 'Pilih Kelompok',
    	    select2: {
				dropdownAutoWidth : true,
        	    //width: 300,
            	placeholder: '== Pilih Kelompok ==',
	            allowClear: true
    	    },
		    success: function(response, newValue) {
				$(this).prev().val(newValue);
    		}
	    });   
	});
	$('a.simpan_pembelajaran').click(function(){
		var data = $("form#pembelajaran").serializeObject();
		var result = $.parseJSON(JSON.stringify(data));
		console.log(result);
		var array_guru = Array.isArray(result.guru);
		if(!array_guru){
			$.ajax({
				url: '<?php echo url('rombel/simpan_pembelajaran/'); ?>',
				type: 'post',
				data: {keahlian_id:result.keahlian_id, rombel_id:result.rombel_id, query:result.query, guru_id:result.guru, guru_pengajar_id:result.pengajar,mapel_id:result.mapel, kelompok_id:result.kelompok_id, nomor_urut:result.nomor_urut, nama_mapel_alias:result.nama_mapel_alias,_token:result.token,pembelajaran_id:result.pembelajaran_id},
				success: function(response){
					var view = $.parseJSON(response);
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
		} else {
			$.each(result.guru, function (i, item) {
				$.ajax({
					url: '<?php echo url('rombel/simpan_pembelajaran/'); ?>',
					type: 'post',
					data: {keahlian_id:result.keahlian_id, rombel_id:result.rombel_id, query:result.query, guru_id:item, guru_pengajar_id:result.pengajar[i], mapel_id:result.mapel[i], kelompok_id:result.kelompok_id[i], nomor_urut:result.nomor_urut[i], nama_mapel_alias:result.nama_mapel_alias[i],_token:result.token[i],pembelajaran_id:result.pembelajaran_id[i]},
					success: function(response){
						var view = $.parseJSON(response);
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
		}
	});
});
</script>
@endsection
