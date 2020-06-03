@extends('adminlte::page')

@section('title_postfix', 'Kenaikan Kelas |')

@section('content_header')
    <h1>Proses Kenaikan Kelas</h1>
@stop

@section('content')
	@if ($message = Session::get('success'))
      <div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Sukses!</strong> {{ $message }}
      </div>
    @endif

    @if ($message = Session::get('error'))
      <div class="alert alert-danger alert-block alert-dismissable"><i class="fa fa-ban"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Error!</strong> {{ $message }}
      </div>
    @endif
<form action="{{ route('laporan.simpan_kenaikan') }}" method="post" class="form-horizontal">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="table-responsive no-padding">
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
						<input type="hidden" id="rombongan_belajar_id" value="{{$siswa->rombongan_belajar->rombongan_belajar_id}}" />
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td>
						<?php
						/*
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
						*/
						?>
						<select name="status[]" id="status" class="form-control">
							@if($cari_tingkat_akhir)
							<option value="">== Pilih Status Kenaikan==</option>
							@else
							<option value="">== Pilih Status Kelulusan==</option>
							@endif
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
					</td>
					<td>
						<input type="hidden" class="form-control" name="rombongan_belajar[]" id="rombongan_belajar" value="{{($siswa->kenaikan) ? ($siswa->kenaikan->rombongan_belajar) ? $siswa->kenaikan->rombongan_belajar->rombongan_belajar_id : '' : ''}}" />
						<input type="text" class="form-control" id="disabled" value="{{($siswa->kenaikan) ? ($siswa->kenaikan->status == 1 && $siswa->kenaikan->rombongan_belajar || $siswa->kenaikan->status == 2 && $siswa->kenaikan->rombongan_belajar) ? $siswa->kenaikan->rombongan_belajar->nama : '' : ''}}" disabled />
					</td>
				</tr>
				{{--dd($siswa)--}}
				@endforeach
			</tbody>
		</table>
	</div>
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
</form>
@stop

@section('js')
<script>
$('select#status').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var ini_id = $(this);
	var kelas_sekarang = $(this).closest('td').prev('td').find('input#kelas_sekarang').val();
	var next_td_value = $(this).closest('td').next('td').find('input#rombongan_belajar');
	var next_td_disabled = $(this).closest('td').next('td').find('input#disabled');
	var rombongan_belajar_id = $(this).closest('td').prev('td').find('input#rombongan_belajar_id').val();
	if(ini == ''){
		return false;
	}
	console.log(ini);
	if(ini == 3 || ini == 4){
		$(next_td_value).val('');
		$(next_td_disabled).val('');
	} else if(ini == 2){
		$(next_td_value).val(rombongan_belajar_id);
		$(next_td_disabled).val(kelas_sekarang);
	} else {
		$.ajax({
            url: '{{route('ajax.get_next_rombel')}}',
            type: 'post',
            data: {_token:"{{ csrf_token() }}", rombongan_belajar_id:rombongan_belajar_id},
            success: function(data){
				var value = 'a';
				const select = document.createElement("select");
				const option = document.createElement("option");
				option.value = 'a';
				option.textContent = '== Pilih Rombongan Belajar ==';
				select.appendChild(option);
				$.each(data.rombongan_belajar, function(i, item){
					const option = document.createElement("option");
					option.value = i;
					option.textContent = item;
					select.appendChild(option);
				});
				select.onchange = function selectChanged(e) {
					value = e.target.value
				}
				swal({
					title: "Pilih Rombel",
                    input: 'select',
                    content: select,
                    button: {
						text: "Pilih",
						closeModal: false,
					},
					closeOnClickOutside: false
				}).then(function() {
					if(value){
						console.log(value);
						return fetch('{{route('ajax.get_single_rombel')}}',{
							method: 'POST',
							headers: {
								"Content-Type": "application/json",
								"Accept": "application/json, text-plain, */*",
								"X-Requested-With": "XMLHttpRequest",
								"X-CSRF-TOKEN": "{{ csrf_token() }}"
							},
							credentials: "same-origin",
							body:JSON.stringify({rombongan_belajar_id: value})
						});
					} else {
						swal("Gagal!", "Rombongan Belajar tidak boleh kosong", "error").then(function(){
							$(ini_id).val('');
						});
					}
				}).then(results => {
					return results.json();
				}).then(result => {
					console.log(result);
					if(result.nama){
						$(next_td_value).val(result.rombongan_belajar_id);
						$(next_td_disabled).val(result.nama);
						swal.stopLoading();
						swal.close();
					} else {
						swal("Gagal!", "Rombongan Belajar tidak boleh kosong", "error").then(function(){
							$(ini_id).val('');
						});
					}
				});
				/*
				Swal.fire({
                    title: "Pilih Rombel",
                    input: 'select',
                    inputOptions: data.rombongan_belajar,
                    inputPlaceholder: 'Pilih Rombongan Belajar',
                    confirmButtonText: 'Pilih',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: (value) => {
                        if (value) {
                            return fetch('{{route('ajax.get_single_rombel')}}',{
								method: 'POST',
								headers: {
									"Content-Type": "application/json",
									"Accept": "application/json, text-plain, * /*",
									"X-Requested-With": "XMLHttpRequest",
									"X-CSRF-TOKEN": "{{ csrf_token() }}"
								},
								credentials: "same-origin",
								body:JSON.stringify({rombongan_belajar_id: value})
							}).then(response => {
								return response.json()
							}).catch(error => {
								Swal.showValidationMessage(
									`Request failed: ${error}`
								)
							})
                        } else {
                            Swal.showValidationMessage(
                                'Rombongan Belajar tidak boleh kosong'
                            )
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
					console.log(result);
					if(result.dismiss){
						ini_id.val('');
					} else {
						$(next_td_value).val(result.value.rombongan_belajar_id);
						$(next_td_disabled).val(result.value.nama);
					}
				});
				*/
            }
		});
	}
});
</script>
@stop