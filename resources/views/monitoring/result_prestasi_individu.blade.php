<?php
$kkm_text = 'SKM';
$label_pengetahuan = "Nilai Pengetahuan per KD";
$axisLabel_pengetahuan = 'Kompetensi Dasar Pengetahuan';
$label_keterampilan = "Nilai Keterampilan per KD";
$axisLabel_keterampilan = 'Kompetensi Dasar Keterampilan';
$kkm_value = CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm);
$get_rerata_pengetahuan = $pembelajaran->one_anggota_rombel->nilai_kd_pengetahuan->avg('nilai_kd');
$get_rerata_keterampilan = $pembelajaran->one_anggota_rombel->nilai_kd_keterampilan->avg('nilai_kd');
$get_kd_pengetahuan = array();
$get_kd_keterampilan = array();
foreach($pembelajaran->kd_nilai_p as $kd_nilai_p){
	//dd($kd_nilai_p);
	$nilai = $pembelajaran->one_anggota_rombel->nilai_kd_pengetahuan()->where('kompetensi_dasar_id', $kd_nilai_p->kompetensi_dasar_id)->first();
	$get_kd_pengetahuan[] = array(
		'id'	=> $kd_nilai_p->kompetensi_dasar->id_kompetensi,
		'nilai'	=> ($nilai) ? $nilai->nilai_kd : 0,
		'nama_penilaian'	=> $kd_nilai_p->nama_penilaian,
		'kkm'	=> $kkm_value,
	);
}
foreach($pembelajaran->kd_nilai_k as $kd_nilai_k){
	$nilai = $pembelajaran->one_anggota_rombel->nilai_kd_keterampilan()->where('kompetensi_dasar_id', $kd_nilai_k->kompetensi_dasar_id)->first();
	$get_kd_keterampilan[] = array(
		'id'	=> $kd_nilai_k->kompetensi_dasar->id_kompetensi,
		'nilai'	=> ($nilai) ? $nilai->nilai_kd : 0,
		'nama_penilaian'	=> $kd_nilai_k->nama_penilaian,
		'kkm'	=> $kkm_value,
	);
}
?>
<h3><strong>Sebaran Hasil Penilaian</strong></h3>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-sm-6">
				<table class="table table-bordered table-striped">
					<tr>
						<th width="40%">Kompetensi</th>
						<th class="text-center" width="5%">:</th>
						<th width="55%">Pengetahuan</th>
					</tr>
					<tr>
						<th><?php echo $kkm_text; ?></th>
						<th class="text-center">:</th>
						<th><?php echo $kkm_value; ?></th>
					</tr>
					<tr>
						<th>Nilai rata-rata</th>
						<th class="text-center">:</th>
						<th><?php echo number_format($get_rerata_pengetahuan,0); ?></th>
					</tr>
				</table>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div id="chart_pengetahuan" style="height: 400px;"></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-sm-6">
				<table class="table table-bordered table-striped">
					<tr>
						<th width="40%">Kompetensi</th>
						<th class="text-center" width="5%">:</th>
						<th width="55%">Keterampilan</th>
					</tr>
					<tr>
						<th><?php echo $kkm_text; ?></th>
						<th class="text-center">:</th>
						<th><?php echo $kkm_value; ?></th>
					</tr>
					<tr>
						<th>Nilai rata-rata</th>
						<th class="text-center">:</th>
						<th><?php echo number_format($get_rerata_keterampilan,0); ?></th>
					</tr>
				</table>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div id="chart_keterampilan" style="height: 400px;"></div>
		</div>
	</div>
</div>
<!-- Resources -->
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/core.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/charts.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/themes/animated.js') }}"></script>
<!-- Chart code -->
<script>
am4core.ready(function() {
am4core.useTheme(am4themes_animated);
var chart_pengetahuan = am4core.create("chart_pengetahuan", am4charts.XYChart);
var chart_keterampilan = am4core.create("chart_keterampilan", am4charts.XYChart);

// Export
chart_pengetahuan.exporting.menu = new am4core.ExportMenu();
chart_pengetahuan.exporting.filePrefix = "Analisis Individu Pengetahuan {{strtoupper($pembelajaran->one_anggota_rombel->siswa->nama)}} - {{$pembelajaran->nama_mata_pelajaran}} - {{$pembelajaran->rombongan_belajar->nama}}";
chart_pengetahuan.exporting.useWebFonts = false;
chart_pengetahuan.legend = new am4charts.Legend();
chart_pengetahuan.legend.position = "top";

chart_keterampilan.exporting.menu = new am4core.ExportMenu();
chart_keterampilan.exporting.filePrefix = "Analisis Individu Keterampilan {{strtoupper($pembelajaran->one_anggota_rombel->siswa->nama)}} - {{$pembelajaran->nama_mata_pelajaran}} - {{$pembelajaran->rombongan_belajar->nama}}";
chart_keterampilan.exporting.useWebFonts = false;
chart_keterampilan.legend = new am4charts.Legend();
chart_keterampilan.legend.position = "top";
//chart.legend.contentAlign = "left";
// Data for both series
var data_pengetahuan = {!!json_encode($get_kd_pengetahuan)!!};
var data_keterampilan = {!!json_encode($get_kd_keterampilan)!!};
/* Create axes */
var categoryAxisPengetahuan = chart_pengetahuan.xAxes.push(new am4charts.CategoryAxis());
categoryAxisPengetahuan.dataFields.category = "id";
categoryAxisPengetahuan.renderer.minGridDistance = 20;
categoryAxisPengetahuan.renderer.labels.template.horizontalCenter = "right";
categoryAxisPengetahuan.renderer.labels.template.verticalCenter = "middle";
categoryAxisPengetahuan.renderer.labels.template.rotation = 310;

var categoryAxisKeterampilan = chart_keterampilan.xAxes.push(new am4charts.CategoryAxis());
categoryAxisKeterampilan.dataFields.category = "id";
categoryAxisKeterampilan.renderer.minGridDistance = 20;
categoryAxisKeterampilan.renderer.labels.template.horizontalCenter = "right";
categoryAxisKeterampilan.renderer.labels.template.verticalCenter = "middle";
categoryAxisKeterampilan.renderer.labels.template.rotation = 310;

/* Create value axis */
var valueAxisPengetahuan = chart_pengetahuan.yAxes.push(new am4charts.ValueAxis());
valueAxisPengetahuan.min = 0;
valueAxisPengetahuan.max = 100;
valueAxisPengetahuan.strictMinMax = true;
valueAxisPengetahuan.renderer.minGridDistance = 5;
var range_pengetahuan = valueAxisPengetahuan.axisRanges.create();
range_pengetahuan.value = {{$kkm_value}};
range_pengetahuan.grid.stroke = am4core.color("#FF0000");
range_pengetahuan.grid.strokeWidth = 2;
range_pengetahuan.grid.strokeOpacity = 1;
range_pengetahuan.label.inside = true;
range_pengetahuan.label.text = "SKM";
range_pengetahuan.label.fill = range_pengetahuan.grid.stroke;
//range_pengetahuan.label.align = "right";
range_pengetahuan.label.verticalCenter = "middle";

var valueAxisKeterampilan = chart_keterampilan.yAxes.push(new am4charts.ValueAxis());
valueAxisKeterampilan.min = 0;
valueAxisKeterampilan.max = 100;
valueAxisKeterampilan.strictMinMax = true;
valueAxisKeterampilan.renderer.minGridDistance = 5;
var range_keterampilan = valueAxisKeterampilan.axisRanges.create();
range_keterampilan.value = {{$kkm_value}};
range_keterampilan.grid.stroke = am4core.color("#FF0000");
range_keterampilan.grid.strokeWidth = 2;
range_keterampilan.grid.strokeOpacity = 1;
range_keterampilan.label.inside = true;
range_keterampilan.label.text = "SKM";
range_keterampilan.label.fill = range_pengetahuan.grid.stroke;
//range_keterampilan.label.align = "right";
range_keterampilan.label.verticalCenter = "middle";
/* Create series */
var columnSeriesPengetahuan = chart_pengetahuan.series.push(new am4charts.ColumnSeries());
columnSeriesPengetahuan.name = "Nilai Pengetahuan Per KD";
columnSeriesPengetahuan.dataFields.valueX = "nama_penilaian";
columnSeriesPengetahuan.dataFields.valueY = "nilai";
columnSeriesPengetahuan.dataFields.categoryX = "id";
columnSeriesPengetahuan.columns.template.tooltipText = "[#fff font-size: 15px]Nama Penilaian : {nama_penilaian}\n KD : {categoryX}[/]\n [#fff font-weight: bold]Nilai : {valueY}[/]"
columnSeriesPengetahuan.columns.template.propertyFields.fillOpacity = "fillOpacity";
columnSeriesPengetahuan.columns.template.propertyFields.stroke = "stroke";
columnSeriesPengetahuan.columns.template.propertyFields.strokeWidth = "strokeWidth";
columnSeriesPengetahuan.columns.template.propertyFields.strokeDasharray = "columnDash";
columnSeriesPengetahuan.tooltip.label.textAlign = "middle";

var columnSeriesKeterampilan = chart_keterampilan.series.push(new am4charts.ColumnSeries());
columnSeriesKeterampilan.name = "Nilai Keterampilan Per KD";
columnSeriesKeterampilan.dataFields.valueX = "nama_penilaian";
columnSeriesKeterampilan.dataFields.valueY = "nilai";
columnSeriesKeterampilan.dataFields.categoryX = "id";
columnSeriesKeterampilan.columns.template.tooltipText = "[#fff font-size: 15px]Nama Penilaian : {nama_penilaian}\n KD : {categoryX}[/]\n [#fff font-weight: bold]Nilai : {valueY}[/]"
columnSeriesKeterampilan.columns.template.propertyFields.fillOpacity = "fillOpacity";
columnSeriesKeterampilan.columns.template.propertyFields.stroke = "stroke";
columnSeriesKeterampilan.columns.template.propertyFields.strokeWidth = "strokeWidth";
columnSeriesKeterampilan.columns.template.propertyFields.strokeDasharray = "columnDash";
columnSeriesKeterampilan.tooltip.label.textAlign = "middle";

chart_pengetahuan.data = data_pengetahuan;
chart_keterampilan.data = data_keterampilan;

}); // end am4core.ready()
</script>