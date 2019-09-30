@extends('adminlte::page')

@section('content_header')
    <h1>Sinkonisasi Dapodik</h1>
@stop

@section('content')
	@role('superadmin')
		<p>This is visible to users with the admin role. Gets translated to
		\Laratrust::hasRole('superadmin')</p>
	@endrole
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
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th class="text-center" style="vertical-align: middle;">No</th>
					<th class="text-center">mata_pelajaran_id</th>
					<th class="text-center">nama</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				foreach($dapodik as $data){
					$data->created_at = date('Y-m-d H:i:s', strtotime($data->create_date));
					$data->updated_at = date('Y-m-d H:i:s', strtotime($data->last_update));
					$data->deleted_at = ($data->expired_date) ? date('Y-m-d H:i:s', strtotime($data->expired_date)) : NULL;
					unset($data->create_date, $data->last_update, $data->expired_date);
					$find = DB::table('mata_pelajaran')->where('mata_pelajaran_id', $data->mata_pelajaran_id)->first();
					$set_data = (array) $data;
					if($find){
						App\Mata_pelajaran::find($find->mata_pelajaran_id)->update($set_data);
						$result = 'update';
						$class = 'table-danger"';
					} else {
						App\Mata_pelajaran::create($set_data);
						$result = 'insert';
						$class = 'table-success"';
					}
			?>
				<tr class="<?php echo $class; ?>">
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $data->mata_pelajaran_id; ?></td>
					<td><?php echo $data->nama; ?></td>
					<td class="text-center"><?php echo $result; ?></td>
				</tr>
			<?php
			//}
			//break; 
			} ?>
			</tbody>
		</table>
		<div class="text-center">{{ $dapodik->links() }}</div>
@Stop
@section('js')
<script>
$(document).ready(function(){
	$('body').mouseover(function(){
		$(this).css({cursor: 'progress'});
	});
	var cari = $('body').find('a[rel=next]');
	if(cari.length>0){
		var url = $(cari).attr('href');
		console.log(url);
		window.location.replace(url);
	} else {
		window.location.replace('<?php echo url('sinkronisasi/ambil-data'); ?>');
	}
})
</script>
@Stop