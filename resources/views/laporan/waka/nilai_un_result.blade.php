<table class="table table-bordered">
	<thead>
		<tr>
			<th width="5%" class="text-center" style="vertical-align:middle;">No</th>
			<th width="45%" class="text-center" style="vertical-align:middle;">Nama Peserta Didik</th>
			<th width="30%" class="text-center" style="vertical-align:middle;">Pembelajaran</th>
			<th width="20%" class="text-center" style="vertical-align:middle;">Nilai</th>
		</tr>
	</thead>
	<tbody>
		@forelse ($get_siswa as $siswa)
		<tr>
			<td class="text-center">{{$loop->iteration}}</td>
			<td>
				{{strtoupper($siswa->siswa->nama)}}
			</td>
			<td>
				{{$pembelajaran->nama_mata_pelajaran}}
			</td>
			<td>
				<input type="text" class="form-control" name="nilai[{{$siswa->anggota_rombel_id}}]" value="{{($siswa->nilai_un) ? $siswa->nilai_un->nilai : '0'}}" />
			</td>
		</tr>
		@empty
		<tr>
			<input type="hidden" class="form-control" name="nilai[]" value="" />
			<td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</td>
		</tr>
		@endforelse
	</tbody>
</table>