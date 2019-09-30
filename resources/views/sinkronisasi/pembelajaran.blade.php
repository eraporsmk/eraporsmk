@extends('adminlte::page')

@section('content_header')
    <h1>Sinkonisasi Dapodik</h1>
@stop

@section('content')
	@role('superadmin')
		<p>This is visible to users with the admin role. Gets translated to
		\Laratrust::hasRole('superadmin')</p>
	@endrole
	@if ($message = Session::get('success'))
      <div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Sukses!</strong> {{ $message }}
      </div>
    @endif

    @if ($message = Session::get('error'))
      <div class="alert alert-danger alert-block alert-dismissable"><i class="fa fa-ban"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Error!</strong> {{ $message }}
      </div>
    @endif
		<table class="table table-bordered table-striped table-hover">
            <thead>
				<tr>
					<th class="text-center" style="vertical-align: middle;">No</th>
					<th class="text-center">mata_pelajaran</th>
					<th class="text-center">rombongan_belajar</th>
					<th class="text-center">guru_mata_pelajaran</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				if($dapodik->total()){
					foreach($dapodik as $data){
						$rombongan_belajar = App\Rombongan_belajar::where('rombel_id_dapodik', '=', $data->rombongan_belajar_id)->first();
						$get_guru = App\Guru::where('guru_id_dapodik', '=', $data->ptk_id)->first();
						$mata_pelajaran = App\Mata_pelajaran::where('mata_pelajaran_id', '=', $data->mata_pelajaran_id)->first();
						$mata_pelajaran_id = $data->mata_pelajaran_id;
						$insert_pembelajaran = array(
							'sekolah_id'				=> $sekolah->sekolah_id,
							'semester_id'				=> $semester->semester_id,
							'rombongan_belajar_id'		=> $rombongan_belajar->rombongan_belajar_id,
							'guru_id'					=> $get_guru->guru_id,
							'mata_pelajaran_id'			=> $mata_pelajaran_id,
							'nama_mata_pelajaran'		=> $data->nama_mata_pelajaran,
							'kkm'						=> 0,
							'is_dapodik'				=> 1,
							'pembelajaran_id_dapodik'	=> $data->pembelajaran_id,
							'last_sync'					=> date('Y-m-d H:i:s'),
						);
						$find_pembelajaran = App\Pembelajaran::where('pembelajaran_id_dapodik', '=', $data->pembelajaran_id)->first();
						if($find_pembelajaran){
							App\Pembelajaran::find($find_pembelajaran->pembelajaran_id)->update($insert_pembelajaran);
							$result = 'update';
						} else {
							$id_insert_pembelajaran = App\Pembelajaran::create($insert_pembelajaran);
							$result = 'insert';
						}
			?>
					<tr>
						<td class="text-center"><?php echo $no++; ?></td>
						<td><?php echo ($mata_pelajaran) ? $mata_pelajaran->nama.' ('.$mata_pelajaran_id.')' : '-'; ?></td>
						<td><?php echo ($rombongan_belajar) ? $rombongan_belajar->nama : '-'; ?></td>
						<td><?php echo ($get_guru) ? $get_guru->nama : '-'; ?></td>
						<td><?php echo $result; ?></td>
					</tr>
			<?php
				}
			//break; 
			} else { ?>
				<tr>
					<td class="text-center" colspan="5">Tidak ada data untuk ditampilkan</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<div class="text-center">{{ $dapodik->links() }}</div>
@Stop
@section('js')
<script>
$(document).ready(function(){
	$('body').mouseover(function(){
		$(this).css({cursor: 'progress'});
	});
	var cari = $('body').find('a[rel=next]');
	if(cari.length>0){
		var url = $(cari).attr('href');
		console.log(url);
		window.location.replace(url);
	} else {
		window.location.replace('<?php echo url('sinkronisasi/ambil-data'); ?>');
	}
})
</script>
@Stop