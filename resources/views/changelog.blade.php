@extends('adminlte::page')

@section('title_postfix', 'Daftar Perubahan | ')

@section('content_header')
    <h1>Daftar Perubahan</h1>
@stop

@section('content')
    <?php
			$hits = '';
			$filename = public_path('Changelog.txt');//"./Changelog.txt";
			if (file_exists($filename)) {
				$hits = file_get_contents($filename);
			} else {
				$hits = file_put_contents($filename, '<div id="form">
<legend><h3>Versi 5.0.4</h3></legend>
<ol>
<li><b style="color:green">[Perbaikan]</b> Ordering nama peserta didik di rekap nilai</li>
<li><b style="color:green">[Perbaikan]</b> Edit perencanaan</li>
<li><b style="color:green">[Perbaikan]</b> Penilaian remedial</li>
<li><b style="color:green">[Perbaikan]</b> Hitung ulang nilai remedial (jika ada) pada aksi Generate Nilai</li>
<li><b style="color:green">[Perbaikan]</b> Login menggunakan NUTPK/NISN</li>
</ol>
<legend><h3>Versi 5.0.3</h3></legend>
<ol>
<li><b style="color:blue">[Pembaharuan]</b> Pengiriman data eRapor SMK ke server Direktorat Pembinaan SMK</li>
<li><b style="color:green">[Perbaikan]</b> Tampilan nilai di laman Pratinjau Nilai</li>
<li><b style="color:green">[Perbaikan]</b> Tampilan nilai di laman Rapor Semester</li>
<li><b style="color:green">[Perbaikan]</b> Cetak Halaman Depan Rapor</li>
</ol>
<legend><h3>Versi 5.0.2</h3></legend>
<ol>
<li><b style="color:blue">[Pembaharuan]</b> Sinkronisasi referensi wilayah</li>
<li><b style="color:blue">[Pembaharuan]</b> Sinkronisasi referensi kompetensi dasar</li>
<li><b style="color:blue">[Pembaharuan]</b> Sinkronisasi data Diterima dikelas untuk peserta didik di menu detil peserta didik</li>
<li><b style="color:blue">[Pembaharuan]</b> Pilihan periode aktif pada laman login</li>
<li><b style="color:blue">[Pembaharuan]</b> Registrasi menggunakan password baru dapodik</li>
<li><b style="color:blue">[Pembaharuan]</b> Penambahan akses Referensi Rombel di Login Waka Kurikulum</li>
<li><b style="color:blue">[Pembaharuan]</b> Penambahan tombol Simpan Data Peserta Didik di Login Wali Kelas dan Waka Kurikulum</li>
<li><b style="color:green">[Perbaikan]</b> Duplikasi rencana penilaian keterampilan</li>
<li><b style="color:green">[Perbaikan]</b> Mapping pembelajaran</li>
<li><b style="color:green">[Perbaikan]</b> Simpan asesor dan instruktur</li>
<li><b style="color:green">[Perbaikan]</b> Urutan mata pelajaran di cetak rapor</li>
<li><b style="color:green">[Perbaikan]</b> Status kenaikan kelas &amp; kelulusan di cetak rapor</li>
</ol>
<legend><h3>Versi 5.0.1</h3></legend>
<ol>
<li><b style="color:blue">[Pembaharuan]</b> Menghapus menu migrasi e-Rapor versi 4.x ke versi 5.x</li>
<li><b style="color:blue">[Pembaharuan]</b> Duplikasi rencana penilaian</li>
<li><b style="color:blue">[Pembaharuan]</b> Guru BK dapat melihat semua penilaian sikap yang di input oleh guru lain</li>
<li><b style="color:blue">[Pembaharuan]</b> Tombol hapus data ganda pada Referensi Kompetensi Dasar</li>
<li><b style="color:green">[Perbaikan]</b> Sinkronisasi sekolah</li>
<li><b style="color:green">[Perbaikan]</b> Sinkronisasi peserta didik</li>
<li><b style="color:green">[Perbaikan]</b> Data rombel di menu list peserta didik</li>
<li><b style="color:green">[Perbaikan]</b> Filter peserta didik</li>
<li><b style="color:green">[Perbaikan]</b> Unduh format excel penilaian</li>
<li><b style="color:green">[Perbaikan]</b> Unduh legger KD</li>
<li><b style="color:green">[Perbaikan]</b> Update profil pengguna</li>
<li><b style="color:green">[Perbaikan]</b> Penilaian remedial</li>
<li><b style="color:green">[Perbaikan]</b> Periksa koneksi database Dapodik</li>
<li><b style="color:green">[Perbaikan]</b> Mengaktifkan form edit asesor dan instruktur</li>
<li><b style="color:green">[Perbaikan]</b> Mengganti ukuran kertas cetak rapor UTS dari Legal ke A4</li>
<li><b style="color:green">[Perbaikan]</b> Autocomplete Saved Password</li>
<li><b style="color:green">[Perbaikan]</b> Filter mapel agama sesuai agama peserta didik</li>
<li><b style="color:green">[Perbaikan]</b> Filter dudi</li>
</ol>
<legend><h3>Versi 5.0.0</h3></legend>
<ol>
<li><b style="color:blue">[Rilis]</b> Rilis aplikasi.</li>
</ol>
</div>');
			}
			//echo '<div class="last_update">'. date ("d-m-Y H:i:s", filemtime($filename)).'</div>';
			echo $hits;
			?>
@stop