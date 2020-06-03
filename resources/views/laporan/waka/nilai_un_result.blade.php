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
				<input type="text" class="form-control set_nilai" name="nilai[{{$siswa->anggota_rombel_id}}]" value="{{($siswa->nilai_un) ? $siswa->nilai_un->nilai : '0'}}" />
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
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js') }}"></script>
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		}
	});
	$('#fileupload').fileupload({
		url: '{{route('laporan.import_excel')}}',
		dataType: 'json',
		progressall: function(e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress .progress-bar').css('width', progress + '%');
		},
		done: function(e, data) {
			console.log(data.result.sheetData);
			var cari_form = $('body').find('.set_nilai');
			$(cari_form).each(function(i,v) {
				$(this).val(data.result.sheetData[i].nilai);
			});
			$.each(data.result.files, function(index, file) {
				console.log('ini');
				console.log(file);
			});
			$('#progress').css('width', '0%');
		},
		error: function(data) {
			$.each(data.responseJSON.errors.file, function(index, message) {
				console.log(message);
			});
			$('#progress .progress-bar').css('width','0%');
		}
	});
</script>