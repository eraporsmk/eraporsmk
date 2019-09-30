<table class="table table-bordered">
			<thead>
				<tr>
					<th width="50%" class="text-center" style="vertical-align:middle;">Nama Peserta Didik</th>
					<th width="25%" class="text-center" style="vertical-align:middle;">Status Kenaikan</th>
					<th width="25%" class="text-center" style="vertical-align:middle;">Ke Kelas</th>
				</tr>
			</thead>
			<tbody>
				@foreach($get_siswa as $siswa)
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
						<input type="hidden" id="kelas_sekarang" value="{{$siswa->rombongan_belajar->nama}}" />
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td>
						@if($open)
						<select name="status[]" id="status" class="form-control">
							<option value="">== Pilih Status Kenaikan==</option>
							@if($cari_tingkat_akhir)
							<option value="3"{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 3) ? ' selected="selected"' : '' : ''}}>Lulus</option>
							<option value="4"{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 4) ? ' selected="selected"' : '' : ''}}>Tidak Lulus</option>
							@else
								@if($siswa->rombongan_belajar->tingkat == 12)
									<option value="3"{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 3) ? ' selected="selected"' : '' : ''}}>Lulus</option>
									<option value="4"{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 4) ? ' selected="selected"' : '' : ''}}>Tidak Lulus</option>
								@else
									<option value="1"{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 1) ? ' selected="selected"' : '' : ''}}>Naik Ke Kelas</option>
									<option value="2"{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 2) ? ' selected="selected"' : '' : ''}}>Tidak Naik</option>
								@endif
							@endif
						</select>
						@else
						{{
						($siswa->kenaikan) ? 
						($siswa->kenaikan->status == 1) ? 'a' : 'b'
						: 'Belum dilakukan proses kenaikan'
						}}
						@endif
					</td>
					<td>
						@if($open)
						<input type="text" class="form-control" name="rombongan_belajar[]" id="rombongan_belajar" value="{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 2) ? $siswa->rombongan_belajar->nama : $siswa->kenaikan->rombongan_belajar : ''}}" />
						@else
						{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 2) ? $siswa->rombongan_belajar->nama : $siswa->kenaikan->rombongan_belajar : '-'}}
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
<script>
$('select#status').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var prev_td = $(this).closest('td').prev('td').find('input#kelas_sekarang').val();
	var next_td = $(this).closest('td').next('td').find('input');
	if(ini == 2){
		$(next_td).val(prev_td);
		//$(next_td).attr('disabled', 'disabled');
	} else {
		//$(next_td).removeAttr('disabled');
		$(next_td).val('');
	}
});
</script>