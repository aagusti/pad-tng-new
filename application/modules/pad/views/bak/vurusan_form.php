<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>urusan';
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<div class="page-header" style="margin-bottom:8px;">
			<strong>:: Urusan</strong>
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
			<div class="form-group">
				<label class="control-label col-sm-1">Kode</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="kode" value="<?=$dt['kode']?>">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1">Uraian</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nama" value="<?=$dt['nama']?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-4">
					<button type="submit" class="btn btn-primary">Simpan</button>
					<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
				</div>
			</div>
		</form>
    </div>
</div>
<? $this->load->view('_foot'); ?>