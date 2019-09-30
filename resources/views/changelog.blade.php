@extends('adminlte::page')

@section('title_postfix', 'Daftar Perubahan | ')

@section('content_header')
    <h1>Daftar Perubahan</h1>
@stop

@section('content')
    <?php
			$hits = '';
			$filename = "./Changelog.txt";
			if (file_exists($filename)) {
				$hits = file_get_contents($filename);
			} else {
				$hits = file_put_contents($filename, '<div id="form">
<legend><h3>Versi 2.0</h3></legend>
<ol>
<li><b style="color:blue">[Pembaruan]</b> Sinkronisasi dengan dapodik.</li>
<li><b style="color:blue">[Pembaruan]</b> Nama ketua ekstrakurikuler mengambil dari database siswa </li>
<li><b style="color:blue">[Pembaruan]</b> penambahan fitur remedial </li>
<li><b style="color:blue">[Pembaruan]</b> penambahan fitur updater via aplikasi</li>
<li><b style="color:blue">[Pembaruan]</b> Sinkronisasi dengan dapodik.</li>
<li><b style="color:green">[Perbaikan]</b> Perbaikan semua temuan bugs</li>
<li><b style="color:green">[Perbaikan]</b> Proses instalasi tidak sekali proses insert sql </li>
<li><b style="color:green">[Perbaikan]</b> Import guru </li>
<li><b style="color:green">[Perbaikan]</b> Import siswa</li>
<li><b style="color:green">[Perbaikan]</b> Import rombongan belajar </li>
<li><b style="color:green">[Perbaikan]</b> Referensi Teknik Penilaian </li>
<li><b style="color:green">[Perbaikan]</b> Referensi Sikap </li>
<li><b style="color:green">[Perbaikan]</b> Navigasi anggota rombel </li>
<li><b style="color:green">[Perbaikan]</b> validasi input nilai di atas 100 </li>
<li><b style="color:green">[Perbaikan]</b> bobot aktifitas penilaian keterampilan </li>
<li><b style="color:green">[Perbaikan]</b> nilai akhir raport dalam ratusan (jika masih dalam ratusan, lakukan update nilai terlebih dahulu di menu entry nilai) </li>
<li><b style="color:green">[Perbaikan]</b> hasil download legger keterampilan dalam ratusan (jika masih dalam ratusan, lakukan update nilai dulu di menu entry nilai keterampilan) </li>
</ol>
</div>');
			}
			echo '<div class="last_update">'. date ("d-m-Y H:i:s", filemtime($filename)).'</div>';
			echo $hits;
			?>
@stop