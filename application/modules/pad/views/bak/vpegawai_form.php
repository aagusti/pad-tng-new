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
		window.location = '<?=active_module_url();?>pegawai';
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
				<a href="#"><strong>PEGAWAI</strong></a>
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
				<label class="control-label col-sm-1" for="nip">NIP</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="nip" id="nip" value="<?=$dt['nip']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="nama">Nama</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="jabatan">Jabatan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="jabatan" id="jabatan" value="<?=$dt['jabatan']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="golongan">Golongan</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="golongan" id="golongan" value="<?=$dt['golongan']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="pangkat">Pangkat</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="pangkat" id="pangkat" value="<?=$dt['pangkat']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="pangkat">Bagian</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bagian" id="bagian" value="<?=$dt['bagian']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="nomor_telp">Telp.</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="nomor_telp" id="nomor_telp" value="<?=$dt['nomor_telp']?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="tmt">TMT</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="enabled">Aktif</label>
				<div class="col-sm-5">
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