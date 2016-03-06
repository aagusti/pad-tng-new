<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
function show_rpt(){
	var rptparams;
	var rpt = $(".nav-list li.active").data('rpt');
	switch(rpt) {
		case 'trm_tgl':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kondisi:  '',
			}
			break;
		case 'trm_masa':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kondisi:  1,
			}
			break;
		case 'trm_tgl_jenis':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				usaha_id:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'trm_rekap':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				usaha_id:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'trm_piutang':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				usaha_id:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'trm_masa_jenis':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kondisi:  3,
			}
			break;
		case 'trm_tgl_reklame':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kondisi:  8,
			}
			break;
		case 'trm_tgl_air':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kondisi:  15,
			}
			break;
		case 'trm_blm_dialokasikan':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'trm_sdh_dialokasikan':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
            
		case 'lra_harian':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
			}
			break;
		case 'lra_mingguan':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				minggu:  $("div.tab-pane.active #minggu").val(),
			}
			break;
		case 'lra_bulanan':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				bulan:  $("div.tab-pane.active #bulan").val(),
			}
			break;
            
		case 'lra_harian_objek':
			rptparams = {
				rpt: 'lra_harian',
				kondisi: 'perobjek',
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
			}
			break;
		case 'lra_mingguan_objek':
			rptparams = {
				rpt: 'lra_mingguan',
				kondisi: 'perobjek',
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				minggu:  $("div.tab-pane.active #minggu").val(),
			}
			break;
		case 'lra_bulanan_objek':
			rptparams = {
				rpt: 'lra_bulanan',
				kondisi: 'perobjek',
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				bulan:  $("div.tab-pane.active #bulan").val(),
			}
			break;
		default:
			rptparams = {
				rpt: rpt,
				nama: "khairul anwar",
			}
	}
	
	/* 
	var data = $.param(rptparams);
	var data = encodeURIComponent(decodeURIComponent($.param(rptparams)));
	*/
	var data = decodeURIComponent($.param(rptparams));
	// var url  = '<?=active_module_url($this->router->fetch_class());?>show_rpt/?'+data;
	var url  = '<?=active_module_url($this->router->fetch_class());?>cetak/pdf/?'+data;
	
	var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
	window.open(url, 'Laporan', winparams);
}

$(document).ready(function() {
	var tglcetak = $("[id=tglcetak]").datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		$("[id=tglcetak]").datepicker('hide');
	}).data('datepicker');

	var tglawal = $('[id=tglawal]').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		$("[id=tglawal]").datepicker('hide');
		$('div.tab-pane.active #tglakhir')[0].focus();
	}).data('datepicker');
	
	var tglakhir = $('[id=tglakhir]').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		$("[id=tglakhir]").datepicker('hide');
	}).data('datepicker');
	
	$("[id=btnshow_rpt]").click(function(){
		show_rpt();
	});
	
});
</script>
<div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>LAPORAN PENERIMAAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="col-sm-12">
		<p>&nbsp;</p>
			<div class="col-sm-4">
				<div class="sidebar-nav">
					<ul class="nav nav-list">
						<!--li class="nav-header">Laporan</li-->
						<li data-rpt="trm_tgl" class="active"><a href="#tabs-global" data-toggle="tab">Penerimaan Per Tanggal</a></li>
						<li data-rpt="trm_tgl_jenis"><a href="#tabs-trm_tgl_jenis" data-toggle="tab">Penerimaan Per Tanggal Per Jenis Usaha</a></li>
						<li data-rpt="trm_rekap"><a href="#tabs-trm_rekap" data-toggle="tab">Rekapitulasi Realisasi Per Wajib Pajak</a></li>
						<li data-rpt="trm_piutang"><a href="#tabs-trm_piutang" data-toggle="tab">Realisasi dan Piutang Per Wajib Pajak</a></li>
						<!--li data-rpt="trm_masa"><a href="#tabs-global" data-toggle="tab">Penerimaan Per Masa Pajak Global (Self Assesment)</a></li>
						<li data-rpt="trm_masa_jenis"><a href="#tabs-global" data-toggle="tab">Penerimaan Per Masa Pajak Per Jenis Usaha</a></li>
						<li data-rpt="trm_tgl_reklame"><a href="#tabs-global" data-toggle="tab">Penerimaan Per Tanggal (Reklame)</a></li>
						<li data-rpt="trm_tgl_air"><a href="#tabs-global" data-toggle="tab">Penerimaan Per Tanggal (Air Bawah Tanah)</a></li>
						<li data-rpt="trm_blm_dialokasikan"><a href="#tabs-global" data-toggle="tab">Daftar Penerimaan Bulanan (Belum dialokasikan)</a></li>
						<li data-rpt="trm_sdh_dialokasikan"><a href="#tabs-global" data-toggle="tab">Daftar Penerimaan Bulanan (Sudah dialokasikan)</a></li-->
                        <li class="nav-header">Realisasi Anggaran (Per Objek)</li>
						<li data-rpt="lra_harian_objek"><a href="#tabs-lra_harian" data-toggle="tab">Realisasi Harian</a></li>
						<li data-rpt="lra_mingguan_objek"><a href="#tabs-lra_mingguan" data-toggle="tab">Realisasi Mingguan</a></li>
						<li data-rpt="lra_bulanan_objek"><a href="#tabs-lra_bulanan" data-toggle="tab">Realisasi Bulanan</a></li>
                        <li class="nav-header">Realisasi Anggaran (Per Rincian Objek)</li>
						<li data-rpt="lra_harian"><a href="#tabs-lra_harian" data-toggle="tab">Realisasi Harian</a></li>
						<li data-rpt="lra_mingguan"><a href="#tabs-lra_mingguan" data-toggle="tab">Realisasi Mingguan</a></li>
						<li data-rpt="lra_bulanan"><a href="#tabs-lra_bulanan" data-toggle="tab">Realisasi Bulanan</a></li>
					</ul>
				</div>
			</div>

			<div class="col-sm-8">			
				<div class="tab-content">
				
					<div class="tab-pane active" id="tabs-global">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Periode</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
                    
					<div class="tab-pane" id="tabs-trm_tgl_jenis">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Periode</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Usaha</label> 
								<div class="col-sm-4">
									<?=$select_usaha;?>
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
                    
					<div class="tab-pane" id="tabs-trm_rekap">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Usaha</label> 
								<div class="col-sm-4">
									<?=$select_usaha;?>
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
                    
					<div class="tab-pane" id="tabs-trm_piutang">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Periode Masa Pajak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Usaha</label> 
								<div class="col-sm-4">
									<?=$select_usaha;?>
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
                    
					<div class="tab-pane" id="tabs-lra_harian">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
                    
					<div class="tab-pane" id="tabs-lra_mingguan">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Minggu</label> 
								<div class="col-sm-4">
                                    <select class="form-control" id="minggu" name="minggu">
                                        <? 
                                        $i=1; while($i<63) { 
                                            echo "<option value={$i}>Minggu Ke {$i}</option>";
                                            $i++;
                                        }; 
                                        ?>
                                    </select>
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-lra_bulanan">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan</label> 
								<div class="col-sm-4">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value=1>Januari</option>
                                        <option value=2>Pebruari</option>
                                        <option value=3>Maret</option>
                                        <option value=4>April</option>
                                        <option value=5>Mei</option>
                                        <option value=6>Juni</option>
                                        <option value=7>Juli</option>
                                        <option value=8>Agustus</option>
                                        <option value=9>September</option>
                                        <option value=10>Oktober</option>
                                        <option value=11>Nopember</option>
                                        <option value=12>Desember</option>
                                    </select>
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-4">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt">Lihat Laporan</button>
								</div>
							</div>
						</div>
					</div>
                    
				</div>
			</div>
		</div>

	</div>
</div>
<? $this->load->view('_foot'); ?>