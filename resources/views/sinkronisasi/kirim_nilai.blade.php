@extends('adminlte::page')

@section('content_header')
    <h1>Kirim Nilai ke Aplikasi Dapodik</h1>
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
	{{ config('site.last_sync') }}
@Stop
@section('js')
<script>
$(document).ready(function(){
	$('body').mouseover(function(){
		$(this).css({cursor: 'progress'});
	});
	var cari = $('body').find('a[rel=next]');
	if(cari.length>0){
		var url = $(cari).attr('href');
		console.log(url);
		//window.location.replace(url);
	} else {
		//window.location.replace('<?php echo url('sinkronisasi/ambil-data'); ?>');
	}
})
</script>
@Stop