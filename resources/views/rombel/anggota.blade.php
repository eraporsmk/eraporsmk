@extends('layouts.modal')

@section('title'){{ $title }} @stop



@section('content')
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 3%" class="text-center">No.</th>
				<th style="width: 30%" class="text-center">Nama</th>
				<th style="width: 10%" class="text-center">NISN</th>
				<th style="width: 3%" class="text-center">L/P</th>
				<th style="width: 25%" class="text-center">Tempat, Tanggal Lahir</th>
				<th style="width: 10%" class="text-center">Agama</th>
				@if($all_anggota->jenis_rombel == 51)
				<th style="width: 10%" class="text-center">Kelas Reguler</th>
				@endif
				<th style="width: 10%" class="text-center">Aksi</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		if($all_anggota){
			$i=1;
			foreach($all_anggota->anggota_rombel as $anggota){
				//dd($anggota->siswa);
				if($anggota->siswa){
				$siswa = $anggota->siswa;
		?>
			<tr>
				<td class="text-center"><?php echo $i; ?></td>
				<td><?php echo strtoupper($siswa->nama); ?></td>
				<td class="text-center"><?php echo $siswa->nisn; ?></td>
				<td><?php echo $siswa->jenis_kelamin; ?></td>
				<td><?php echo strtoupper($siswa->tempat_lahir).', '.CustomHelper::TanggalIndo($siswa->tanggal_lahir); ?></td>
				<td class="text-center"><?php echo $siswa->agama->nama; ?></td>
				@if($all_anggota->jenis_rombel == 51)
				<td class="text-center">{{($siswa->kelas) ? $siswa->kelas->nama : '-'}}</td>
				@endif
				<td class="text-center"><a class="btn btn-sm btn-danger confirm" href="<?php echo url('rombel/keluarkan/'.$anggota->anggota_rombel_id); ?>">Keluarkan</a></td>
			</tr>
		<?php
					$i++;
				}
			}
		} else {
		?>
			<tr>
				<td colspan="6">Anggota rombel tidak ditemukan</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
@stop

@section('footer')
    <a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection

@section('js')
<script type="text/javascript">
$('a.confirm').bind('click',function(e) {
	var ini = $(this).parents('tr');
	e.preventDefault();
	var url = $(this).attr('href');
	swal({
		title: "Anda Yakin?",
		text: "Tindakan ini tidak bisa dikembalikan!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		closeOnClickOutside: false,
	}).then((willDelete) => {
		if (willDelete) {
			$.get(url).done(function(response) {
				var data = $.parseJSON(response);
				ini.remove();
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false});
			});
		}
	});
});
</script>
@endsection
