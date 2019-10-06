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
            <img src="{{url('vendor/img/logo.png')}}" alt="logo" class="text-center" width="100" />
        </div>
        <!-- /.login-logo -->
		{{-- dd($errors) --}}
        <div class="login-box-body">
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
            <form action="{{ route('form_activated')}}" method="post">
                {!! csrf_field() !!}
				<div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                    <input type="text" name="email" class="form-control" placeholder="Email/NISN/NUPTK" value="{{ old('email') }}">
					<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
					@if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('kode_aktivasi') ? 'has-error' : '' }}">
                    <input type="text" name="kode_aktivasi" class="form-control" placeholder="Kode Aktifasi" value="{{ old('activation_code') }}">
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					@if ($errors->has('kode_aktivasi'))
                        <span class="help-block">
                            <strong>{{ $errors->first('kode_aktivasi') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Aktifasi</button>
				</div>
            </form>
			<div class="auth-links text-center">
				<p>- ATAU -</p>
                <a href="{{ url(config('adminlte.login_url', 'login')) }}" class="btn btn-warning btn-block btn-flat">Login</a>
            </div>
        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop
