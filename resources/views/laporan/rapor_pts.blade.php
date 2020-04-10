@extends('adminlte::page')

@section('title_postfix', 'Cetak Rapor UTS |')

@section('content_header')
    <h1>Cetak Rapor UTS</h1>
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
<form action="{{ route('laporan.cetak_uts') }}" method="post" class="form-horizontal">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="table-responsive no-padding">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Mata Pelajaran</th>
					<th class="text-center">Guru Mata Pelajaran</th>
					<th class="text-center">Pilih Penilaian</th>
				</tr>
			</thead>
			<tbody>
			<?php $check = 0; ?>
			@if($data_pembelajaran->count())
				@foreach($data_pembelajaran as $pembelajaran)
				<?php
				$rombongan_belajar_id = $pembelajaran->rombongan_belajar_id;
				$rencana_penilaian_id = [];
				if(count($pembelajaran->rapor_pts)) { 
					foreach($pembelajaran->rapor_pts as $rapor_pts){
						$rencana_penilaian_id[] = $rapor_pts->rencana_penilaian_id;
					}
					$check++; 
				} 
				?>
				<tr>
					<td class="text-center">{{$loop->iteration}}</td>
					<td>
					<input type="hidden" name="rombongan_belajar_id" value="{{$pembelajaran->rombongan_belajar_id}}" />
					{{$pembelajaran->nama_mata_pelajaran}}
					</td>
					<td>{{CustomHelper::nama_guru($pembelajaran->guru->gelar_depan, $pembelajaran->guru->nama, $pembelajaran->guru->gelar_belakang)}}</td>
					<td>
						<select class="form-control select2" name="rencana_penilaian[{{$pembelajaran->pembelajaran_id}}][]" multiple="multiple" style="width:100%">
							<option value="">== Pilih Penilaian ==</option>
							@if($pembelajaran->rencana_penilaian->count())
							@foreach($pembelajaran->rencana_penilaian as $rencana_penilaian)
							<option value="{{$rencana_penilaian->rencana_penilaian_id}}"{{(in_array($rencana_penilaian->rencana_penilaian_id,$rencana_penilaian_id)) ? ' selected="selected"' : ''}}>{{$rencana_penilaian->nama_penilaian}}</option>
							@endforeach
							@endif
						</select>
					</td>
				</tr>
				{{--dd($pembelajaran)--}}
				@endforeach
			@else
			@endif
			</tbody>
		</table>
	</div>
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
</form>
@if($check)
<a target="_blank" class="btn btn-warning" href="{{url('cetak/rapor-uts/'.$rombongan_belajar_id)}}"><i class="fa fa-print"></i> Cetak</a>
@endif
{{--dd($data_pembelajaran)--}}
@stop
@section('js')
<script>
$('.select2').select2();
</script>
@stop