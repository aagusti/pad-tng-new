<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>kecamatan';
	});
	
	var tmt_dtp = $('#tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tmt_dtp.hide();
	}).data('datepicker');
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
				<a href="#"><strong>KECAMATAN</strong></a>
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
				<label class="control-label col-sm-1" for="kode">Kode</label>
				<div class="col-sm-1">
					<input class="form-control" type="text" name="kode" id="kode" value="<?=$dt['kode']?>" maxlength="3" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="nama">Kecamatan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="zonanm">Zona</label>
				<div class="col-sm-4">
					<?=$select_air_zona;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="tmt">TMT</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="enabled">Aktif</label>
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