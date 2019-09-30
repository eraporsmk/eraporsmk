@extends('adminlte::page')
@section('title_postfix',  'Penilaian '.$title.' |')
@section('content_header')
    <h1>Penilaian {{$title}}</h1>
@stop

@section('content')
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
	<form action="{{ route('penilaian.simpan_nilai_ukk') }}" method="post" class="form-horizontal" id="form" accept-charset="UTF-8" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="col">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
					<div class="col-sm-9">
						<input type="hidden" name="guru_id" value="{{$user->guru_id}}" />
						<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
						<input type="hidden" name="semester_id" id="semester_id" value="{{$semester->semester_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="rencana_ukk_id" class="col-sm-3 control-label">Paket UKK</label>
					<div class="col-sm-9">
						<select name="rencana_ukk_id" class="select2 form-control" id="rencana_ukk_id" style="width:100%;">
							<option value="">== Pilih Paket UKK ==</option>
							@foreach($all_rencana_ukk as $rencana_ukk)
							<option value="{{$rencana_ukk->rencana_ukk_id}}">{{$rencana_ukk->paket_ukk->nama_paket_id}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
		<div id="result"></div>
		<div class="col">
			<div class="col-sm-8" id="simpan" style="display:none;">
				<input class="btn btn-primary" type="submit" value="Simpan">
			</div>
		</div>
	</form>
@stop

@section('js')
<script>
$('.select2').select2();
var checkJSON = function(m) {
	if (typeof m == 'object') { 
		try{ m = JSON.stringify(m); 
		} catch(err) { 
			return false; 
		}
	}
	if (typeof m == 'string') {
		try{ m = JSON.parse(m); 
		} catch (err) {
			return false;
		}
	}
	if (typeof m != 'object') { 
		return false;
	}
	return true;
};
$('#rencana_ukk_id').change(function(){
	$('#simpan').hide();
	$('#result').html('');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-siswa-nilai-ukk')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#result').html(response);
			$('#simpan').show();
		}
	});
});
</script>
@stop