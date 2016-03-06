<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.nav-tabs > .active > a, .nav-pills > .active > a:hover {
    color: blue;
}
</style>

<script>
$(document).ready(function() {
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>subjek_pajak';
	});
});
</script>

<script>
var mID;
var oTable;
var xRow;

$(document).ready(function() {
	oTable = $('#table1').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bFilter": false,
		"bPaginate": false,
		"bInfo": false,
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": true, "aTargets": [ 0 ] }
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if(aData[0]!=xRow) {
					if ($(this).hasClass('row_selected')) {
						$(this).removeClass('row_selected');
					} else {
						oTable.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}

					var data = oTable.fnGetData( this );
					mID = data[0];
				}
				xRow = aData[0];
			})
		},
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=active_module_url();?>subjek_pajak/grid"
	}); 
});
</script>
<div class="container-fluid">
	
	<?php
	if(validation_errors()){
		echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
		echo validation_errors('<small>','</small>');
		echo '</blockquote>';
	}
	?>
	
	<?php echo form_open($faction, array('id'=>'myform','class'=>'form'));?>
		<input type="hidden" name="id" value="<?=$dt['id']?>"/>
				
		<div class="row">
			<!-- Subjek Pajak -->
			<div class="span4">					
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#"><strong>SUBJEK PAJAK</strong></a>
					</li>
				</ul>
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>No. Form</strong>
								<input class="span2" type="text" name="formno" value="<?=$dt['formno']?>" />
							</div>
							<div class="span2">
								<strong>Tanggal Terima</strong>
								<input class="span2" type="text" name="reg_date" value="<?=$dt['reg_date']?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>Pribadi/Badan</strong>
								<input class="span2" type="text" name="pb" value="<?=$dt['pb']?>" />
							</div>
							<div class="span2">
								<strong>Pajak/Retribusi</strong>
								<input class="span2" type="text" name="rp" value="<?=$dt['rp']?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span4">
								<strong>Nama Wajib Pajak</strong>
								<input class="span4" type="text" name="customernm" value="<?=$dt['customernm']?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span4">
								<strong>Alamat</strong>
								<textarea class="span4" name="alamat" rows="2" cols="50"><?=$dt['alamat']?></textarea>  
							</div>
						</div>
					</div>
				</div>
						
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>Kecamatan</strong>
								<input class="span2" type="text" name="kecamatan_id" value="<?=$dt['kecamatan_id']?>" />
							</div>
							<div class="span2">
								<strong>Kelurahan</strong>
								<input class="span2" type="text" name="kelurahan_id" value="<?=$dt['kelurahan_id']?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span4">
								<strong>Kabupaten/Kota</strong>
								<input class="span4" type="text" name="kabupaten" value="<?=$dt['kabupaten']?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>Kode Pos</strong>
								<input class="span2" type="text" name="kodepos" value="<?=$dt['kodepos']?>" />
							</div>
							<div class="span2">
								<strong>Telp.</strong>
								<input class="span2" type="text" name="telphone" value="<?=$dt['telphone']?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="span8">		
				<div class="row">
					<!-- pemilik -->
					<div class="span4">				
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#"><strong>PEMILIK</strong></a>
							</li>
						</ul>
						
						<div class="row">
							<div class="span4">
								<div class="row">
									<div class="span4">
										<strong>Nama</strong>
										<input class="span4" type="text" name="wpnama" value="<?=$dt['wpnama']?>" />
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="span4">
								<div class="row">
									<div class="span4">
										<strong>Alamat</strong>
										<textarea class="span4" name="wpalamat" rows="2" cols="50"><?=$dt['wpalamat']?></textarea>  
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="span4">
								<div class="row">
									<div class="span2">
										<strong>Kecamatan</strong>
										<input class="span2" type="text" name="wpkecamatan" value="<?=$dt['wpkecamatan']?>" />
									</div>
									<div class="span2">
										<strong>Kelurahan</strong>
										<input class="span2" type="text" name="wpkelurahan" value="<?=$dt['wpkelurahan']?>" />
									</div>
								</div>
							</div>
						</div>
								
						<div class="row">
							<div class="span4">
								<div class="row">
									<div class="span4">
										<strong>Kabupaten/Kota</strong>
										<input class="span4" type="text" name="wpkabupaten" value="<?=$dt['wpkabupaten']?>" />
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="span4">
								<div class="row">
									<div class="span2">
										<strong>Kode Pos</strong>
										<input class="span2" type="text" name="wpkodepos" value="<?=$dt['wpkodepos']?>" />
									</div>
									<div class="span2">
										<strong>Telp.</strong>
										<input class="span2" type="text" name="wptelp" value="<?=$dt['wptelp']?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!-- pengelola -->
					<div class="span4">				
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#"><strong>PENGELOLA</strong></a>
					</li>
				</ul>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span4">
								<strong>Nama</strong>
								<input class="span4" type="text" name="pnama" value="<?=$dt['pnama']?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span4">
								<strong>Alamat</strong>
								<textarea class="span4" name="palamat" rows="2" cols="50"><?=$dt['palamat']?></textarea>  
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>Kecamatan</strong>
								<input class="span2" type="text" name="pkecamatan" value="<?=$dt['pkecamatan']?>" />
							</div>
							<div class="span2">
								<strong>Kelurahan</strong>
								<input class="span2" type="text" name="pkelurahan" value="<?=$dt['pkelurahan']?>" />
							</div>
						</div>
					</div>
				</div>
						
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span4">
								<strong>Kabupaten/Kota</strong>
								<input class="span4" type="text" name="pkabupaten" value="<?=$dt['pkabupaten']?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>Kode Pos</strong>
								<input class="span2" type="text" name="pkodepos" value="<?=$dt['pkodepos']?>" />
							</div>
							<div class="span2">
								<strong>Telp.</strong>
								<input class="span2" type="text" name="ptelp" value="<?=$dt['ptelp']?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
				</div>
			</div>
			
			<!-- pengukuhan -->
			<div class="span8">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#"><strong>PENGUKUHAN</strong></a>
					</li>
				</ul>
				<div class="row">
					<div class="span2">
						<strong>No. Pengukuhan :</strong>
						<input class="span2" type="text" name="kukuhno" value="<?=$dt['kukuhno']?>" />
					</div>
					<div class="span2">
						<strong>Tanggal Pengukuhan</strong>
						<input class="span2" type="text" name="kukuhtgl" value="<?=$dt['kukuhtgl']?>" />
					</div>
					<div class="span2">
						<strong>Pejabat</strong>
						<input class="span2" type="text" name="kukuh_jabat_id" value="<?=$dt['kukuh_jabat_id']?>" />
					</div>
					<div class="span2">
						<strong>NPWPD</strong>
						<input class="span2" type="text" name="npwpd" value="<?=$dt['npwpd']?>" />
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<!-- pengukuhan -->	
			<div class="span12">		
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#"><strong>PERIZINAN</strong></a>
					</li>
				</ul>
				
				<div class="row">
					<div class="span4">
						<strong>Perizinan</strong>
						<input class="span4" type="text" name="ijin1" value="<?=$dt['ijin1']?>" />
					</div>
					<div class="span4">
						<strong>No. Perizinan</strong>
						<input class="span4" type="text" name="ijin1no" value="<?=$dt['ijin1no']?>" />
					</div>
					<div class="span2">
						<strong>Tanggal Berlaku</strong>
						<input class="span2" type="text" name="ijin1tgl" value="<?=$dt['ijin1tgl']?>" />
					</div>
					<div class="span2">
						<strong>Tanggal Berakhir</strong>
						<input class="span2" type="text" name="ijin1tglakhir" value="<?=$dt['ijin1tglakhir']?>" />
					</div>
				</div>

				<div class="row">
					<div class="span4">
						<input class="span4" type="text" name="ijin2" value="<?=$dt['ijin2']?>" />
					</div>
					<div class="span4">
						<input class="span4" type="text" name="ijin2no" value="<?=$dt['ijin2no']?>" />
					</div>
					<div class="span2">
						<input class="span2" type="text" name="ijin2tgl" value="<?=$dt['ijin2tgl']?>" />
					</div>
					<div class="span2">
						<input class="span2" type="text" name="ijin2tglakhir" value="<?=$dt['ijin2tglakhir']?>" />
					</div>
				</div>
				
				<div class="row">
					<div class="span4">
						<input class="span4" type="text" name="ijin3" value="<?=$dt['ijin3']?>" />
					</div>
					<div class="span4">
						<input class="span4" type="text" name="ijin3no" value="<?=$dt['ijin3no']?>" />
					</div>
					<div class="span2">
						<input class="span2" type="text" name="ijin3tgl" value="<?=$dt['ijin3tgl']?>" />
					</div>
					<div class="span2">
						<input class="span2" type="text" name="ijin3tglakhir" value="<?=$dt['ijin3tglakhir']?>" />
					</div>
				</div>

				<div class="row">
					<div class="span4">
						<input class="span4" type="text" name="ijin4" value="<?=$dt['ijin4']?>" />
					</div>
					<div class="span4">
						<input class="span4" type="text" name="ijin4no" value="<?=$dt['ijin4no']?>" />
					</div>
					<div class="span2">
						<input class="span2" type="text" name="ijin4tgl" value="<?=$dt['ijin4tgl']?>" />
					</div>
					<div class="span2">
						<input class="span2" type="text" name="ijin4tglakhir" value="<?=$dt['ijin4tglakhir']?>" />
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<!-- jenis objek pajak -->	
			<div class="span12">		
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#"><strong>OBJEK PAJAK</strong></a>
					</li>
				</ul>
				
				<div class="row">
					<div class="span4">
						<table class="table" id="table1">
							<thead>
								<tr>
									<th>No.</th>
									<th>Keterangan</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					
					<div class="span4">
						<div class="row">
							<div class="span2">
								<strong>No. Urut</strong>
								<input class="span2" type="text" name="ijin1" value="<?=$dt['ijin1']?>" />
							</div>
							<div class="span2">
								<strong>Tanggal Daftar</strong>
								<input class="span2" type="text" name="ijin1" value="<?=$dt['ijin1']?>" />
							</div>
						</div>
						
						<div class="row">
							<div class="span4">
								<strong>Jenis Usaha</strong>
								<input class="span4" type="text" name="ijin1no" value="<?=$dt['ijin1no']?>" />
							</div>
						</div>
						
						<div class="row">
							<div class="span2">
								<strong>Kecamatan</strong>
								<input class="span2" type="text" name="ijin1tgl" value="<?=$dt['ijin1tgl']?>" />
							</div>
							<div class="span2">
								<strong>Kelurahan</strong>
								<input class="span2" type="text" name="ijin1tglakhir" value="<?=$dt['ijin1tglakhir']?>" />
							</div>
						</div>
						
						<div class="row">
							<div class="span4">
								<strong>Keterangan</strong>
								<input class="span4" type="text" name="ijin1no" value="<?=$dt['ijin1no']?>" />
							</div>
						</div>
						
					</div>
				</div>
				
			</div>
		</div>
		
		<!-- 
						
				<input class="form-control" type="text" name="reg_kelurahan_id" value="<?=$dt['reg_kelurahan_id']?>" />
				<input class="form-control" type="text" name="customer_status_id" value="<?=$dt['customer_status_id']?>" />
				<input class="form-control" type="text" name="kirimtgl" value="<?=$dt['kirimtgl']?>" />
				<input class="form-control" type="text" name="batastgl" value="<?=$dt['batastgl']?>" />

				<input class="form-control" type="text" name="penerimanm" value="<?=$dt['penerimanm']?>" />
				<input class="form-control" type="text" name="penerimaalamat" value="<?=$dt['penerimaalamat']?>" />
				<input class="form-control" type="text" name="penerimatgl" value="<?=$dt['penerimatgl']?>" />
				<input class="form-control" type="text" name="kembalitgl" value="<?=$dt['kembalitgl']?>" />
				<input class="form-control" type="text" name="kembalioleh" value="<?=$dt['kembalioleh']?>" />
				<input class="form-control" type="text" name="kukuhprinted" value="<?=$dt['kukuhprinted']?>" />
				<input class="form-control" type="text" name="kartuprinted" value="<?=$dt['kartuprinted']?>" />
				<input class="form-control" type="text" name="id" value="<?=$dt['id']?>" />
				<input class="form-control" type="text" name="tmt" value="<?=$dt['tmt']?>" />
				<input class="form-control" type="text" name="enabled" value="<?=$dt['enabled']?>" />
				<input class="form-control" type="text" name="create_date" value="<?=$dt['create_date']?>" />
				<input class="form-control" type="text" name="create_uid" value="<?=$dt['create_uid']?>" />
				<input class="form-control" type="text" name="write_date" value="<?=$dt['write_date']?>" />
				<input class="form-control" type="text" name="write_uid" value="<?=$dt['write_uid']?>" />
				<input class="form-control" type="text" name="kukuhnip" value="<?=$dt['kukuhnip']?>" />
				<input class="form-control" type="text" name="kembalinip" value="<?=$dt['kembalinip']?>" />
				<input class="form-control" type="text" name="catatnip" value="<?=$dt['catatnip']?>" />
				<input class="form-control" type="text" name="petugas_jabat_id" value="<?=$dt['petugas_jabat_id']?>" />
				<input class="form-control" type="text" name="pencatat_jabat_id" value="<?=$dt['pencatat_jabat_id']?>" />
				<input class="form-control" type="text" name="parent" value="<?=$dt['parent']?>" />
		-->
		<div class="form-group">
			<div class="col-sm-4">
				<button type="submit" class="btn btn-primary">Simpan</button>
				<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
			</div>
		</div>
	<?php echo form_close();?>
</div>
<? $this->load->view('_foot'); ?>