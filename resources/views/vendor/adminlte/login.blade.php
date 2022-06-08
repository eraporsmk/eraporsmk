@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/auth.css') }}">
    @yield('css')
@stop

@section('body_class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <img src="{{asset('vendor/img/logo.png')}}" alt="logo" class="text-center" width="100" />
        </div>
        <!-- /.login-logo -->
		{{-- dd($errors) --}}
        <div class="login-box-body">
			{{--dd(config())--}}
            <p class="login-box-msg"><strong>e-Rapor SMK Versi {{CustomHelper::get_setting('app_version')}}</strong></p>
			@if ($message = Session::get('success'))
			  <div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				  <strong>Sukses!</strong> {{ $message }}
			  </div>
			@endif
		
			@if ($message = Session::get('error'))
			  <div class="alert alert-danger alert-block alert-dismissable"><i class="fa fa-ban"></i>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Gagal!</strong> {{ $message }}
			  </div>
			@endif
            <form action="{{ url(config('adminlte.login_url', 'login')) }}" method="post">
                {!! csrf_field() !!}

                <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}{{ $errors->has('nuptk') ? 'has-error' : '' }}{{ $errors->has('nisn') ? 'has-error' : '' }}{{ $errors->has('password') ? 'has-error' : '' }}">
                    <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email/NUPTK/NISN">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
					@if ($errors->has('nuptk'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nuptk') }}</strong>
                        </span>
                    @endif
					@if ($errors->has('nisn'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nisn') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                    <input type="password" name="password" class="form-control" placeholder="Sandi">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
				<div class="form-group">
                    <select class="select2 form-control" name="semester_id" style="width: 100%;">
					@foreach ($all_data as $tahun)
						@foreach ($tahun->semester as $data)
						<option value="{{ $data->semester_id }}" {{ ((old('semester_id')) ? old('semester_id') == $data->semester_id : ($data->periode_aktif == 1 ? "selected":"")) }}>{{ $data->nama }} - Semester {{ ($data->semester == 1) ? 'Ganjil' : 'Genap' }}</option>
						@endforeach
					@endforeach
					</select>
                </div>
                <?php
				/*<div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Masuk Otomatis
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Masuk</button>
                    </div>
                    <!-- /.col -->
                </div>*/
				?>
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Masuk</button>
				</div>
            </form>
			<?php
			$sekolah = App\Sekolah::first();
			?>
			@if (!$sekolah)
            <div class="auth-links text-center">
				<p>- ATAU -</p>
				<a href="{{ url(config('adminlte.register_url', 'register')) }}" class="btn btn-success btn-block btn-flat">Registrasi</a>
            </div>
			@endif
        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function () {
			$('.select2').select2();
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    @yield('js')
@stop
