<div class="row">
	<div class="col-md-4">
		<a href="{{url('laporan/unduh-leger-kd/'.$rombongan_belajar->rombongan_belajar_id)}}" class="tooltip-viewport-bottom" title="Unduh Leger KD">
			<div class="small-box bg-yellow disabled color-palette">
				<div class="inner">
					<h3>Leger KD</h3>
					<p>Menampilkan nilai otentik per Kompetensi Dasar<br>&nbsp;</p>
				</div>
				<div class="icon">
					<i class="fa fa-file-excel-o"></i>
				</div>
				<div class="small-box-footer"><strong>(Kelas {{$rombongan_belajar->nama}})</strong></div>
			</div>
		</a>
	</div>
	<div class="col-md-4">
		<a href="{{url('laporan/unduh-leger-nilai-akhir/'.$rombongan_belajar->rombongan_belajar_id)}}" class="tooltip-viewport-bottom" title="Unduh Leger Nilai Akhir">
			<div class="small-box bg-green">
				<div class="inner">
					<h3>Leger Nilai Akhir</h3>
					<p>Menampilkan nilai akhir per kompetensi<br>&nbsp;</p>
				</div>
				<div class="icon">
					<i class="fa fa-file-excel-o"></i>
				</div>
				<div class="small-box-footer"><strong>(Kelas {{$rombongan_belajar->nama}})</strong></div>
			</div>
		</a>
	</div>
	<div class="col-md-4">
		<a href="{{url('laporan/unduh-leger-nilai-rapor/'.$rombongan_belajar->rombongan_belajar_id)}}" class="tooltip-viewport-bottom" title="Unduh Leger Nilai Rapor">
			<div class="small-box bg-red">
				<div class="inner">
					<h3>Leger Nilai Rapor</h3>
					<p>Menampilkan nilai rapor<br>&nbsp;</p>
				</div>
				<div class="icon">
					<i class="fa fa-file-excel-o"></i>
				</div>
				<div class="small-box-footer"><strong>(Kelas {{$rombongan_belajar->nama}})</strong></div>
			</div>
		</a>
	</div>
</div>