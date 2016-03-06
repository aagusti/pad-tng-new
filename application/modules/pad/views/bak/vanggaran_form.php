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
		window.location = '<?=active_module_url();?>anggaran';
	});
	
	$('#target, #jumlah').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99', mDec: '0'
	});

for(var i=1; i<=12; i++){ 
$('#bulan'+i).keyup(function() {
	hitung_jumlah();
});


$('#bulan'+i).autoNumeric('init', {
	aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});

}

function hitung_jumlah() {
	var bulan=[12];
	var jumlah = 0;
	for(var i=1; i<=12; i++){
	bulan[i] =parseFloat($('#bulan'+i).autoNumeric('get'));
	jumlah += bulan[i] << 0;
	}
	if (jumlah > 0 ){
		document.getElementById("jumlah").readOnly = true;
		document.getElementById("jumlah").value=jumlah;
	}
	else
		document.getElementById("jumlah").readOnly = false;
}


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
				<a href="#"><strong>ANGGARAN TAHUN <?=pad_tahun_anggaran();?></strong></a>
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
				<label class="control-label col-sm-2" for="rekening_id">Rekening</label>
				<div class="col-sm-4">
					<?=$select_rekening;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="tahun">Tahun Anggaran</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" maxlength="4" name="tahun" id="tahun" value="<?
					 if ($dt['tahun']!='') echo $dt['tahun']; else echo pad_tahun_anggaran();?>" style="text-align:right;" required />
				</div>
			</div>
			<div class="form-group hidden">
				<label class="control-label col-sm-2" for="status_anggaran">Status Anggaran</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="status_anggaranx" id="status_anggaranx" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="target">Status Anggaran</label>
				<div class="col-sm-3">
					<select class="form-control" name="status_anggaran" id="status_anggaran">
					<option value=0> Murni</option>
					<option value=1> Perubahan</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="target">Target</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="target" id="target" value="<?=$dt['target']?>" style="text-align:right;" required />
				</div>
			</div>
			<br>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan1">Bulan 1</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan1" id="bulan1" value="<?=$dt['bulan1']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan2">Bulan 2</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan2" id="bulan2" value="<?=$dt['bulan2']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan3">Bulan 3</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan3" id="bulan3" value="<?=$dt['bulan3']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan4">Bulan 4</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan4" id="bulan4" value="<?=$dt['bulan4']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan5">Bulan 5</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan5" id="bulan5" value="<?=$dt['bulan5']?>" style="text-align:right;"  />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan6">Bulan 6</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan6" id="bulan6" value="<?=$dt['bulan6']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan7">Bulan 7</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan7" id="bulan7" value="<?=$dt['bulan7']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan8">Bulan 8</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan8" id="bulan8" value="<?=$dt['bulan8']?>" style="text-align:right;"  />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan9">Bulan 9</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan9" id="bulan9" value="<?=$dt['bulan9']?>" style="text-align:right;"  />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan10">Bulan 10</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan10" id="bulan10" value="<?=$dt['bulan10']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan11">Bulan 11</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan11" id="bulan11" value="<?=$dt['bulan11']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="bulan12">Bulan 12</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="bulan12" id="bulan12" value="<?=$dt['bulan12']?>" style="text-align:right;" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="jumlah">Jumlah</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="jumlah" id="jumlah" value="<?=$dt['jumlah']?>" style="text-align:right;" />
				</div>
			</div>


			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>