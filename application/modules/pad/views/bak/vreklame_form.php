<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>
<style type="text/css">
.form-control {
  height: 30px;
  font-size: 12px;
}
.form-group {
  margin-bottom: 15px;
}
</style>
	
<script>
function get_kelurahan(kec_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>subjek_pajak/get_kelurahan/"+kec_id,
		success: function (j) {
			var data = $.parseJSON(j);
			var select = $('#kelurahan_id');

			select.html("");
			$.each(data, function(i, val){
				select.append($('<option />', { value: val['id'], text: val['nama'] }));
			});
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}



$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url($controller);?>';
	});
	
	/* init page */
	$('#latitude, #longitude').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '9999.999999'
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
				<a href="#"><strong>MASTER DATA REKLAME</strong></a>
			</li>
		</ul>

		<?php
		echo msg_block();
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		}
		?>

		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
			<input type="hidden" name="id" class="form-control" value="<?=$dt['id']?>"/>
			
			<div class="form-group">
				<label class="control-label col-sm-2" for="kode">Kode</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="kode" id="kode" value="<?=$dt['kode'];?>" />
				</div>
			</div>
            
			<div class="form-group">
				<label class="control-label col-sm-2" for="kecamatan_id">Kecamatan</label>
				<div class="col-sm-4">
					<?echo $select_kecamatan;?>
				</div>
			</div>
            
			<div class="form-group">
				<label class="control-label col-sm-2" for="kelurahan_id">Kelurahan</label>
				<div class="col-sm-4">
					<?echo $select_kelurahan;?>
				</div>
			</div>
						
			<div class="form-group">
				<label class="control-label col-sm-2" for="latitude">Kordinat Peta</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="latitude" id="latitude" value="<?=$dt['latitude'];?>" />,
					<input type="text" class="form-control" name="longitude" id="longitude" value="<?=$dt['longitude'];?>" />				
                </div>
			</div>
            
            <p>&nbsp;</p>
                        
			<div class="form-group">
				<label class="control-label col-sm-2" for="pemilik_nama">Pemilik</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="pemilik_nama" id="pemilik_nama" value="<?=$dt['pemilik_nama'];?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-2" for="pemilik_alamat">Alamat Pemilik</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="pemilik_alamat" id="pemilik_alamat" value="<?=$dt['pemilik_alamat'];?>" />
				</div>
			</div>
            
			<div class="form-group">
				<label class="control-label col-sm-2" for="pemilik_kecamatan">Kecamatan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="pemilik_kecamatan" id="pemilik_kecamatan" value="<?=$dt['pemilik_kecamatan'];?>" />
				</div>
			</div>
            
			<div class="form-group">
				<label class="control-label col-sm-2" for="pemilik_kelurahan">Kelurahan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="pemilik_kelurahan" id="pemilik_kelurahan" value="<?=$dt['pemilik_kelurahan'];?>" />
				</div>
			</div>
            
			<div class="form-group">
				<label class="control-label col-sm-2" for="pemilik_kota">Kabupaten/Kota</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="pemilik_kota" id="pemilik_kota" value="<?=$dt['pemilik_kota'];?>" />
				</div>
			</div>
            
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
	</div>
</div>
<? $this->load->view('_foot'); ?>
