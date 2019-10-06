<div class="col-sm-12">
	<table class="table table-bordered table-striped">
		<tr>
			<th width="20%">Rombongan Belajar</th>
			<th class="text-center" width="5%">:</th>
			<th width="75%">{{$pembelajaran->rombongan_belajar->nama}}</th>
		</tr>
		<tr>
			<th>Mata Pelajaran</th>
			<th class="text-center">:</th>
			<th>{{$pembelajaran->nama_mata_pelajaran}}</th>
		</tr>
		<tr>
			<th>SKM</th>
			<th class="text-center">:</th>
			<th>{{CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm)}}</th>
		</tr>
		<tr>
			<th>Kompetensi Dasar</th>
			<th class="text-center">:</th>
			<th>{{$pembelajaran->kd_nilai_capaian->kompetensi_dasar->kompetensi_dasar}}</th>
		</tr>
	</table>
	<div id="bar_analisis" style="height: 600px; width:100%"></div>
	<?php
		$capaian_kompetensi = '';
		if($pembelajaran->anggota_rombel->count()){
			$i=0;
			foreach($pembelajaran->anggota_rombel as $siswa){
				$nilai_value = $siswa->{$with}->avg('nilai_kd');
				$capaian_kompetensi .= '{';
				$capaian_kompetensi .= '"nama_siswa": "'. strtoupper($siswa->siswa->nama).'",';
				if($i==0 || $i==($pembelajaran->anggota_rombel->count() - 1)){
					$capaian_kompetensi .= '"kkm": "'. CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm).'",';
				}
				$capaian_kompetensi .= ($nilai_value) ? '"nilai":'. number_format($nilai_value,0) .'},' : '"nilai":0},';
				$i++;
			}
		}
		?>
</div>
<!-- Resources -->
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/core.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/charts.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/amcharts4/themes/animated.js') }}"></script>
<!-- Chart code -->
<script>
am4core.ready(function() {
	am4core.useTheme(am4themes_animated);
	var chart = am4core.create("bar_analisis", am4charts.XYChart);

	// Export
	chart.exporting.menu = new am4core.ExportMenu();
	chart.exporting.filePrefix = "Analisis Pencapaian Kompetensi {{$pembelajaran->nama_mata_pelajaran}} - {{$pembelajaran->rombongan_belajar->nama}}";
	chart.exporting.useWebFonts = false;
	chart.legend = new am4charts.Legend();
	chart.legend.position = "top";
	//chart.legend.contentAlign = "left";
	// Data for both series
	var data = [{!!$capaian_kompetensi!!}];
	// Create axes
	var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "nama_siswa";
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.renderer.labels.template.rotation = 310;
	
	// Create value axis
	var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.min = 0;
	valueAxis.max = 100;
	valueAxis.strictMinMax = true;
	valueAxis.renderer.minGridDistance = 5;
	var range = valueAxis.axisRanges.create();
	range.value = {{CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm)}};
	range.grid.stroke = am4core.color("#FF0000");
	range.grid.strokeWidth = 2;
	range.grid.strokeOpacity = 1;
	range.label.inside = true;
	range.label.text = "SKM";
	range.label.fill = range.grid.stroke;
	//range.label.align = "right";
	range.label.verticalCenter = "middle";
	// Create series
	var columnSeries = chart.series.push(new am4charts.ColumnSeries());
	columnSeries.name = "Nilai Peserta Didik";
	columnSeries.dataFields.valueY = "nilai";
	columnSeries.dataFields.categoryX = "nama_siswa";
	
	columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{categoryX} : [/][#fff font-weight: bold]  rata-rata {valueY}[/]"
	columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
	columnSeries.columns.template.propertyFields.stroke = "stroke";
	columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
	columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
	columnSeries.tooltip.label.textAlign = "middle";
	
	chart.data = data;

}); // end am4core.ready()
</script>