<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>


<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>air_sub_kelompok_usaha/index/<?=$this->session->userdata("induk_id")?>';
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
				<a href="#"><strong>AIR - SUB KELOMPOK USAHA</strong></a>
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
				<label class="control-label col-sm-2" for="kelompok_usaha">Kelompok Usaha</label>
				<div class="col-sm-4">
					<?=$select_kelompok_usaha;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="kode">Kode</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="kode" id="kode" value="<?=$dt['kode']?>" maxlength="8" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="nama">Uraian</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required />
				</div>
			</div>
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>