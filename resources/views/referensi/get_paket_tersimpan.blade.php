<?php foreach($paket_ukk as $paket){ ?>
	<tr class="prepend">
		<td><input type="text" class="form-control" value="<?php echo $paket->nomor_paket; ?>" readonly="" /></td>
		<td><input type="text" class="form-control" value="<?php echo $paket->nama_paket; ?>" readonly="" /></td>
		<td><input type="text" class="form-control" value="<?php echo ($paket->status == 1) ? 'Aktif' : 'Tidak Aktif'; ?>" readonly="" /></td>
	</tr>
<?php } ?>