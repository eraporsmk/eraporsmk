<div style="clear:both;"></div>
<div class="table-responsive no-padding">
	<table class="table table-bordered table-hover" id="result_2">
		<thead>
			<tr>
				<th width="20%" class="text-center" rowspan="3" style="vertical-align: :middle;">Nama Siswa</th>
				<!--th width="10%" class="text-center" rowspan="3" style="vertical-align: :middle;">Kelas</th-->
				<th width="70%" class="text-center" colspan="{{$rencana_p5bk->count()}}" style="vertical-align: :middle;">Aspek Dinilai</th>
			</tr>
			<tr>
				@foreach ($rencana_p5bk as $item)
				<th class="text-center" width="20%" style="vertical-align: :middle;">{{$item->budaya_kerja->aspek}}</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			@if($data_siswa->count())
				@foreach($data_siswa as $siswa)
				{{--dd($siswa)--}}
				<tr>
					<td>
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<!--td class="text-center">{{($siswa->siswa->kelas) ? $siswa->siswa->kelas->nama : '-'}}</td-->
					@foreach ($rencana_p5bk as $item)
					<td>
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>Elemen</th>
									<th>Nilai</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($item->budaya_kerja->elemen_budaya_kerja as $elemen_budaya_kerja)
								<?php
								$nilai_budaya_kerja = $siswa->nilai_budaya_kerja()->where('aspek_budaya_kerja_id', $item->aspek_budaya_kerja_id)->where('elemen_id', $elemen_budaya_kerja->elemen_id)->first();
								?>
								<tr>
									<td>{{$elemen_budaya_kerja->elemen}}</td>
									<td>
										<select name="nilai[{{$siswa->anggota_rombel_id}}][{{$item->aspek_budaya_kerja_id}}][{{$elemen_budaya_kerja->elemen_id}}]" class="form-control select2" style="width: 100%">
											<option value="">-</option>
											@foreach ($opsi_budaya_kerja as $opsi)
											<option value="{{$opsi->opsi_id}}" {{($nilai_budaya_kerja && $nilai_budaya_kerja->opsi_id == $opsi->opsi_id) ? 'selected' : ''}}>{{$opsi->kode}}</option>
											@endforeach
										</select>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</td>
					@endforeach
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