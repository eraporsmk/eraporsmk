<style>
body{font-size:11px !important;}
</style>
<table width="100%">
  <tr>
    <td style="width: 20%;padding-top:5px; padding-bottom:5px;">Nama Siswa</td>
    <td style="width: 1%;" class="text-center">:</td>
    <td style="width: 80%"><?php echo $siswa->nama; ?></td>
  </tr>
  <tr>
	<td>NISN/NISN</td>
    <td class="text-center">:</td>
    <td><?php echo $siswa->no_induk.' / '.$siswa->nisn; ?></td>
  </tr>
  <tr>
	<td>Tahun Pelajaran</td>
    <td class="text-center">:</td>
    <td><?php echo str_replace('/','-',$data_semester->nama); ?></td>
  </tr>
  <tr>
	<td><?php if($rombongan_belajar->tingkat == 10){ ?>Program Keahlian<?php } else { ?>Kompetensi Keahlian<?php } ?></td>
    <td class="text-center">:</td>
    <td><?php echo $rombongan_belajar->jurusan->nama_jurusan; ?></td>
  </tr>
  <tr>
	<td>Rombel</td>
    <td class="text-center">:</td>
    <td><?php echo $rombongan_belajar->nama; ?></td>
  </tr>
</table><br />
<div class="strong" align="center">DAFTAR NILAI<br />UJIAN TENGAH SEMESTER</div>
<p>&nbsp;</p>
<table border="1" class="table">
    <thead>
  <tr>
    <th style="vertical-align:middle;width: 2px;" align="center" rowspan="2">No</th>
    <th style="vertical-align:middle;width: 300px;" rowspan="2" align="center" class="text-center">Mata Pelajaran</th>
    <th rowspan="2" align="center" style="width:10px;" class="text-center">SKM</th>
    <th colspan="2" align="center" class="width:150px;text-center">Nilai</th>
	<th rowspan="2" align="center" style="width:200px;" class="text-center">Keterangan</th>
  </tr>
  <tr>
    <th align="center" style="width:40px;" class="text-center">Angka</th>
    <th align="center" style="width:150px;" class="text-center">Huruf</th>
  </tr>
    </thead>
    <tbody>
	<?php $i=1;?>
	@foreach($all_nilai as $kelompok => $nilai_kelompok)
		<tr>
			<td colspan="6" class="strong">{{$kelompok}}</td>
		</tr>
		@foreach($nilai_kelompok[$siswa->peserta_didik_id] as $nilai)
		@if($nilai)
		<tr>
			<td class="text-center">{{$i++}}</td>
			<td>{{$nilai['nama_mata_pelajaran']}}</td>
			<td class="text-center">{{$nilai['kkm']}}</td>
			<td class="text-center">{{$nilai['angka']}}</td>
			<td class="text-center">{{$nilai['terbilang']}}</td>
			<td></td>
		</tr>
		@endif
		@endforeach
	@endforeach
	</tbody>
</table>
<br>
<div class="strong">CATATAN WALI KELAS (untuk perhatian Orang Tua/Wali)</div>
<table width="100%" border="1">
  <tr>
    <td style="padding:10px 10px 60px 10px;">{{($anggota_rombel->catatan_wali) ? $anggota_rombel->catatan_wali->uraian_deskripsi : ''}}</td>
  </tr>
</table>
<br>
<table width="100%">
  <tr>
    <td style="width:40%;">
		<p>Mengetahui,<br>Kepala Sekolah</p>
	<br>
<br>
<br>
<br>
<p><u>{{ CustomHelper::nama_guru($sekolah->guru->gelar_depan, $sekolah->guru->nama, $sekolah->guru->gelar_belakang) }}</u><br>
NIP. {{$sekolah->guru->nip}}
</p>
	</td>
	<td style="width:20%"></td>
    <td style="width:40%;"><p>{{$sekolah->kabupaten}}, {{CustomHelper::TanggalIndo($tanggal_rapor)}}<br>Wali Kelas</p><br>
<br>
<br>
<br>
<p>
<u>{{CustomHelper::nama_guru($rombongan_belajar->wali->gelar_depan, $rombongan_belajar->wali->nama, $rombongan_belajar->wali->gelar_belakang)}}</u><br>
NIP. {{$rombongan_belajar->wali->nip}}
</td>
  </tr>
</table>