<?php $nilai_value = array(); ?>
@foreach($rencana_penilaian->pembelajaran->anggota_rombel as $anggota_rombel)
	<?php $skor_akhir = 0; ?>
	@if($kompetensi_id == 1)
		@foreach($anggota_rombel->nilai_kd_pengetahuan as $kd_pengetahuan)
			<?php $skor_akhir += $kd_pengetahuan->nilai_kd; ?>
		@endforeach
		<?php $result = ($skor_akhir) ? number_format($skor_akhir / count($anggota_rombel->nilai_kd_pengetahuan), 0) : 0; ?>
		<?php $nilai_value[strtoupper($anggota_rombel->siswa->nama)] = $result; ?>
	@else
		@foreach($anggota_rombel->nilai_kd_keterampilan as $kd_keterampilan)
			<?php $skor_akhir += $kd_keterampilan->nilai_kd; ?>
		@endforeach
		<?php $result = ($skor_akhir) ? number_format($skor_akhir / count($anggota_rombel->nilai_kd_keterampilan), 0) : 0; ?>
		<?php $nilai_value[strtoupper($anggota_rombel->siswa->nama)] = $result; ?>
	@endif
@endforeach
<h3><strong>Sebaran Hasil Penilaian Per Rencana Penilaian</strong></h3>
<div class="row">
	<div class="col-sm-6">
	<table class="table table-bordered table-striped">
		<tr>
			<td width="40%">Rombongan Belajar</td>
			<td class="text-center" width="5%">:</td>
			<td width="55%">{{$rencana_penilaian->pembelajaran->rombongan_belajar->nama}}</td>
		</tr>
		<tr>
			<td>Mata Pelajaran</td>
			<td class="text-center">:</td>
			<td>{{$rencana_penilaian->pembelajaran->nama_mata_pelajaran}}</td>
		</tr>
		<tr>
			<td>Penilaian</td>
			<td class="text-center">:</td>
			<td>{{$rencana_penilaian->nama_penilaian}}</td>
		</tr>
		<tr>
			<td>SKM</td>
			<td class="text-center">:</td>
			<td>{{CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm)}}</td>
		</tr>
		<tr>
			<td>Bobot Penilaian</td>
			<td class="text-center">:</td>
			<td>{{$rencana_penilaian->bobot}}</td>
		</tr>
	</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-9">
		<div id="chartdiv" style="width: 100%; height:350px;"></div>
	</div>
	<div class="col-sm-3">
		<table class="table table-bordered table-hover">
			<tr>
				<td width="50%" class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="95-100">A+</a></td>
				<td width="50%" class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'A'),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'A+'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="90-94">A</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'A-'),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'A'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="90-94">A-</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'B'),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'A-'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="80-84">B+</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'B'),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'B+'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="75-79">B</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'B-'),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'B'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="70-74">B-</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'C'),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'B-'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="60-69">C</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'C'),'left'); ?></td>
			</tr>
			<tr>
				<td class="text-center"><a class="tooltip-left" href="javascript:void(0)" title="0-59">D</a></td>
				<td class="text-center"><?php echo CustomHelper::sebaran_tooltip($nilai_value,0,CustomHelper::predikat(CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm),'D'),'left'); ?></td>
			</tr>
		</table>
	</div>
</div>
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.css') }}">
<script src="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.js') }}"></script>
<!-- Resources -->
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/core.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/charts.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/themes/animated.js') }}"></script>
<!-- Chart code -->
<script>
am4core.ready(function() {
am4core.useTheme(am4themes_animated);
var chart = am4core.create("chartdiv", am4charts.XYChart);

// Export
chart.exporting.menu = new am4core.ExportMenu();
chart.exporting.filePrefix = "Analisis Hasil Penilaian {{$rencana_penilaian->pembelajaran->nama_mata_pelajaran}} - {{$rencana_penilaian->pembelajaran->rombongan_belajar->nama}}";
chart.exporting.useWebFonts = false;
chart.legend = new am4charts.Legend();
chart.legend.position = "top";
//chart.legend.contentAlign = "left";
// Data for both series
var data = [
        {
			"rentang": "100-95", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,100,95)) }}
		},
		{
			"rentang": "89-85", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,89,85)) }}
		},
		{
			"rentang": "84-80", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,84,80)) }}
		},
		{
			"rentang": "79-75", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,79,75)) }}
		},
		{
			"rentang": "74-70", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,74,70)) }}
		},
		{
			"rentang": "69-65", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,69,65)) }}
		},
		{
			"rentang": "64-60", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,64,60)) }}
		},
		{
			"rentang": "59-55", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,59,55)) }}
		},
		{
			"rentang": "54-50", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,54,50)) }}
		},
		{
			"rentang": "49-0", 
			"jumlah_pd": {{ count(CustomHelper::sebaran($nilai_value,49,0)) }}
		}
    ];
/* Create axes */
var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "rentang";
categoryAxis.renderer.minGridDistance = 20;

/* Create value axis */
var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

/* Create series */
var columnSeries = chart.series.push(new am4charts.ColumnSeries());
columnSeries.name = "Sebaran Peserta Didik";
columnSeries.dataFields.valueY = "jumlah_pd";
columnSeries.dataFields.categoryX = "rentang";

columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} \n Rentang nilai {categoryX} : [/][#fff font-weight: bold] {valueY} peserta didik[/]"
columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
columnSeries.columns.template.propertyFields.stroke = "stroke";
columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
columnSeries.tooltip.label.textAlign = "middle";

chart.data = data;

}); // end am4core.ready()
</script>