<input type="hidden" name="ekstrakurikuler_id" value="{{$ekskul->ekstrakurikuler_id}}" />
<div class="col">
	<div class="col-sm-8">
		<div class="form-group">
			<label for="ajaran_id" class="col-sm-3 control-label">Filter Kelas</label>
			<div class="col-sm-9">
				<select id="rombel_reguler" name="rombel_reguler" class="form-control select2">
					<option value="">Semua Kelas</option>
					@foreach($all_rombel as $rombel){?>
					<option value="{{$rombel->rombongan_belajar_id}}">{{$rombel->nama}}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
<div class="table-responsive no-padding">
	<table class="table table-bordered table-hover" id="result_2">
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
				{{--dd($siswa)--}}
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
						@if(!$siswa->siswa)
							{{dd($siswa)}}
						@endif
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td>{{($siswa->siswa->kelas) ? $siswa->siswa->kelas->nama : '-'}}</td>
					<td>
						<select name="nilai[]" class="form-control" id="nilai_ekskul">
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
	</table>
</div>
<script>
$('.select2').select2();
$('select#nilai_ekskul').change(function(e) {
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
$('#rombel_reguler').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var ekstrakurikuler_id = $('#ekstrakurikuler_id').val();
	$.ajax({
		url: '{{url('ajax/filter-rombel-ekskul')}}',
		type: 'post',
		data: $("#form").serialize(),
		success: function(response){
			$('#result_2').html(response);
		}
	});
});
</script>