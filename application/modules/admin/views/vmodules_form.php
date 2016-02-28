<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>privileges';
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>MODULE</strong></a>
			</li>
		</ul>
		
		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal','enctype'=>'multipart/form-data'));?>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<input type="hidden" name="app_id" value="<?=$dt['app_id']?>"/>
			<div class="form-group">
				<label class="control-label col-sm-1">Kode</label>
				<div class="col-sm-3">
					<input class="input-small" type="text" name="kode" value="<?=$dt['kode']?>">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1">Module</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="nama" value="<?=$dt['nama']?>">
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