<table class="table table-bordered">
	<thead>
		<tr>
			<th class="text-center" style="vertical-align:middle;">Nama Peserta Didik</th>
			<th class="text-center" style="vertical-align:middle;">Mitra DU/DI</th>
			<th class="text-center" style="vertical-align:middle;">Alamat</th>
			<!--th class="text-center" style="vertical-align:middle;">Bidang Usaha</th-->
			<th class="text-center" style="vertical-align:middle;">Skala Kesesuaian dengan Kompetensi Keahlian (1-10)</th>
			<th class="text-center" style="vertical-align:middle;">Lamanya (bulan)</th>
			<th class="text-center" style="vertical-align:middle;">Keterangan</th>
		</tr>
	</thead>
	<tbody>
	@foreach($get_siswa as $siswa)
		<tr>
			<td>
				<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}"  />
				{{strtoupper($siswa->siswa->nama)}}
			</td>
			<td>
				@if($open)
				<select class="form-control select2" name="mitra_prakerin[]" id="mitra_prakerin" style="width:100%">
					<option value="">== Pilih DU/DI ==</option>
					@foreach($all_dudi as $dudi)
					<option value="{{$dudi->nama}}" data-bidang_usaha="{{$dudi->nama_bidang_usaha}}" data-lokasi="{{$dudi->desa_kelurahan}} - {{$dudi->kecamatan->nama}} - {{$dudi->kecamatan->get_kabupaten->nama}}"{{($siswa->prakerin) ? ($siswa->prakerin->mitra_prakerin == $dudi->nama) ? ' selected="selected"' : '' : ''}}>{{$dudi->nama}}</option>
					@endforeach
				</select>
				@else
				{{($siswa->prakerin) ? $siswa->prakerin->mitra_prakerin : '-'}}
				@endif
			</td>
			<td>
				@if($open)
				<input type="text" class="form-control" name="lokasi_prakerin[]" id="lokasi_prakerin" value="{{($siswa->prakerin) ? $siswa->prakerin->lokasi_prakerin : ''}}" />
				@else
				{{($siswa->prakerin) ? $siswa->prakerin->lokasi_prakerin : '-'}}
				@endif
			</td>
			<!--td>
				@if($open)
				<input type="text" class="form-control" name="bidang_usaha[]" id="bidang_usaha" value="{{($siswa->prakerin) ? $siswa->prakerin->bidang_usaha : ''}}" />
				@else
				{{($siswa->prakerin) ? $siswa->prakerin->bidang_usaha : '-'}}
				@endif
			</td-->
			<td>
				@if($open)
				<select class="form-control select2" name="skala[]" id="skala" style="width:100%">
					<option value="">== Pilih Skala ==</option>
					@for ($i = 1; $i < 11; $i++)
					<option value="{{$i}}"{{($siswa->prakerin) ? ($siswa->prakerin->skala == $i) ? ' selected="selected"' : '' : ''}}>{{$i}}</option>
					@endfor
				</select>
				@else
				{{($siswa->prakerin) ? $siswa->prakerin->skala : '-'}}
				@endif
			</td>
			<td>
				@if($open)
				<input type="number" class="form-control" name="lama_prakerin[]" value="{{($siswa->prakerin) ? $siswa->prakerin->lama_prakerin : ''}}" />
				@else
				{{($siswa->prakerin) ? $siswa->prakerin->lama_prakerin : '-'}}
				@endif
			</td>
			<td>
				@if($open)
				<input type="text" class="form-control" name="keterangan_prakerin[]" value="{{($siswa->prakerin) ? $siswa->prakerin->keterangan_prakerin : ''}}" />
				@else
				{{($siswa->prakerin) ? $siswa->prakerin->keterangan_prakerin : '-'}}
				@endif
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@if($open)
<button type="submit" class="btn btn-success pull-right">Simpan</button>
@endif
<script>
$('.select2').select2();
$('select#mitra_prakerin').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var lokasi_prakerin = $(this).find("option:selected").data('lokasi');
	var bidang_usaha = $(this).find("option:selected").data('bidang_usaha');
	if(ini == ''){
		$(this).closest('td').next('td').find('input').val('');
		$(this).closest('td').next('td').next('td').find('input').val('');
	} else {
		$(this).closest('td').next('td').find('input').val(lokasi_prakerin);
		$(this).closest('td').next('td').next('td').find('input').val(bidang_usaha);
	}
});
</script>