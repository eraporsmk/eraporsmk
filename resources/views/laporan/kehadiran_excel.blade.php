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
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td>{{($siswa->kehadiran) ? $siswa->kehadiran->sakit : ''}}</td>
					<td>{{($siswa->kehadiran) ? $siswa->kehadiran->izin : ''}}</td>
					<td>{{($siswa->kehadiran) ? $siswa->kehadiran->alpa : ''}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>