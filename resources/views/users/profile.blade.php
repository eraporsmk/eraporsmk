@extends('adminlte::page')

@section('content_header')
<h1>{{$title}}</h1>
@stop

@section('content')
{{-- dd($errors) --}}
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
@if ($errors->any())
<div class="alert alert-danger alert-block alert-dismissable">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<form id="form-change-password" role="form" method="POST"
	action="{{ route('update_profile', ['id' => $user->user_id]) }}" novalidate enctype="multipart/form-data">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	@role('siswa')
	<div class="col-md-6">
		<table class="table">
			<tr>
				<td width="30%">Nama</td>
				<td width="1">:</td>
				<td width="70%">{{trim($user->siswa->nama)}}</td>
			</tr>
			<tr>
				<td>NIS</td>
				<td>:</td>
				<td>{{$user->siswa->no_induk}}</td>
			</tr>
			<tr>
				<td>NISN</td>
				<td>:</td>
				<td>{{$user->siswa->nisn}}</td>
			</tr>
			<tr>
				<td>NIK</td>
				<td>:</td>
				<td>{{$user->siswa->nik}}</td>
			</tr>
			<tr>
				<td>Jenis Kelamin</td>
				<td>:</td>
				<td>{{($user->siswa->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan'}}</td>
			</tr>
			<tr>
				<td>tempat_lahir</td>
				<td>:</td>
				<td>{{$user->siswa->tempat_lahir}}, {{CustomHelper::TanggalIndo($user->siswa->tanggal_lahir)}}</td>
			</tr>
			<tr>
				<td>Agama</td>
				<td>:</td>
				<td>{{($user->siswa->agama) ? $user->siswa->agama->nama : '-'}}</td>
			</tr>
			<tr>
				<td>Status dalam keluarga</td>
				<td>:</td>
				<td>{{$user->siswa->status}}</td>
			</tr>
			<tr>
				<td>Anak ke</td>
				<td>:</td>
				<td>{{$user->siswa->anak_ke}}</td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td>:</td>
				<td>{{$user->siswa->alamat}}</td>
			</tr>
			<tr>
				<td>RT/RW</td>
				<td>:</td>
				<td>{{$user->siswa->rt}}/{{$user->siswa->rw}}</td>
			</tr>
			<tr>
				<td>Desa/Kelurahan</td>
				<td>:</td>
				<td>{{$user->siswa->desa_kelurahan}}</td>
			</tr>
			<tr>
				<td>Kecamatan</td>
				<td>:</td>
				<td>{{$user->siswa->kecamatan}}</td>
			</tr>
			<tr>
				<td>Kodepos</td>
				<td>:</td>
				<td>{{$user->siswa->kode_pos}}</td>
			</tr>
			<tr>
				<td>Telp/HP</td>
				<td>:</td>
				<td>{{$user->siswa->no_telp}}</td>
			</tr>
			<tr>
				<td>Asal Sekolah</td>
				<td>:</td>
				<td>{{$user->siswa->sekolah_asal}}</td>
			</tr>
			<tr>
				<td>Diterima dikelas</td>
				<td>:</td>
				<td>{{$user->siswa->diterima_kelas}}</td>
			</tr>
			<tr>
				<td>Diterima pada tanggal</td>
				<td>:</td>
				<td>{{CustomHelper::TanggalIndo(date('Y-m-d', strtotime($user->siswa->diterima)))}}</td>
			</tr>
			<tr>
				<td>Nama Ayah</td>
				<td>:</td>
				<td>{{$user->siswa->nama_ayah}}</td>
			</tr>
			<tr>
				<td>Pekerjaan Ayah</td>
				<td>:</td>
				<td>{{($user->siswa->pekerjaan_ayah) ? $user->siswa->pekerjaan_ayah->nama : '-'}}</td>
			</tr>
			<tr>
				<td>Nama Ibu</td>
				<td>:</td>
				<td>{{$user->siswa->nama_ibu}}</td>
			</tr>
			<tr>
				<td>Pekerjaan Ibu</td>
				<td>:</td>
				<td>{{($user->siswa->pekerjaan_ibu) ? $user->siswa->pekerjaan_ibu->nama : '-'}}</td>
			</tr>
			<tr>
				<td>Nama Wali</td>
				<td>:</td>
				<td>{{($user->siswa->nama_wali) ? $user->siswa->nama_wali : '-'}}</td>
			</tr>
			<tr>
				<td>Alamat Wali</td>
				<td>:</td>
				<td>{{($user->siswa->nama_wali) ? $user->siswa->alamat_wali : '-'}}</td>
			</tr>
			<tr>
				<td>Telp/HP Wali</td>
				<td>:</td>
				<td>{{($user->siswa->nama_wali) ? $user->siswa->telp_wali : '-'}}</td>
			</tr>
			<tr>
				<td>Pekerjaan Wali</td>
				<td>:</td>
				<td>{{($user->siswa->nama_wali) ? $user->siswa->pekerjaan_wali->nama : '-'}}</td>
			</tr>
		</table>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<?php $img = ($user->photo!= '')  ? asset('storage/images/245/'.$user->photo) : asset('vendor/img/avatar3.png'); ?><img
				src="<?php echo $img;?>" class="img-responsive center-block" alt="{{$user->name}}" />
			<div class="input-group" style="margin-top:10px;">
				<label class="input-group-btn">
					<span class="btn btn-primary">
						Cari berkas&hellip; <input type="file" name="image" style="display: none;" multiple>
					</span>
				</label>
				<input type="text" class="form-control" readonly>
			</div>
		</div>
		<label for="email" class="col-form-label">Email</label>
		<div class="form-group">
			<input type="hidden" value="{{$user->name}}" id="name" name="name" class="form-control">
			<input type="email" value="{{$user->email}}" id="email" name="email" class="form-control">
		</div>
		<label for="current-password" class="col-form-label">Sandi Saat Ini (Biarkan kosong jika tidak ingin
			merubah)</label>
		<div class="form-group">
			<input type="password" class="form-control" id="current-password" name="current_password"
				placeholder="Sandi saat ini" autocomplete="new-password">
		</div>
		<label for="password" class="col-form-label">Sandi Baru</label>
		<div class="form-group">
			<input type="password" class="form-control" id="password" name="password" placeholder="Sandi baru">
		</div>
		<label for="password_confirmation" class="col-form-label">Konfirmasi Sandi</label>
		<div class="form-group">
			<input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
				placeholder="Ketik ulang kata sandi baru">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-danger pull-right">Simpan</button>
		</div>
	</div>
	@else
	<div class="col-md-8">
		<label for="name" class="col-form-label">Nama</label>
		<div class="form-group">
			<input type="text" value="{{$user->name}}" id="name" name="name" class="form-control">
		</div>
		<label for="email" class="col-form-label">Email</label>
		<div class="form-group">
			<input type="email" value="{{$user->email}}" id="email" name="email" class="form-control">
		</div>
		<label for="current-password" class="col-form-label">Sandi Saat Ini (Biarkan kosong jika tidak ingin
			merubah)</label>
		<div class="form-group">
			<input type="password" class="form-control" id="current-password" name="current_password"
				placeholder="Sandi saat ini" autocomplete="new-password">
		</div>
		<label for="password" class="col-form-label">Sandi Baru</label>
		<div class="form-group">
			<input type="password" class="form-control" id="password" name="password" placeholder="Sandi baru">
		</div>
		<label for="password_confirmation" class="col-form-label">Konfirmasi Sandi</label>
		<div class="form-group">
			<input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
				placeholder="Ketik ulang kata sandi baru">
		</div>
	</div>
	<div class="col-md-4 text-center">
		<label for="current-password" class="col-form-label">Foto Profil</label>
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<?php $img = ($user->photo!= '')  ? asset('storage/images/245/'.$user->photo) : asset('vendor/img/avatar3.png'); ?><img
						src="<?php echo $img;?>" class="user-image" alt="User Image" />
					<div class="input-group" style="margin-top:10px;">
						<label class="input-group-btn">
							<span class="btn btn-primary">
								Cari berkas&hellip; <input type="file" name="image" style="display: none;" multiple>
							</span>
						</label>
						<input type="text" class="form-control" readonly>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-danger">Simpan</button>
		</div>
	</div>
	@endif
</form>

@endsection

@section('js')
<script>
	$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });
  
});
</script>
@endsection