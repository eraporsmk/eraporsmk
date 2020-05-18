<table>
	<thead>
		<tr>
			<th colspan="4">FORMAT IMPORT {{($query == 'nilai-us') ? 'NILAI US/USBN' : 'NILAI UN'}} MATA PELAJARAN {{$pembelajaran->nama_mata_pelajaran}}</th>
		</tr>
		<tr>
			<th>NO</th>
			<th>NISN</th>
			<th>NAMA PESERTA DIDIK</th>
			<th>NILAI</th>
		</tr>
	</thead>
	<tbody>
		@foreach($get_siswa as $siswa)
		<tr>
			<td>{{$loop->iteration}}</td>
			<td>'{{$siswa->siswa->nisn}}</td>
			<td>{{strtoupper($siswa->siswa->nama)}}</td>
			<td>
				@if($query == 'nilai-us')
				{{($siswa->nilai_us) ? $siswa->nilai_us->nilai : '0'}}
				@else
				{{($siswa->nilai_un) ? $siswa->nilai_un->nilai : '0'}}
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>