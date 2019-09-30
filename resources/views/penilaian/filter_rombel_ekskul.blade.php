		<thead>
			<tr>
				<th width="20%" class="text-center">Nama Siswa</th>
				<th width="10%" class="text-center">Kelas</th>
				<th width="20%" class="text-center">Predikat</th>
				<th width="50%" class="text-center">Deskripsi</th>
			</tr>
		</thead>
		<tbody>
			@if($all_siswa)
				@foreach($all_siswa as $siswa)
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td>{{($siswa->siswa->kelas) ? $siswa->siswa->kelas->nama : '-'}}</td>
					<td>
						<select name="nilai[]" class="form-control" id="nilai_ekskul_2">
							<option value="">== Pilih Predikat ==</option>
							<option value="1"{{($siswa->nilai_ekskul && $siswa->nilai_ekskul->nilai == 1) ? ' selected="selected"': ''}}>Sangat Baik</option>
							<option value="2"{{($siswa->nilai_ekskul && $siswa->nilai_ekskul->nilai == 2) ? ' selected="selected"': ''}}>Baik</option>
							<option value="3"{{($siswa->nilai_ekskul && $siswa->nilai_ekskul->nilai == 3) ? ' selected="selected"': ''}}>Cukup</option>
							<option value="4"{{($siswa->nilai_ekskul && $siswa->nilai_ekskul->nilai == 4) ? ' selected="selected"': ''}}>Kurang</option>
						</select>
					</td>
					<td><input type="text" class="form-control" id="deskripsi_ekskul" name="deskripsi_ekskul[]" value="{{($siswa->nilai_ekskul) ? $siswa->nilai_ekskul->deskripsi_ekskul : ''}}" /></td>
				</tr>
				@endforeach
			@else
			<tr>
				<td colspan="4" class="text-center">Tidak ada data anggota esktrakuriler</td>
			</tr>
			@endif
		</tbody>
<script>
$('.select2').select2();
$('select#nilai_ekskul_2').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var nama_ekskul = $("#kelas_ekskul option:selected").text();
	nama_ekskul = nama_ekskul.toLowerCase();
	var nilai_ekskul = $(this).find("option:selected").text();
	nilai_ekskul = nilai_ekskul.toLowerCase();
	if(ini == ''){
		$(this).closest('td').next('td').find('input').val('');
	} else {
		$(this).closest('td').next('td').find('input').val('Melaksanakan kegiatan '+nama_ekskul+' dengan '+nilai_ekskul);
	}
});
</script>