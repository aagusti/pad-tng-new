<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.nav > li > a:focus, .nav > li > a:hover {
    text-decoration: none;
    background-color: #2C3B41;
}
</style>

<script>
var jenisfile;
function show_rpt(){
	var rptparams;
	var rpt = $(".nav-list li.active").data('rpt');
	switch(rpt) {
		case 'dat_sptpd_masuk':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'dat_sptpd_masuk_masa':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'dat_sptpd_masuk_tgl_all':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'dat_sptpd_masuk_masa_all':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
			}
			break;
		case 'dat_sptpd_blm_masuk_masa':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'dat_sptpd_blm_masuk_masa_all':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
			}
			break;
		case 'dat_srt_pemberitahuan':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'dat_srt_teguran':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'dat_srt_teguran_global':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
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

	if (jenisfile == 'pdf'){
		var url  = '<?=active_module_url($this->router->fetch_class());?>cetak/pdf/?'+data;
	}
	else if (jenisfile == 'xls'){
		var url  = '<?=active_module_url($this->router->fetch_class());?>cetak/html/?'+data;
	}

	
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
		jenisfile = 'pdf';
		show_rpt();
	});

	$("[id=btnshow_rpt_xls]").click(function(){
		jenisfile = 'xls';
		show_rpt();
	});
	
	
});
</script>
<div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>LAPORAN PENDATAAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="col-sm-12">
		<p>&nbsp;</p>
			<div class="col-sm-4">
				<div class="sidebar-nav">
					<ul class="nav nav-list">
						<li class="nav-header">Daftar SPTPD Masuk</li>
						<li data-rpt="dat_sptpd_masuk" class="active"><a href="#tabs-dat_sptpd_masuk" data-toggle="tab">Daftar SPTPD Masuk</a></li>
						<li data-rpt="dat_sptpd_masuk_masa"><a href="#tabs-dat_sptpd_masuk_masa" data-toggle="tab">Daftar SPTPD Masuk (Masa Pajak)</a></li>
						<!--li data-rpt="dat_sptpd_masuk_tgl_all"><a href="#tabs-dat_sptpd_masuk_tgl_all" data-toggle="tab">Daftar SPTPD (Global Per Tanggal SPT)</a></li>
						<li data-rpt="dat_sptpd_masuk_masa_all"><a href="#tabs-dat_sptpd_masuk_masa_all" data-toggle="tab">Daftar SPTPD (Global Per Masa Pajak)</a></li-->
						<li class="nav-header">Daftar SPTPD Belum Masuk</li>
						<li data-rpt="dat_sptpd_blm_masuk_masa"><a href="#tabs-dat_sptpd_blm_masuk_masa" data-toggle="tab">WP Belum Menyampaikan SPTPD</a></li>
						<!--li data-rpt="dat_sptpd_blm_masuk_masa_all"><a href="#tabs-dat_sptpd_blm_masuk_masa_all" data-toggle="tab">Daftar SPTPD (Global Per Masa Pajak)</a></li-->
						<li class="nav-header">Surat Pemberitahuan & Teguran</li>
						<li data-rpt="dat_srt_pemberitahuan"><a href="#tabs-dat_srt_pemberitahuan" data-toggle="tab">Surat Pemberitahuan Penyampaian SPTPD</a></li>
						<li data-rpt="dat_srt_teguran"><a href="#tabs-dat_srt_teguran" data-toggle="tab">Surat Teguran Penyampaian SPTPD</a></li>
						<li data-rpt="dat_srt_teguran_global"><a href="#tabs-dat_srt_teguran_global" data-toggle="tab">Surat Teguran Penyampaian SPTPD (Semua Masa Pajak)</a></li>
					</ul>
				</div>
			</div>

			<div class="col-sm-8">		
				<div class="tab-content">
					<div class="tab-pane active" id="tabs-dat_sptpd_masuk">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Periode</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control"  id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
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
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_sptpd_masuk_masa">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Masa Pajak</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option>
										<option value="2">PEBRUARI</option>
										<option value="3">MARET</option>
										<option value="4">APRIL</option>
										<option value="5">MEI</option>
										<option value="6">JUNI</option>
										<option value="7">JULI</option>
										<option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option>
										<option value="10">OKTOBER</option>
										<option value="11">NOVEMBER</option>
										<option value="12">DESEMBER</option>
									</select>	
									<input class="form-control" id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
				
					<div class="tab-pane" id="tabs-dat_sptpd_masuk_tgl_all">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Periode</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control"  id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_sptpd_masuk_masa_all">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option>
										<option value="2">PEBRUARI</option>
										<option value="3">MARET</option>
										<option value="4">APRIL</option>
										<option value="5">MEI</option>
										<option value="6">JUNI</option>
										<option value="7">JULI</option>
										<option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option>
										<option value="10">OKTOBER</option>
										<option value="11">NOVEMBER</option>
										<option value="12">DESEMBER</option>
									</select>	
									<input class="form-control" id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_sptpd_blm_masuk_masa">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Masa Pajak</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option>
										<option value="2">PEBRUARI</option>
										<option value="3">MARET</option>
										<option value="4">APRIL</option>
										<option value="5">MEI</option>
										<option value="6">JUNI</option>
										<option value="7">JULI</option>
										<option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option>
										<option value="10">OKTOBER</option>
										<option value="11">NOVEMBER</option>
										<option value="12">DESEMBER</option>
									</select>	
									<input class="form-control" id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_sptpd_blm_masuk_masa_all">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option>
										<option value="2">PEBRUARI</option>
										<option value="3">MARET</option>
										<option value="4">APRIL</option>
										<option value="5">MEI</option>
										<option value="6">JUNI</option>
										<option value="7">JULI</option>
										<option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option>
										<option value="10">OKTOBER</option>
										<option value="11">NOVEMBER</option>
										<option value="12">DESEMBER</option>
									</select>	
									<input class="form-control" id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div>
							&nbsp;
							<div class="form-group">
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_srt_pemberitahuan">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Masa Pajak</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option>
										<option value="2">PEBRUARI</option>
										<option value="3">MARET</option>
										<option value="4">APRIL</option>
										<option value="5">MEI</option>
										<option value="6">JUNI</option>
										<option value="7">JULI</option>
										<option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option>
										<option value="10">OKTOBER</option>
										<option value="11">NOVEMBER</option>
										<option value="12">DESEMBER</option>
									</select>	
									<input class="form-control" id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_srt_teguran">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Masa Pajak</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option>
										<option value="2">PEBRUARI</option>
										<option value="3">MARET</option>
										<option value="4">APRIL</option>
										<option value="5">MEI</option>
										<option value="6">JUNI</option>
										<option value="7">JULI</option>
										<option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option>
										<option value="10">OKTOBER</option>
										<option value="11">NOVEMBER</option>
										<option value="12">DESEMBER</option>
									</select>	
									<input class="form-control" id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tabs-dat_srt_teguran_global">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
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
								<div class="col-sm-2">
									<button id="btnshow_rpt" class="btn btn-primary" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (PDF)</button>
								</div>
								<div class="col-sm-1">
								</div>
								<div class="col-sm-2">
									<button id="btnshow_rpt_xls" class="btn btn-success" name="btnshow_rpt"><i class="fa fa-print"></i> CETAK (EXCEL)</button>
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