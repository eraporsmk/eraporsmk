<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Mata Pelajaran</th>
					<th class="text-center">Guru Mata Pelajaran</th>
					<th class="text-center">Pilih Penilaian</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$check = 0;
			?>
			@if($data_pembelajaran->count())
				@foreach($data_pembelajaran as $pembelajaran)
				<?php
				$rombongan_belajar_id = $pembelajaran->rombongan_belajar_id;
				$rencana_penilaian_id = [];
				if(count($pembelajaran->rapor_pts)) { 
					foreach($pembelajaran->rapor_pts as $rapor_pts){
						$rencana_penilaian_id[] = $rapor_pts->rencana_penilaian_id;
					}
					$check++; 
				} 
				?>
				<tr>
					<td class="text-center">{{$loop->iteration}}</td>
					<td>
					<input type="hidden" name="rombongan_belajar_id" value="{{$pembelajaran->rombongan_belajar_id}}" />
					{{$pembelajaran->nama_mata_pelajaran}}
					</td>
					<td>{{CustomHelper::nama_guru($pembelajaran->guru->gelar_depan, $pembelajaran->guru->nama, $pembelajaran->guru->gelar_belakang)}}</td>
					<td>
						<select class="form-control select2" name="rencana_penilaian[{{$pembelajaran->pembelajaran_id}}][]" multiple="multiple" style="width:100%">
							<option value="">== Pilih Penilaian ==</option>
							@if($pembelajaran->rencana_penilaian->count())
							@foreach($pembelajaran->rencana_penilaian as $rencana_penilaian)
							<option value="{{$rencana_penilaian->rencana_penilaian_id}}"{{(in_array($rencana_penilaian->rencana_penilaian_id,$rencana_penilaian_id)) ? ' selected="selected"' : ''}}>{{$rencana_penilaian->nama_penilaian}}</option>
							@endforeach
							@endif
						</select>
					</td>
				</tr>
				@endforeach
			@else
			@endif
			</tbody>
		</table>
@if($data_pembelajaran->count())
<button type="submit" class="btn btn-success pull-right">Simpan</button>
@endif
@if($check)
<a target="_blank" class="btn btn-warning" href="{{url('cetak/rapor-uts/'.$rombongan_belajar_id)}}"><i class="fa fa-print"></i> Cetak</a>
@endif
<script>
$('.select2').select2();
</script>