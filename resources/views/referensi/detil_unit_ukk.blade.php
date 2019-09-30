@extends('layouts.modal')

@section('title') Detil Unit UKK @stop
@section('content')
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
					<td><?php echo $unit->kode_unit; ?></td>
					<td><?php echo $unit->nama_unit; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
@stop

@section('footer')
	<a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection