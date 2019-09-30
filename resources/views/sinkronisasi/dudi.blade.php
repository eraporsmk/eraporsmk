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
					<th class="text-center">Nama</th>
					<th class="text-center">Bidang Usaha</th>
					<th class="text-center">Nomor MoU</th>
					<th class="text-center">Judul MoU</th>
					<th class="text-center">Status</th>
	            </tr>
            </thead>
			<tbody>
		<?php
		$no = ($currentPage - 1) * $per_page + 1;
		if($dapodik->total()){
			foreach($dapodik as $data){
				$data_dudi = array(
					'dudi_id_dapodik'	=> $data->dudi_id,
					'sekolah_id'		=> $data->sekolah_id,
					'nama'				=> $data->nama,
					'bidang_usaha_id'	=> $data->bidang_usaha_id,
					'nama_bidang_usaha'	=> $data->nama_bidang_usaha,
					'alamat_jalan'		=> $data->alamat_jalan,
					'rt'				=> $data->rt,
					'rw'				=> $data->rw,
					'nama_dusun'		=> $data->nama_dusun,
					'desa_kelurahan'	=> $data->desa_kelurahan,
					'kode_wilayah'		=> $data->kode_wilayah,
					'kode_pos'			=> $data->kode_pos,
					'lintang'			=> $data->lintang,
					'bujur'				=> $data->bujur,
					'nomor_telepon'		=> $data->nomor_telepon,
					'nomor_fax'			=> $data->nomor_fax,
					'email'				=> $data->email,
					'website'			=> $data->website,
					'npwp'				=> $data->npwp,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$find_dudi = App\Dudi::where('dudi_id_dapodik', '=', $data->dudi_id)->first();
				$find_mou = App\Mou::where('mou_id_dapodik', '=', $data->mou_id)->first();
				if($find_dudi){
					$dudi_id = $find_dudi->dudi_id;
					App\Dudi::find($find_dudi->dudi_id)->update($data_dudi);
					$status = 'update';
				} else {
					$create_dudi = App\Dudi::create($data_dudi);
					$dudi_id = $create_dudi->dudi_id;
					$status = 'insert';
				}
				$data_mou = array(
					'mou_id_dapodik'	=> $data->mou_id,
					'id_jns_ks'			=> $data->id_jns_ks,
					'dudi_id'			=> $dudi_id,
					'dudi_id_dapodik'	=> $data->dudi_id,
					'sekolah_id'		=> $data->sekolah_id,
					'nomor_mou'			=> $data->nomor_mou,
					'judul_mou'			=> $data->judul_mou,
					'tanggal_mulai'		=> $data->tanggal_mulai,
					'tanggal_selesai'	=> $data->tanggal_selesai,
					'nama_dudi'			=> $data->nama_dudi,
					'npwp_dudi'			=> $data->npwp_dudi,
					'nama_bidang_usaha'	=> $data->nama_bidang_usaha,
					'telp_kantor'		=> $data->telp_kantor,
					'fax'				=> $data->fax,
					'contact_person'	=> $data->contact_person,
					'telp_cp'			=> $data->telp_cp,
					'jabatan_cp'		=> $data->jabatan_cp,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				if($find_mou){
					App\Mou::find($find_mou->mou_id)->update($data_mou);
				} else {
					App\Mou::create($data_mou);
				}
		?>
			<tr>
				<td class="text-center"><?php echo $no; ?></td>
				<td><?php echo $data->nama_dudi; ?></td>
				<td><?php echo $data->nama_bidang_usaha; ?></td>
				<td><?php echo $data->nomor_mou; ?></td>
				<td><?php echo $data->judul_mou; ?></td>
				<td class="text-center"><?php echo $status; ?></td>
			</tr>
		<?php
			$no++;
			}
		} else {
		?>
				<tr>
					<td colspan="6" class="text-center">Tidak ada data untuk ditampilkan</td>
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