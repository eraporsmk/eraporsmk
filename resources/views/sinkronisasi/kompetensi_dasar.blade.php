@extends('adminlte::page')

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
	<style>
	.box{min-height:400px;}
	</style>
	@role('superadmin')
		<p>This is visible to users with the admin role. Gets translated to
		\Laratrust::hasRole('superadmin')</p>
	@endrole
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
	<div id="status" class="text-center">
		<h1>Sedang proses sinkronisasi Ref. Kompetensi Dasar</h1>
		<img src="<?php echo url('vendor/img/loading.gif'); ?>" />
		<h2 id="count">Jumlah Ref.KD terproses : {{ $terproses }}</h2>
		<h3>Jangan tutup browser sebelum proses ini selesai</h3>
	</div>
	<div id="response"></div>
@Stop
@section('js')
<script>
$(document).ready(function(){
	$.get('<?php echo url('sinkronisasi/proses-artisan'); ?>', function(data) {
		$("#spinner").remove();
		$('#status').hide('slow');
		$('#response').html(data);
		window.location.replace('<?php echo url('/sinkronisasi/ambil-data'); ?>');
	});
	$.ajaxSetup({ cache: false });
	setInterval(function() {
		$('#count').load('<?php echo url('sinkronisasi/jumlah_kd'); ?>');
	}, 3000);
})
</script>
@Stop