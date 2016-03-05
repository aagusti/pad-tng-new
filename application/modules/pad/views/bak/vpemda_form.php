<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
$(document).ready(function() {	
	$('#myform').submit(function() {
		var c = confirm("Simpan perubahan?");
		return c; 
	});
	
	var tmt_dtp = $('#tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tmt_dtp.hide();
	}).data('datepicker');
	
	$('#tgl_jatuhtempo_self, #tgl_spt, #reklame_id, #airtanah_id, #self_dok_id, #office_dok_id').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99', mDec: '0'
	});
	
	$('#spt_denda, #pad_bunga').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99'
	});
	
	$("#tgl_jatuhtempo_self").tooltip({
		'placement': 'right',
		'title': 'Tanggal jatuh tempo jenis pajak Self'
	});
	$("#tgl_spt").tooltip({
		'placement': 'right',
		'title': 'Tanggal paling lambat pelaporan SPTPD'
	});
	$("#spt_denda").tooltip({
		'placement': 'right',
		'title': 'Denda keterlambatan melaporkan SPTPD (decimal)'
	});
	$("#pad_bunga").tooltip({
		'placement': 'right',
		'title': 'Bunga keterlambatan membayar pajak (decimal)'
	});
	$("#thn_ang").tooltip({
		'placement': 'right',
		'title': 'This setting is sessionable'
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
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
		
			<div class="tabbable">
				<ul id="myTab" class="nav nav-tabs">
					<li class="active"><a href="#pemda" data-toggle="tab"><strong>Pemda</strong></a></li>
					<li><a href="#sptpd" data-toggle="tab"><strong>SPTPD</strong></a></li>
					<li class="hide"><a href="#ref" data-toggle="tab"><strong>ID Referensi</strong></a></li>
					<li><a href="#srt" data-toggle="tab"><strong>Surat</strong></a></li>
					<li><a href="#ta" data-toggle="tab"><strong>Tahun Angggaran</strong></a></li>
				</ul>	
				<? 
				echo msg_block();
				if(validation_errors()){
					echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
					echo validation_errors('<small>','</small>');
					echo '</blockquote>';
				} 
				?>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="pemda">
						<input type="hidden" name="id" value="<?=$dt['id']?>"/>
						<br>
					<div class="row">
					<div class="col-xs-6">  

						<div class="form-group">
							<label class="control-label col-sm-4" for="daerah">Nama Daerah</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="daerah" id="daerah" value="<?=$dt['daerah']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="pemda_nama">Nama Pemda</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="pemda_nama" id="pemda_nama" value="<?=$dt['pemda_nama']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="pemda_nama_singkat">Singkatan</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="pemda_nama_singkat" id="pemda_nama_singkat" value="<?=$dt['pemda_nama_singkat']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="ppkd_id">ID PPKD/Unit</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" name="ppkd_id" id="ppkd_id" value="<?=$dt['ppkd_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="type">Jenis</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="type" id="type" value="<?=$dt['type']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kepala_nama">Nama Kepala</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="kepala_nama" id="kepala_nama" value="<?=$dt['kepala_nama']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="jabatan">Jabatan</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="jabatan" id="jabatan" value="<?=$dt['jabatan']?>" />
							</div>
						</div>
					</div>
					<div class="col-xs-6">  
						<div class="form-group">
							<label class="control-label col-sm-4" for="alamat">Alamat</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="alamat" id="alamat" value="<?=$dt['alamat']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="alamat_lengkap">Alamat Lengkap</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="alamat_lengkap" id="alamat_lengkap" value="<?=$dt['alamat_lengkap']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="ibukota">Ibukota</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="ibukota" id="ibukota" value="<?=$dt['ibukota']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="telp">Telp.</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" name="telp" id="telp" value="<?=$dt['telp']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="fax">Fax.</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" name="fax" id="fax" value="<?=$dt['fax']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="website">Website</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="website" id="website" value="<?=$dt['website']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="email">Email</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="email" id="email" value="<?=$dt['email']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="tmt">TMT</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>" />
							</div>
						</div>
					</div>
					</div>
					</div>

					<div class="tab-pane fade in" id="sptpd">
					<br>
						<div class="form-group">
							<label class="control-label col-sm-2" for="tgl_jatuhtempo_self">Jatuh Tempo Self</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="tgl_jatuhtempo_self" id="tgl_jatuhtempo_self" value="<?=$dt['tgl_jatuhtempo_self']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="tgl_spt">Tgl Max. Lapor SPTPD</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="tgl_spt" id="tgl_spt" value="<?=$dt['tgl_spt']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="spt_denda">Denda Telat Lapor</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="spt_denda" id="spt_denda" value="<?=$dt['spt_denda']?>" required /> %
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="pad_bunga">Denda Telat Bayar</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="pad_bunga" id="pad_bunga" value="<?=$dt['pad_bunga']?>" required /> %
							</div>
						</div>
					</div>
					
					<div class="tab-pane fade in hide" id="ref">
					<br>
						<div class="form-group">
							<label class="control-label col-sm-2" for="reklame_id">ID Pajak Reklame</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="reklame_id" id="reklame_id" value="<?=$dt['reklame_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="airtanah_id">ID Pajak Air Tanah</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="airtanah_id" id="airtanah_id" value="<?=$dt['airtanah_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="parkir_id">ID Parkir</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="parkir_id" id="parkir_id" value="<?=$dt['parkir_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="ppj_id">ID PPJ</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="ppj_id" id="ppj_id" value="<?=$dt['ppj_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="hiburan_id">ID Hiburan</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="hiburan_id" id="hiburan_id" value="<?=$dt['hiburan_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="walet_id">ID Walet</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="walet_id" id="walet_id" value="<?=$dt['walet_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="hotel_id">ID Hotel</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="hotel_id" id="hotel_id" value="<?=$dt['hotel_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="restauran_id">ID Restauran</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="restauran_id" id="restauran_id" value="<?=$dt['restauran_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="self_dok_id">ID Dokumen Self</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="self_dok_id" id="self_dok_id" value="<?=$dt['self_dok_id']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="office_dok_id">ID Dokumen Office</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="office_dok_id" id="office_dok_id" value="<?=$dt['office_dok_id']?>" required />
							</div>
						</div>
					</div>

					<div class="tab-pane fade in" id="srt">
					<br>
						<div class="form-group">
							<label class="control-label col-sm-2" for="reklame_kd">No. Surat</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="surat_no" id="surat_no" value="<?=$dt['surat_no']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="ijin_kd">Kode Ijin</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="ijin_kd" id="ijinh_kd" value="<?=$dt['ijin_kd']?>" required maxlength="10"/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="reklame_kd">Kode Reklame</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="reklame_kd" id="reklame_kd" value="<?=$dt['reklame_kd']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="airtanah_kd">Kode Air Tanah</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="airtanah_kd" id="airtanah_kd" value="<?=$dt['airtanah_kd']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="parkir_kd">Kode Parkir</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="parkir_kd" id="parkir_kd" value="<?=$dt['parkir_kd']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="ppj_kd">Kode PPJ</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="ppj_kd" id="ppj_kd" value="<?=$dt['ppj_kd']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="hiburan_kd">Kode Hiburan</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="hiburan_kd" id="hiburan_kd" value="<?=$dt['hiburan_kd']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="hotel_kd">Kode Hotel</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="hotel_kd" id="hotel_kd" value="<?=$dt['hotel_kd']?>" required maxlength="10" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="restauran_kd">Kode Restoran</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="restauran_kd" id="restauran_kd" value="<?=$dt['restauran_kd']?>" required maxlength="10" />
							</div>
						</div>
					</div>
                    
					<div class="tab-pane fade in" id="ta">
					<br>
						<div class="form-group">
							<label class="control-label col-sm-2" for="thn_ang">Tahun Anggaran</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="thn_ang" id="thn_ang" value="<?=$dt['thn_ang']?>" required />
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>