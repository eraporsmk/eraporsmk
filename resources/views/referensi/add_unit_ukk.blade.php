@extends('adminlte::page')

@section('title_postfix', 'Tambah Referensi Unit Kompetensi |')

@section('content_header')
    <h1>Tambah Referensi Unit Kompetensi</h1>
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
	<form action="{{ route('referensi.simpan_ukk') }}" class="form-horizontal" id="myform" method="post" accept-charset="utf-8">
		{{ csrf_field() }}
		<input type="hidden" name="query" value="unit_ukk" />
		<div class="form-group">
			<label for="jurusan" class="col-sm-2 control-label">Kompetensi Keahlian</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" disabled="" value="{{$paket_ukk->jurusan->nama_jurusan}}" />
				<input type="hidden" class="form-control" name="jurusan_id" value="{{$paket_ukk->jurusan_id}}" />
			</div>
		</div>
		<div class="form-group">
			<label for="kode_paket" class="col-sm-2 control-label">Kode Kompetensi</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="kode_paket" disabled="" value="{{$paket_ukk->jurusan_id}}" />
			</div>
		</div>
		<div class="form-group">
			<label for="nomor_paket" class="col-sm-2 control-label">Nomor Paket</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="nomor_paket" disabled="" value="{{$paket_ukk->nomor_paket}}" />
			</div>
		</div>
		<div class="form-group">
			<label for="paket_ukk_id" class="col-sm-2 control-label">Judul Paket</label>
			<div class="col-sm-5">
				<input type="hidden" class="form-control" name="paket_ukk_id" value="{{$paket_ukk->paket_ukk_id}}" />
				<input type="text" class="form-control" disabled="" value="{{$paket_ukk->nama_paket_id}}" />
			</div>
		</div>
		<table class="table table-striped table-bordered" id="clone">
			<thead>
				<tr>
					<th width="20%">Kode Unit</th>
					<th width="80%">Nama Unit Kompetensi</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($paket_ukk->unit_ukk as $unit){ ?>
				<tr>
					<td><input type="text" class="form-control" readonly="" value="<?php echo $unit->kode_unit; ?>" /></td>
					<td><input type="text" class="form-control" readonly="" value="<?php echo $unit->nama_unit; ?>" /></td>
				</tr>
			<?php } ?>
			<?php for ($i = 1; $i <= 5; $i++) {?>
				<tr>
					<td><input type="text" class="form-control" name="kode_unit[]" /></td>
					<td><input type="text" class="form-control" name="nama_unit[]" /></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<a class="clone btn btn-danger pull-left">Tambah Form Unit</a>
		<button type="submit" class="btn btn-success pull-right"><?php echo isset($data) ? 'Update' : 'Simpan'; ?></button>
	</form>
@stop
@section('js')
<script>
var i = <?php echo isset($i) ? $i : 0; ?>;
$("a.clone").click(function() {
	$("table#clone tbody tr:last").clone().find("td").each(function() {
		$(this).find('input[type=text]').val('');
	}).end().appendTo("table#clone");
	i++;
});
</script>
@stop