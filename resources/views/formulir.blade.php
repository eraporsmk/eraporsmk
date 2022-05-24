@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/auth.css') }}">
    @yield('css')
@stop

@section('body_class', 'login-page')

@section('body')
<?php
$sekolah = App\Sekolah::first();
$agama = App\Agama::get();
?>
    <div class="container" style="margin: 7% auto;">
        <div class="login-logo">
            <img src="{{asset('vendor/img/logo.png')}}" alt="logo" class="text-center" width="100" />
        </div>
        <div class="box">
            <!-- /.login-logo -->
            {{-- dd($errors) --}}
            <form action="{{ route('formulir') }}" method="post" class="form-horizontal">
                <input type="hidden" name="jenis_ptk_id" value="97">
                <input type="hidden" name="status_kepegawaian_id" value="99">
                <input type="hidden" name="kode_wilayah" value="016405AA">
                <input type="hidden" name="last_sync" value="{{now()}}">
                <input type="hidden" name="sekolah_id" value="{{$sekolah->sekolah_id}}">
                <div class="box-body">
                    {{--dd(config())--}}
                    <h3 class="text-center"><strong>Formulir Guru {{$sekolah->nama}}</strong></h3>
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
                    {!! csrf_field() !!}
    
                    <div class="form-group has-feedback {{ $errors->has('nama') ? 'has-error' : '' }}">
                        <label for="nama" class="col-sm-2 control-label">Nama Lengkap <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Nama Lengkap">
                            @if ($errors->has('nama'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nama') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('nik') ? 'has-error' : '' }}">
                        <label for="nik" class="col-sm-2 control-label">NIK <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="Nomor Induk Kependudukan">
                            @if ($errors->has('nik'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nik') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('nuptk') ? 'has-error' : '' }}">
                        <label for="nuptk" class="col-sm-2 control-label">NUPTK</label>
                        <div class="col-sm-10">
                            <input type="text" name="nuptk" class="form-control" value="{{ old('nuptk') }}" placeholder="NUPTK (jika ada)">
                            @if ($errors->has('nuptk'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nuptk') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('tempat_lahir') ? 'has-error' : '' }}">
                        <label for="tempat_lahir" class="col-sm-2 control-label">Tempat Lahir <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" placeholder="Tempat Lahir">
                            @if ($errors->has('tempat_lahir'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tempat_lahir') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('tanggal_lahir') ? 'has-error' : '' }}">
                        <label class="col-sm-2 control-label">Tanggal Lahir <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input name="tanggal_lahir" type="text" class="form-control pull-right" id="datepicker" placeholder="Tanggal Lahir" value="{{ old('tanggal_lahir') }}">
                            </div>
                            @if ($errors->has('tanggal_lahir'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tanggal_lahir') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('jenis_kelamin') ? 'has-error' : '' }}">
                        <label class="col-sm-2 control-label">Jenis Kelamin <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <select class="select2 form-control" name="jenis_kelamin" style="width: 100%;">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @if ($errors->has('jenis_kelamin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('jenis_kelamin') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('agama_id') ? 'has-error' : '' }}">
                        <label class="col-sm-2 control-label">Agama <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <select class="select2 form-control" name="agama_id" style="width: 100%;">
                                @foreach ($agama as $item)
                                <option value="{{$item->id ?: $item->agama_id}}">{{$item->nama}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('agama_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('agama_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('alamat') ? 'has-error' : '' }}">
                        <label for="alamat" class="col-sm-2 control-label">Alamat Rumah <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" placeholder="Alamat Rumah">
                            @if ($errors->has('alamat'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('alamat') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('rt') ? 'has-error' : '' }} {{ $errors->has('rw') ? 'has-error' : '' }}">
                        <label for="rt" class="col-sm-2 control-label">RT/RW <small class="text-red">*</small></label>
                        <div class="col-sm-5">
                            <input type="text" name="rt" class="form-control" value="{{ old('rt') }}" placeholder="RT">
                            @if ($errors->has('rt'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('rt') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-5">
                            <input type="text" name="rw" class="form-control" value="{{ old('rw') }}" placeholder="RW">
                            @if ($errors->has('rw'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('rw') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('desa_kelurahan') ? 'has-error' : '' }}">
                        <label for="desa_kelurahan" class="col-sm-2 control-label">Desa/Kelurahan <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="desa_kelurahan" class="form-control" value="{{ old('desa_kelurahan') }}" placeholder="Desa/Kelurahan">
                            @if ($errors->has('desa_kelurahan'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('desa_kelurahan') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('kode_pos') ? 'has-error' : '' }}">
                        <label for="kode_pos" class="col-sm-2 control-label">Kode Pos <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="kode_pos" class="form-control" value="{{ old('kode_pos') }}" placeholder="Kode Pos">
                            @if ($errors->has('kode_pos'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('kode_pos') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('no_hp') ? 'has-error' : '' }}">
                        <label for="no_hp" class="col-sm-2 control-label">Nomor HP Aktif <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="Nomor HP Aktif">
                            @if ($errors->has('no_hp'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('no_hp') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email" class="col-sm-2 control-label">Email Aktif <small class="text-red">*</small></label>
                        <div class="col-sm-10">
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email Aktif">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="box-footer text-right">
                    <button type="submit" class="btn btn-primary btn-flat">Simpan</button>
                </div>
            </form>
            <!-- /.login-box-body -->
        </div><!-- /.login-box -->
    </div>
@stop

@section('adminlte_js')
    <script>
        $(function () {
			$('.select2').select2();
            $('#datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd"
            })
        });
    </script>
    @yield('js')
@stop
