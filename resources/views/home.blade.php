@extends('adminlte::page')
@section('title_postfix', 'Beranda | ')

@section('content_header')
    <h1>Beranda</h1>
@stop
@role('siswa')
@section('content_header_right_hidden')
<a href="{{route('cetak.rapor_user', ['user_id' => $user->user_id])}}" class="confirm btn btn-success pull-right">Cetak Rapor</a>
@stop
@endrole
@section('box-title')
Selamat Datang {{ $user->name }}
@stop
@section('content')
	@role(['admin', 'tu'])
		<div class="row">
			<div class="col-lg-3 col-sm-3 col-xs-6">
				<div class="small-box bg-green disabled color-palette">
					<div class="inner">
						<h3>{{($guru) ? number_format($guru, 0,',','.') : 0}}</h3>
						<p>PTK</p>
					</div>
				<div class="icon"><i class="ion ion-person-add"></i></div>
					<a href="{{url('guru')}}" class="small-box-footer">
						Selengkapnya <i class="fa fa-arrow-circle-right"></i>
					</a>
				</div>
			</div><!-- ./col -->
			<div class="col-lg-3 col-sm-3 col-xs-6">
				<div class="small-box bg-yellow disabled color-palette">
					<div class="inner">
						<h3>{{($siswa) ? number_format($siswa, 0,',','.') : 0}}</h3>
						<p>Peserta Didik</p>
					</div>
					<div class="icon"><i class="ion ion-android-contacts"></i></div>
						<a href="{{url('pd-aktif')}}" class="small-box-footer">
							Selengkapnya <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div><!-- ./col -->
				<div class="col-lg-3 col-sm-3 col-xs-6">
					<div class="small-box bg-red disabled color-palette">
						<div class="inner">
							<h3>{{($rencana_penilaian) ? number_format($rencana_penilaian, 0,',','.') : 0}}</h3>
							<p>Rencana Penilaian (P&amp;K)</p>
						</div>
						<div class="icon"><i class="ion ion-android-checkbox-outline"></i></div>
						<a href="javascript:void(0)" class="small-box-footer">&nbsp;</a>
					</div>
				</div><!-- ./col -->
				<div class="col-lg-3 col-sm-3 col-xs-6">
					<div class="small-box bg-maroon disabled color-palette">
						<div class="inner">
							<h3>{{($penilaian) ? number_format($penilaian, 0,',','.') : 0}}</h3>
							<p>Penilaian Per KD (P&amp;K)</p>
						</div>
						<div class="icon"><i class="ion ion-arrow-graph-up-right"></i></div>
						<a href="javascript:void(0)" class="small-box-footer">&nbsp;</a>
					</div>
				</div><!-- ./col -->
			</div>
			<div class="row">
				<div class="col-lg-6 col-xs-12">
					<div class="box-header with-border">
						<h3 class="box-title"><strong>Identitas Sekolah</strong></h3>
					</div>
					<?php //$sekolah = config('site.sekolah'); ?>
					<table class="table table-condensed">
						<tr>
							<td width="30%">Nama Sekolah</td>
							<td width="70%">: {{$sekolah->nama}}</td>
						</tr>
					<tr>
						<td>NPSN</td>
						<td>: {{$sekolah->npsn}}</td>
					</tr>
					<tr>
						<td>Alamat</td>
						<td>: {{$sekolah->alamat}}</td>
					</tr>
					<tr>
						<td>Kodepos</td>
						<td>: {{$sekolah->kode_pos}}</td>
					</tr>
					<tr>
						<td>Desa/Kelurahan</td>
						<td>: {{$sekolah->desa_kelurahan}}</td>
					</tr>
					<tr>
						<td>Kecamatan</td>
						<td>: {{$sekolah->kecamatan}}</td>
					</tr>
					<tr>
						<td>Kabupaten/Kota</td>
						<td>: {{$sekolah->kabupaten}}</td>
					</tr>
					<tr>
						<td>Provinsi</td>
						<td>: {{$sekolah->provinsi}}</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>: {{$sekolah->email}}</td>
					</tr>
					<tr>
						<td>Website</td>
						<td>: {{$sekolah->website}}</td>
					</tr>
					<tr>
					<?php
					//$data_guru = App\Guru::find($sekolah->guru_id);
					if(isset($sekolah->guru->nama)){
						$nama_kepsek = CustomHelper::nama_guru($sekolah->guru->gelar_depan, $sekolah->guru->nama, $sekolah->guru->gelar_belakang);
					} else {
						$nama_kepsek = '-';
					}
					?>
						<td>Kepala Sekolah</td>
						<td>: {{ $nama_kepsek }}</td>
					</tr>
				</table>
			</div>
			<div class="col-lg-6 col-xs-12">
				<div class="box-header with-border">
					<h3 class="box-title"><strong>Informasi Aplikasi</strong></h3>
				</div>
						<table class="table table-condensed">
					<tr>
						<td width="30%">Nama Aplikasi</td>
						<td width="70%">: {{config('site.app_name')}}</td>
					</tr>
					<tr>
						<td>Versi Aplikasi</td>
						<td>: {{config('global.app_version')}}</td>
					</tr>
					<tr>
						<td>Versi Database</td>
						<td>: {{config('global.db_version')}}</td>
					</tr>
					<tr>
						<td>Status Penilaian</td>
						<td>: <div class="btn-group" id="status" data-toggle="buttons">
							<label class="btn btn-default btn-on btn-sm{{($status_penilaian->status == 0) ? ' active' : ''}}">
							<input class="status" type="radio" value="1" name="status"{{($status_penilaian->status == 0) ? ' checked' : ''}}>AKTIF</label>
							<label class="btn btn-default btn-off btn-sm{{($status_penilaian->status == 1) ? ' active' : ''}}">
							<input class="status" type="radio" value="0" name="status"{{($status_penilaian->status == 1) ? ' checked' : ''}}>Non AKtif</label>
						  </div>
						</td>
					</tr>
					<tr>
						<td>Group Diskusi</td>
						<td>: <a href="https://www.facebook.com/groups/2003597939918600/" target="_blank" class="btn btn-sm btn-social btn-facebook"><i class="fa fa-facebook"></i>FB Group</a> <a href="http://t.me/eRaporSMK" target="_blank" class="btn btn-sm btn-social btn-info"><i class="fa fa-paper-plane"></i>Telegram</a></td>
					</tr>
					<tr>
						<td>Tim Helpdesk</td>
						<td>
							<a class="btn btn-sm btn-block btn-social btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=628156441864&text=NPSN:{{$sekolah->npsn}}"><i class="fa fa-whatsapp"></i> Wahyudin [08156441864]</a>
							<a class="btn btn-sm btn-block btn-social btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=6281229997730&amp;text=NPSN:{{$sekolah->npsn}}"><i class="fa fa-whatsapp"></i> Ahmad Aripin [081229997730]</a>
							<a class="btn btn-sm btn-block btn-social btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=6282113057512&amp;text=NPSN:{{$sekolah->npsn}}"><i class="fa fa-whatsapp"></i> Iman [082113057512]</a>
							<a class="btn btn-sm btn-block btn-social btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=6282174508706&amp;text=NPSN:{{$sekolah->npsn}}"><i class="fa fa-whatsapp"></i> Ikhsan [082174508706]</a>
							<a class="btn btn-sm btn-block btn-social btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=6282134924288&amp;text=NPSN:{{$sekolah->npsn}}"><i class="fa fa-whatsapp"></i> Toni [082134924288]</a>
							<a class="btn btn-sm btn-block btn-social btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=6285624669298&amp;text=NPSN:{{$sekolah->npsn}}"><i class="fa fa-whatsapp"></i> Deetha [085624669298]</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	@endrole
	@role('guru')
		<h4 class="page-header">Mata Pelajaran yang diampu di Tahun Pelajaran {{str_replace(' ', ' Semester ', $semester->nama)}}</h4>
		<table class="table table-bordered table-striped table-hover datatable">
			<thead>
				<tr>
					<th rowspan="2"  style="width: 10px;vertical-align:middle;" class="text-center">#</th>
					<th rowspan="2" style="vertical-align:middle;" class="text-center">Mata Pelajaran</th>
					<th rowspan="2" style="vertical-align:middle;" class="text-center">Rombel</th>
					<th rowspan="2" style="vertical-align:middle;" class="text-center">Wali Kelas</th>
					<th rowspan="2" style="vertical-align:middle;" class="text-center">Jumlah PD</th>
					<th class="text-center" colspan="2">Generate Nilai</th>
				</tr>
				<tr>
					<th class="text-center">Pengetahuan</th>
					<th class="text-center">Keterampilan</th>
				</tr>
			</thead>
			<tbody>
		@if($all_pembelajaran->count())
			@foreach($all_pembelajaran as $pembelajaran)
				<?php
				if($pembelajaran->nilai_akhir_pengetahuan_count || $pembelajaran->nilai_akhir_pk_count){
					$class_p = 'danger';
					$text_p = 'Perbaharui';
				} else {
					$text_p = 'Proses';
					$class_p = 'success';
				}
				if($pembelajaran->nilai_akhir_keterampilan_count){
					$text_k = 'Perbaharui';
					$class_k = 'danger';
				} else {
					$text_k = 'Proses';
					$class_k = 'success';
				}
				?>
				<tr>
					<td>{{$loop->iteration}}</td>
					<td>{{$pembelajaran->nama_mata_pelajaran}} ({{$pembelajaran->mata_pelajaran_id}})</td>
					<td class="text-center">{{$pembelajaran->rombongan_belajar->nama}}</td>
					<td>{{ CustomHelper::nama_guru($pembelajaran->rombongan_belajar->wali->gelar_depan, $pembelajaran->rombongan_belajar->wali->nama, $pembelajaran->rombongan_belajar->wali->gelar_belakang) }}</td>
					<td class="text-center">{{$pembelajaran->rombongan_belajar->anggota_rombel_count}}</td>
					<td><div class="text-center">
						<?php
						if($pembelajaran->rencana_pengetahuan_dinilai_count){
							echo '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/1').'" class="generate_nilai btn btn-sm btn-'.$class_p.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_p.'</a>';
						} elseif($pembelajaran->rencana_pk_dinilai_count){
							echo '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/3').'" class="generate_nilai btn btn-sm btn-'.$class_p.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_p.'</a>';
						} else {
							echo '-';
						}
						?>
					</div></td>
					<td><div class="text-center"><?php echo ($pembelajaran->rencana_keterampilan_dinilai_count) ? '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/2').'" class="generate_nilai btn btn-sm btn-'.$class_k.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_k.'</a>' : '-'; ?></div></td>
				</tr>
			@endforeach
		@else
			<tr><td class="text-center" colspan="7">Anda tidak memiliki jadwal mengajar!</td></tr>
		@endif
			</tbody>
		</table>
		{{--CustomHelper::test($all_pembelajaran)--}}
	@endrole
	@role('wali')
	@if($rombongan_belajar)
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
	<div class="row">
		<div class="col-lg-12 col-xs-12">
			<section id="mata-pelajaran">
			<?php
			$aktifkan = ($rombongan_belajar->kunci_nilai) ? 1 : 0;
			?>
				<h4>Anda adalah Wali Kelas Rombongan Belajar <label class="label bg-green">{{$rombongan_belajar->nama}}</label></h4>
				<p>Status Penilaian di Rombongan Belajar ini : <span class="btn btn-xs btn-{{($rombongan_belajar->kunci_nilai) ? 'danger' : 'success'}}"> {{($rombongan_belajar->kunci_nilai) ? 'Non Aktif' : 'Aktif'}} </span> <a class="btn btn-{{($rombongan_belajar->kunci_nilai) ? 'success' : 'danger'}} btn-xs" href="{{url('/kunci-nilai/'.$rombongan_belajar->rombongan_belajar_id.'/'.$aktifkan)}}"><i class="fa fa-power-off"></i> {{($rombongan_belajar->kunci_nilai == 1) ? 'Aktifkan' : 'Non Aktifkan'}}</a></p>
				<div class="row">
					<div class="col-lg-12 col-xs-12" style="margin-bottom:20px;">
					<h5>Daftar Mata Pelajaran di Rombongan Belajar <label class="label bg-green">{{$rombongan_belajar->nama}}</label></h5>
						<table class="table table-bordered table-striped table-hover datatable">
							<thead>
								<tr>
									<th style="width: 10px; vertical-align:middle;" class="text-center" rowspan="2">#</th>
									<th rowspan="2" style="vertical-align:middle;">Mata Pelajaran</th>
									<th rowspan="2" style="vertical-align:middle;">Guru Mata Pelajaran</th>
									<th class="text-center" rowspan="2" style="vertical-align:middle;">SKM</th>
									<th class="text-center" colspan="2">Jumlah Rencana Penilaian</th>
									<th class="text-center" colspan="2">Generate Nilai</th>
								</tr>
								<tr>
									<th class="text-center">Pengetahuan</th>
									<th class="text-center">Keterampilan</th>
									<th class="text-center">Pengetahuan</th>
									<th class="text-center">Keterampilan</th>
								</tr>
							</thead>
							<tbody>
								@if($rombongan_belajar->pembelajaran->count())
								@foreach($rombongan_belajar->pembelajaran as $pembelajaran)
								<?php
								if($pembelajaran->nilai_akhir_pengetahuan_count || $pembelajaran->nilai_akhir_pk_count){
									$class_p = 'danger';
									$text_p = 'Perbaharui';
								} else {
									$text_p = 'Proses';
									$class_p = 'success';
								}
								if($pembelajaran->nilai_akhir_keterampilan_count){
									$text_k = 'Perbaharui';
									$class_k = 'danger';
								} else {
									$text_k = 'Proses';
									$class_k = 'success';
								}
								?>
								<tr>
									<td class="text-center">{{$loop->iteration}}</td> 
									<td>{{$pembelajaran->nama_mata_pelajaran}} ({{$pembelajaran->mata_pelajaran_id}})</td>
									<td>{{(($pembelajaran->pengajar) ? 
									CustomHelper::nama_guru($pembelajaran->pengajar->gelar_depan, $pembelajaran->pengajar->nama, $pembelajaran->pengajar->gelar_belakang) : (($pembelajaran->guru) ? CustomHelper::nama_guru($pembelajaran->guru->gelar_depan, $pembelajaran->guru->nama, $pembelajaran->guru->gelar_belakang) : '-'))}}</td>
									<td class="text-center">{{CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm)}}</td>
									<td class="text-center">{{$pembelajaran->rencana_pengetahuan_count ?: $pembelajaran->rencana_pk_count}}</td>
									<td class="text-center">{{$pembelajaran->rencana_keterampilan_count}}</td>
									<!--td><div class="text-center"><?php echo ($pembelajaran->rencana_pengetahuan_dinilai_count) ? '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/1').'" class="generate_nilai btn btn-sm btn-'.$class_p.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_p.'</a>' : '-'; ?></div></td-->
									<td><div class="text-center">
										<?php
										if($pembelajaran->rencana_pengetahuan_dinilai_count){
											echo '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/1').'" class="generate_nilai btn btn-sm btn-'.$class_p.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_p.'</a>';
										} elseif($pembelajaran->rencana_pk_dinilai_count){
											echo '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/3').'" class="generate_nilai btn btn-sm btn-'.$class_p.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_p.'</a>';
										} else {
											echo '-';
										}
										?>
									</div></td>
									<td><div class="text-center"><?php echo ($pembelajaran->rencana_keterampilan_dinilai_count) ? '<a href="'.url('/generate-nilai/'.$pembelajaran->pembelajaran_id.'/2').'" class="generate_nilai btn btn-sm btn-'.$class_k.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text_k.'</a>' : '-'; ?></div></td>
								</tr>
								@endforeach
								@else
								<tr>
									<td class="text-center" colspan="8">Tidak ada data untuk ditampilkan</td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</section>
		</div>
	</div>
	@endif
	@endrole
	@role('waka')
		<h4 class="page-header">Progres Perencanaan dan Penilaian Tahun Pelajaran {{str_replace(' ', ' Semester ', $semester->nama)}}</h4>
		<div class="row" style="margin-bottom:10px;">
			<div class="col-md-4">
				<select id="filter_jurusan" class="form-control select2" style="width:100%">
					<option value="">==Filter Berdasar Kompetensi Keahlian==</option>
					@foreach($all_jurusan as $jurusan)
					<option value="{{$jurusan->jurusan_id}}">{{$jurusan->nama_jurusan_sp}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-4" id="filter_tingkat_show" style="display:none;">
				<select id="filter_tingkat" class="form-control select2" style="width:100%;">
					<option value="">==Filter Berdasar Tingkat==</option>
					<option value="10">Kelas 10</option>
					<option value="11">Kelas 11</option>
					<option value="12">Kelas 12</option>
					<option value="13">Kelas 13</option>
				</select>
			</div>
			<div class="col-md-4" id="filter_rombel_show" style="display:none;">
				<select id="filter_rombel" class="form-control select2" style="width:100%;">
				<option value="">==Filter Berdasar Rombel==</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-xs-12" style="margin-bottom:20px;">
				<table id="datatable" class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th style="vertical-align:middle;" class="text-center" rowspan="2">Rombel</th>
							<th rowspan="2" style="vertical-align:middle;">Mata Pelajaran</th>
							<th rowspan="2" style="vertical-align:middle;">Guru Mata Pelajaran</th>
							<!--th rowspan="2" style="vertical-align:middle;">Guru Mata Pelajaran</th-->
							<th class="text-center" rowspan="2" style="vertical-align:middle;">SKM</th>
							<th class="text-center" colspan="2" width="20%">Jumlah Perencanaan</th>
							<th class="text-center" colspan="2" width="20%">Jumlah Rencana Telah Dinilai</th>
							<th colspan="2" style="vertical-align:middle;" class="text-center">Generate Nilai</th>						</tr>
						<tr>
							<th class="text-center">P</th>
							<th class="text-center">K</th>
							<th class="text-center">P</th>
							<th class="text-center">K</th>
							<th class="text-center">P</th>
							<th class="text-center">K</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	@endrole
	@role('siswa')
	@if($pengguna)
		<h4 class="box-title">Anda sedang berada di Rombongan Belajar <span class="btn btn-xs btn-success">{{($pengguna->siswa->anggota_rombel->rombongan_belajar) ? $pengguna->siswa->anggota_rombel->rombongan_belajar->nama : '-'}}</span> Wali Kelas <span class="btn btn-xs btn-success">{{($user->siswa->anggota_rombel->rombongan_belajar) ? CustomHelper::nama_guru($user->siswa->anggota_rombel->rombongan_belajar->wali->gelar_depan, $user->siswa->anggota_rombel->rombongan_belajar->wali->nama, $user->siswa->anggota_rombel->rombongan_belajar->wali->gelar_belakang) : '-'}}</span></h4>
		<h4 class="page-header">Daftar Mata Pelajaran</h4>
		<div class="row">
			<div class="col-lg-12 col-xs-12" style="margin-bottom:20px;">
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th style="width: 10px; vertical-align:middle;" class="text-center" rowspan="2">#</th>
							<th style="vertical-align:middle;" rowspan="2">Mata Pelajaran</th>
							<th style="vertical-align:middle;" rowspan="2">Guru Mata Pelajaran</th>
							<th class="text-center" colspan="2">Nilai Pengetahuan</th>
							<th class="text-center" colspan="2">Nilai Keterampilan</th>
							<th style="vertical-align:middle;" class="text-center" rowspan="2">Detil Nilai</th>
						</tr>
						<tr>
							<td class="text-center">Angka</td>
							<td class="text-center">Predikat</td>
							<td class="text-center">Angka</td>
							<td class="text-center">Predikat</td>
						</tr>
					</thead>
					<tbody>
						@if($user->siswa->anggota_rombel->rombongan_belajar)
						@foreach($user->siswa->anggota_rombel->rombongan_belajar->pembelajaran as $pembelajaran)
						<?php
						//dd($pembelajaran);
						$nilai_rapor = $pembelajaran->nilai_rapor()->where('anggota_rombel_id', $user->siswa->anggota_rombel->anggota_rombel_id)->first();
						$produktif = array(4,5,9,10,13);
						if(in_array($pembelajaran->kelompok_id,$produktif)){
							$produktif = 1;
						} else {
							$produktif = 0;
						}
						?>
						<tr>
							<td class="text-center">{{$loop->iteration}}</td> 
							<td>{{$pembelajaran->nama_mata_pelajaran}}</td>  
							<td>{{(($pembelajaran->pengajar) ? 
								CustomHelper::nama_guru($pembelajaran->pengajar->gelar_depan, $pembelajaran->pengajar->nama, $pembelajaran->pengajar->gelar_belakang) : (($pembelajaran->guru) ? CustomHelper::nama_guru($pembelajaran->guru->gelar_depan, $pembelajaran->guru->nama, $pembelajaran->guru->gelar_belakang) : '-'))}}</td>  
							<td class="text-center" class="text-center">{{($nilai_rapor) ? $nilai_rapor->nilai_p : '-'}}</td>
							<td class="text-center" class="text-center">{{($nilai_rapor) ? CustomHelper::konversi_huruf($pembelajaran->kkm, $nilai_rapor->nilai_p, $produktif) : '-'}}</td>
							<td class="text-center" class="text-center">{{($nilai_rapor) ? $nilai_rapor->nilai_k : '-'}}</td>
							<td class="text-center" class="text-center">{{($nilai_rapor) ? CustomHelper::konversi_huruf($pembelajaran->kkm, $nilai_rapor->nilai_k, $produktif) : '-'}}</td>
							<td class="text-center" class="text-center">
								<a href="{{url('detil-nilai/'.$pembelajaran->pembelajaran_id)}}" class="btn btn-block btn-xs btn-success">Detil Nilai</a>
							</td>
						</tr>
						@endforeach
						@else
						<tr>
							<td colspan="8" class="text-center">Anda tidak memiliki Rombongan Belajar di semester ini.</td>
						</tr>
						@endif
					</tbody>
				</table>
			</div>
		</div>
	@endif
	@endrole
	@role('kaprog')
		<!--h3>Sedang dalam pengembangan</h3-->
	@endrole
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('css/button-switch.css') }}">
@endsection
@section('js')
<script type="text/javascript">
function turn_on_icheck(){
	$('a.generate_nilai').bind('click',function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var ini = $(this);
		var myhtml = document.createElement("div");
		myhtml.innerHTML = "Generate nilai ini akan mengeksekusi perubahan nilai.<br />Setelah generate akan dikunci kembali";
		swal({
			title: "Anda Yakin?",
			content: myhtml,
			icon: "warning",
			buttons: ["Batal", "Lanjut"],
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((willDelete) => {
			if (willDelete) {
				$.get(url).done(function(response) {
					var data = $.parseJSON(response);
					swal({title: data.title, text: data.text,icon: data.icon, button: {text: "Selesai", closeModal: true},closeOnClickOutside: false}).then((result) => {
						window.location.replace('<?php echo url('/home'); ?>');
					});
				});
			}
		});
	});
}
$(document).ready( function () {
	$('a.confirm').bind('click',function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		console.log(url);
		$.get(url).done(function(data) {
			swal({title: data.title, text: data.text,icon: data.icon, button: {text: "Cetak", closeModal: true},closeOnClickOutside: false}).then((result) => {
				if(data.success){
					window.open(url+'?cetak=1'); 
				}
			});
		});
	});
	@role('admin')
	console.log('admin');
		@if(config('erapor.access_update'))
		$.get('{{route('updater.check')}}').done(function(response) {
			if(response.new_version){
				swal({title: 'Pembaharuan Tersedia', text: 'Versi '+response.new_version+' tersedia',icon: 'success', button: {text: "Proses", closeModal: true},closeOnClickOutside: false}).then((result) => {
				window.location.replace('<?php echo route('updater.index'); ?>');
				});
			}
		});
		@endif
	@endrole
	$('.select2').select2();
	$('a.generate_nilai').bind('click',function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var ini = $(this);
		var myhtml = document.createElement("div");
		myhtml.innerHTML = "Generate nilai ini akan mengeksekusi perubahan nilai.<br />Setelah generate akan dikunci kembali";
		swal({
			title: "Anda Yakin?",
			content: myhtml,
			icon: "warning",
			buttons: ["Batal", "Lanjut"],
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((willDelete) => {
			if (willDelete) {
				$.get(url).done(function(response) {
					var data = $.parseJSON(response);
					swal({title: data.title, text: data.text,icon: data.icon, button: {text: "Selesai", closeModal: true},closeOnClickOutside: false}).then((result) => {
						window.location.replace('<?php echo url('/home'); ?>');
					});
				});
			}
		});
	});
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": {
			"url": "{{ url('progres-perencanaan-dan-penilaian') }}",
			"data": function (d) {
				var filter_jurusan = $('#filter_jurusan').val();
				var filter_tingkat = $('#filter_tingkat').val();
				var filter_rombel = $('#filter_rombel').val();
				if(filter_jurusan){
					d.filter_jurusan = filter_jurusan;
				}
				if(filter_tingkat){
					d.filter_tingkat = filter_tingkat;
				}
				if(filter_rombel){
					d.filter_rombel = filter_rombel;
				}
			}
		},
		"columns": [
            { "name": "rombongan_belajar.nama", "data": "nama_rombel" },
            { "name": "nama_mata_pelajaran", "data": "nama_mata_pelajaran" },
			{ "name": "guru.nama", "data": "guru_mapel" },
			//{ "name": "pengajar.nama", "data": "guru_pengajar"},
            { "name": "kkm", "data": "skm", "orderable": false },
            { "data": "jumlah_rencana_p", "orderable": false },
            { "data": "jumlah_rencana_k", "orderable": false },
            { "data": "jumlah_nilai_p", "orderable": false },
            { "data": "jumlah_nilai_k", "orderable": false },
			{ "data": "generate_p", "orderable": false },
			{ "data": "generate_k", "orderable": false },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
	$('#filter_jurusan').change(function(e){
		$('#filter_rombel_show').hide();
		$('#filter_tingkat').val('');
		$('#filter_tingkat').trigger('change.select2');
		$('#filter_rombel').val('');
		$('#filter_rombel').trigger('change.select2');
        table.draw();
		var ini = $(this).val();
		if(ini == ''){
			$('#filter_tingkat_show').hide();
			return false;
		}
		$('#filter_tingkat_show').show();
        e.preventDefault();
    });
	$('#filter_tingkat').change(function(e){
		$('#filter_rombel').val('');
		$('#filter_rombel').trigger('change.select2');
		table.draw();
		var ini = $(this).val();
		if(ini == ''){
			$('#filter_rombel_show').hide();
			return false;
		}
		$('#filter_rombel_show').show();
		$.ajax({
			url: '{{url('ajax/get-rombel-filter')}}',
			type: 'post',
			data: {
				"_token": "{{ csrf_token() }}",
				"jurusan_id": $('#filter_jurusan').val(),
				"tingkat": $('#filter_tingkat').val(),
			},
			success: function(response){
				var data = $.parseJSON(response);
				$('#filter_rombel').html('<option value="">== Filter Berdasar Rombel ==</option>');
				if($.isEmptyObject(data.result)){
				} else {
					$.each(data.result, function (i, item) {
						$('#filter_rombel').append($('<option>', { 
							value: item.value,
							text : item.text
						}));
					});
				}
			}
		});
		e.preventDefault();
    });
	$('#filter_rombel').change(function(e){
		table.draw();
        e.preventDefault();
    });
	//$('.status').bind('change',function(e) {
	//.change(function() {
	$('input[type=radio][name=status]').change(function() {
		// this will contain a reference to the checkbox
		console.log(this.value)
		var message;
		var status = this.value;
		if(this.value == 1){
			message = 'Penilaian akan di aktifkan';
		} else {
			message = 'Penilaian akan di non aktifkan';
		}
		swal({
			title: 'Anda Yakin?', 
			text: message,
			icon: "warning",
			buttons: true,
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((result) => {
			$.get("{{route('toggle_status_penilaian')}}", { status: status } ).done(function( data ) {
				swal({
					title: data.title, 
					text: data.message,
					icon: data.icon, 
					closeOnClickOutside: false
				})
			});
		});
	});
});
</script>
@stop