<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>jalan_kelas';
	});
	
	$('#nilai').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99', mDec: '2'
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
				<a href="#"><strong>KELAS JALAN</strong></a>
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
				<label class="control-label col-sm-1" for="nama">Uraian</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="singkatan">Singkatan</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="singkatan" id="singkatan" value="<?=$dt['singkatan']?>" maxlength="2" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="kriteria">Kriteria</label>
				<div class="col-sm-2">
					<select class="form-control" name="kriteria" id="kriteria">
						<?if ($dt['kriteria']==0) {?>
						<option value=0 > JALAN </option>
						<option value=1> BANDARA </option>
						<?}else if ($dt['kriteria']==1){?>
						<option value=1> BANDARA </option>
						<option value=0 > JALAN </option>
						<?}else{?>
						<option value=0 > JALAN </option>
						<option value=1> BANDARA </option>
						<?}?>					
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="nilai">Nilai</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nilai" id="nilai" value="<?=$dt['nilai']?>" style="text-align:right;" required />
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