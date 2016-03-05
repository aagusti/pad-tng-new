<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>


<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>air_detail_kelompok_usaha/index/<?=$this->session->userdata("induk_id")?>';
	});
	
	$('#btn_filter_back').click(function() {;
		window.location = '<?=active_module_url();?>air_kelompok_usaha/';
	});
});


function get_sub_kelompok_usaha(induk_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>air_sub_kelompok_usaha/get_sub_kelompok_usaha/"+induk_id,
		async: false,
		success: function (data) {
			$('#induk_id').html(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}


$(document).ready(function() {

$('#induk_id1').change(function() {
	get_sub_kelompok_usaha(this.value);
});

});

$(window).load(function() {
	get_sub_kelompok_usaha(document.getElementById('induk_id1').value);
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
				<a href="#"><strong>AIR - DETAIL KELOMPOK USAHA</strong></a>
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
				<label class="control-label col-sm-2" for="kelompok_usaha">Detail Kelompok Usaha</label>
				<div class="col-sm-4">
					<?=$select_detail_kelompok_usaha;?>
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