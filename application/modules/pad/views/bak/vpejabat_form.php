<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.nav-tabs > .active > a, .nav-pills > .active > a:hover {
    color: blue;
}
.form-horizontal .controls {
  margin-left: 120px;  /* changed from 180px to 140px */
}
.form-horizontal .control-group {
    margin-bottom: 1px;
}
.form-horizontal .control-label{
	text-align:left;
	width: 120px; /* changed from 160px to 120px */
}
.form-horizontal input  {
	height: 14px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}
.form-horizontal select  {
	height: 24px !important;
	padding: 2px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}

button {
	height: 24px !important;
	padding: 4px 8px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}

hr {
  border: 0;
  border-bottom: 1px solid #dddddd;
}
</style>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>pejabat';
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
				<a href="#"><strong>PEJABAT</strong></a>
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
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<div class="control-group">
				<label class="control-label" for="nip">NIP</label>
				<div class="controls">
					<input class="input-medium" type="text" name="nip" id="nip" value="<?=$dt['nip']?>" required />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="pejabatnm">Nama</label>
				<div class="controls">
					<input class="input" type="text" name="pejabatnm" id="pejabatnm" value="<?=$dt['pejabatnm']?>" required />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="jabatan">Jabatan</label>
				<div class="controls">
					<input class="input-xxlarge" type="text" name="jabatan" id="jabatan" value="<?=$dt['jabatan']?>" required />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="golongan">Golongan</label>
				<div class="controls">
					<input class="input-mini" type="text" name="golongan" id="golongan" value="<?=$dt['golongan']?>" required />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="pangkat">Pangkat</label>
				<div class="controls">
					<input class="input-xlarge" type="text" name="pangkat" id="pangkat" value="<?=$dt['pangkat']?>" required />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="tmt">TMT</label>
				<div class="controls">
					<input class="input-small" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="enabled">Aktif</label>
				<div class="controls">
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