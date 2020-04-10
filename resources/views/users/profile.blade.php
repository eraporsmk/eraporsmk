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
<form id="form-change-password" role="form" method="POST" action="{{ route('update_profile', ['id' => $user->user_id]) }}" novalidate enctype="multipart/form-data">
	<input type="hidden" name="_token" value="{{ csrf_token() }}"> 
	<div class="col-md-8">
		<label for="name" class="col-form-label">Nama</label>
		<div class="form-group">
			<input type="text" value="{{$user->name}}" id="name" name="name" class="form-control">
		</div>
		<label for="email" class="col-form-label">Email</label>
		<div class="form-group">
			<input type="email" value="{{$user->email}}" id="email" name="email" class="form-control">
		</div>
		<label for="current-password" class="col-form-label">Sandi Saat Ini (Biarkan kosong jika tidak ingin merubah)</label>
		<div class="form-group">
			<input type="password" class="form-control" id="current-password" name="current_password" placeholder="Sandi saat ini" autocomplete="new-password">
		</div>
		<label for="password" class="col-form-label">Sandi Baru</label>
		<div class="form-group">
				<input type="password" class="form-control" id="password" name="password" placeholder="Sandi baru" >
		</div>
		<label for="password_confirmation" class="col-form-label">Konfirmasi Sandi</label>
		<div class="form-group">
				<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang kata sandi baru">
		</div>
	</div>
	<div class="col-md-4 text-center">
		<label for="current-password" class="col-form-label">Foto Profil</label>
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<?php $img = ($user->photo!= '')  ? url('storage/images/245/'.$user->photo) : url('vendor/img/avatar3.png'); ?><img src="<?php echo $img;?>" class="user-image" alt="User Image" />
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