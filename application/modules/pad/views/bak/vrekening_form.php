<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
    .form-control {
        height: 24px;
        padding: 0px 12px;
        font-size: 12px;
    }

    .form-group {
        margin-bottom: 5px;
    }

    .form-horizontal 
    .control-label {
        padding-top: 3px;
    }
</style>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>rekening';
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
				<a href="#"><strong>REKENING</strong></a>
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

		<br>
		<div class="box box-primary box-solid">
		<div class="box-body">
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<div class="form-group">
				<label class="control-label col-sm-1 col-sm-1" for="kode">Kode</label>
				<div class="col-sm-1">
					<input class="form-control" type="text" name="kode" id="kode" value="<?=$dt['kode']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1 col-sm-1" for="nama">Rekening</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="levelid">Level</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="levelid" id="levelid" value="<?=$dt['levelid']?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="defsign">Defsign</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="defsign" id="defsign" value="<?=$dt['defsign']?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="issummary">Summary</label>
				<div class="col-sm-4">
					<input type="checkbox" name="issummary" <?=$dt['issummary']?>>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="isppkd">PPKD</label>
				<div class="col-sm-4">
					<input type="checkbox" name="isppkd" <?=$dt['isppkd']?>>
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
		</div>
		</div>	
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>