@extends('adminlte::page')

@section('content_header')
    <h1>{{$title}}</h1>
@stop
@section('content_header_right')
    <?php echo $content_header_right; ?>
@stop

@section('content')

<form method="post" action="{{ route('user.update', ['id' => $pengguna->user_id]) }}" data-parsley-validate class="form-horizontal form-label-left">

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} row">
            <label for="name" class="col-sm-2 col-form-label">Nama</label>
            <div class="col-sm-10">
                <input type="text" value="{{$pengguna->name}}" id="name" name="name" class="form-control col-md-7 col-xs-12"> @if ($errors->has('name'))
                <span class="help-block">{{ $errors->first('name') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} row">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="text" value="{{$pengguna->email}}" id="email" name="email" class="form-control col-md-7 col-xs-12"> @if ($errors->has('email'))
                <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('role_id') ? ' has-error' : '' }} row">
            <label class="col-sm-2 col-form-label" for="role_id">Hak akses
                <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
				@if(count($roles))
                    @foreach($roles as $row)
						<?php
						$disabled = '';
						if($pengguna->hasRole('guru') && $row->id == 4) { 
							$disabled = 'disabled="disabled"';
							if($pengguna->hasRole('wali') && $row->id == 10) { 
								$disabled = 'disabled="disabled"';
							}
							if($pengguna->hasRole('pembina_ekskul') && $row->id == 11) { 
								$disabled = 'disabled="disabled"';
							}
						} elseif($pengguna->hasRole('siswa') && $row->id == 5){
							$disabled = 'disabled="disabled"';
						} elseif($pengguna->hasRole('admin') && $row->id == 2){
							$disabled = 'disabled="disabled"';
						} elseif($pengguna->hasRole('tu') && $row->id == 3){
							$disabled = 'disabled="disabled"';
						}
						?>
						<div class="checkbox">
						<label>
						<input type="checkbox" class="icheck" name="role_id[]" value="{{$row->id}}" @if (in_array($row->id, $role_user)) checked="checked" @endif @if (in_array($row->id, $role_disabled)) disabled="disabled" @endif /> {{$row->display_name}}</label>
						</div>
					@endforeach
				@endif
                <!--select class="form-control" id="role_id" name="role_id">
                    @if(count($roles))
                    @foreach($roles as $row)
                    <option value="{{$row->id}}" {{$row->id == $user->roles[0]->id ? 'selected="selected"' : ''}}>{{$row->name}}</option>
                    @endforeach
                    @endif
                </select-->
                @if ($errors->has('role_id'))
                <span class="help-block">{{ $errors->first('role_id') }}</span>
                @endif
            </div>
        </div>
        <div class="ln_solid"></div>

        <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-2">
                <input type="hidden" name="_token" value="{{ Session::token() }}">
                <input name="_method" type="hidden" value="PUT">
                <button type="submit" class="btn btn-success">Simpan</button>
				<a class="btn btn-primary" href="{{ route('user.reset_password', ['id' => $pengguna->user_id]) }}">Atur ulang kata sandi</a>
            </div>
        </div>
    </form>
	
@endsection
