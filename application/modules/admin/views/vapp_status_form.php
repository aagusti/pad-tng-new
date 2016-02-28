<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>apps';
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<div class="page-header" style="margin-bottom:8px;">
			<strong>:: Modules - Seting Tahun Anggaran dan Step</strong>
		</div>
    </div>

    <div class="container-fluid">
		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>
	</div>

    <div class="container-fluid">
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal','enctype'=>'multipart/form-data'));?>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<input type="hidden" name="app_id" value="<?=$dt['app_id']?>"/>
			<div class="form-group">
				<label class="control-label col-sm-1">Tahun</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="tahun" value="<?=$dt['tahun']?>">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1">Step</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="step" value="<?=$dt['step']?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<button type="submit" class="btn btn-primary">Simpan</button>
					<button type="button" class="btn" id="btn_cancel">Batal</button>
				</div>
			</div>
		</form>
    </div>
</div>
<? $this->load->view('_foot'); ?>