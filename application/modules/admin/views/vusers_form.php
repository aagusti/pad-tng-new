<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
	$(document).ready(function() {
		$('#btn_cancel').click(function() {
			window.location = "<?echo $this->uri->segment(2)=='users2' ? active_module_url() : active_module_url('users');?>";
		});
	});
</script>

<div class="content">
	<div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>USERS</strong></a>
			</li>
		</ul><br>
		
		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>
		
		<?=msg_block();?>

		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal','enctype'=>'multipart/form-data'));?>
		<input type="hidden" name="id" value="<?=$dt['id']?>"/>
		<div class="form-group">
			<label class="control-label col-sm-1">User ID</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="userid" value="<?=$dt['userid']?>" 
				<?echo $this->uri->segment(2)=='users2' ? 'readonly' : '';?>>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1">Nama</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="nama" value="<?=$dt['nama']?>">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1">Password</label>
			<div class="col-sm-3">
				<input class="form-control" type="password" name="passwd" value="<?=$dt['passwd']?>">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1">Password (Confirm)</label>
			<div class="col-sm-3">
				<input class="form-control" type="password" name="passconf">
			</div>
		</div>

		<div class="form-group <?echo $this->uri->segment(2)=='users2' ? 'hide' : '';?>">
			<label class="control-label col-sm-1">Jabatan</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="jabatan" value="<?=$dt['jabatan']?>">
			</div>
		</div>
		<div class="form-group <?echo $this->uri->segment(2)=='users2' ? 'hide' : '';?>">
			<label class="control-label col-sm-1">NIP</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="nip" value="<?=$dt['nip']?>">
			</div>
		</div>
		<div class="form-group <?echo $this->uri->segment(2)=='users2' ? 'hide' : '';?>">
			<label class="control-label col-sm-1">Handphone</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="handphone" value="<?=$dt['handphone']?>">
			</div>
		</div>

		<div class="form-group <?echo $this->uri->segment(2)=='users2' ? 'hide' : '';?>">
			<label class="control-label col-sm-1">Disabled</label>
			<div class="col-sm-3">
				<label class="checkbox">
					<input type="checkbox" name="disabled" <?=$dt['disabled']?>>
				</label>
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
