<div class="col">
	<div class="col-sm-6">
		<div class="form-group">
			<label class="col-sm-5 control-label">&nbsp;</label>
			<div class="col-sm-7">
				<a href="{{url('laporan/unduh-kehadiran/'.$rombongan_belajar_id)}}" class="btn btn-success btn-block"><i class="fa fa-download"></i> Unduh Rekap</a>
			</div>
		</div>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th width="70%">Nama Peserta Didik</th>
			<th width="10%">Sakit</th>
			<th width="10%">Izin</th>
			<th width="10%">Tanpa Keterangan</th>
		</tr>
	</thead>
	<tbody>
	@foreach($get_siswa as $siswa)
		<tr>
			<td>
				<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
				{{strtoupper($siswa->siswa->nama)}}
			</td>
			<td>
				@if($open)
				<input type="number" class="form-control" name="sakit[]" value="{{($siswa->kehadiran) ? $siswa->kehadiran->sakit : ''}}" />
				@else
				{{($siswa->kehadiran) ? $siswa->kehadiran->sakit : ''}}
				@endif
			</td>
			<td>
				@if($open)
				<input type="number" class="form-control" name="izin[]" value="{{($siswa->kehadiran) ? $siswa->kehadiran->izin : ''}}" />
				@else
				{{($siswa->kehadiran) ? $siswa->kehadiran->izin : ''}}
				@endif
			</td>
			<td>
				@if($open)
				<input type="number" class="form-control" name="alpa[]" value="{{($siswa->kehadiran) ? $siswa->kehadiran->alpa : ''}}" />
				@else
				{{($siswa->kehadiran) ? $siswa->kehadiran->alpa : ''}}
				@endif
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@if($open)
<button type="submit" class="btn btn-success pull-right">Simpan</button>
@endif