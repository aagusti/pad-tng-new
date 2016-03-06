<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {

	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>reklame_nsr';
	});
	
	$('#njopr, #nspr, #nsr').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99', mDec: '0'
	});

	var tmt_dtp = $('#tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tmt_dtp.hide();
	}).data('datepicker');

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			//document.getElementById('jenis_pajak_id').value = 33;
			document.getElementById('jenis_pajak_id').value = mID;
			document.getElementById('jenis_pajak').value = jenis_pajak;
			$('#cuDialog').modal('hide');
		}
	});
});

$(document).keypress(function(event){
	if (event.which == '13') {
		event.preventDefault();
	}
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>NILAI SEWA REKLAME</strong></a>
			</li>
		</ul>
		<? 
		echo msg_block();
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} 
		?>
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<div class="form-group">
				<label class="control-label col-sm-2" for="kelas_jalan_id">Kelas Jalan</label>
				<div class="col-sm-4">
					<?=$select_jalan_kelas;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="media_id">Media</label>
				<div class="col-sm-4">
					<?=$select_media;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="satuan">Satuan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="satuan" id="satuan" value="<?=$dt['satuan']?>" style="text-align:right" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="njopr">NJOPR</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="njopr" id="njopr" value="<?=$dt['njopr']?>"  style="text-align:right" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="nspr">NSPR</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nspr" id="nspr" value="<?=$dt['nspr']?>"  style="text-align:right" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="nsr">NSR</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nsr" id="nsr" value="<?=$dt['nsr']?>"  style="text-align:right" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="tmt">TMT</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>"  style="text-align:right" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="enabled">Aktif</label>
				<div class="col-sm-4">
					<input type="checkbox" name="enabled" <?=$dt['enabled']?>>
				</div>
			</div>
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>