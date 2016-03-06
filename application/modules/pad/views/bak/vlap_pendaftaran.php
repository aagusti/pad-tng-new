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
		case 'daf_induk_wp':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_induk_wp_perjenis':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_induk_wp_perkec':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				kecid:    $("div.tab-pane.active #kecid").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_wp_baru':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_wp_baru_jenis':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_wp_baru_kec':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				kecid:    $("div.tab-pane.active #kecid").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_wp_jenis_perkec':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
			}
			break;
		case 'daf_wp_tutup':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'daf_wp_ijin_berakhir':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'daf_rekap_kec':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
			}
			break;
		default:
			rptparams = {
				rpt: rpt,
				nama: "khairul anwar",
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				kondisi:  $("div.tab-pane.active #kondisi").val(),
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
				<a href="#"><strong>LAPORAN PENDAFTARAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="col-sm-12">
		<p>&nbsp;</p>
			<div class="col-sm-4">
				<div class="sidebar-nav">
					<ul class="nav nav-list">
						<!--li class="nav-header">Sidebar</li-->
						<li data-rpt="daf_induk_wp" class="active"><a href="#tabs-daf_induk_wp" data-toggle="tab">Daftar Induk WP</a></li>
						<li data-rpt="daf_induk_wp_perjenis"><a href="#tabs-daf_induk_wp_perjenis" data-toggle="tab">Daftar Induk WP Per Jenis Usaha</a></li>
						<li data-rpt="daf_induk_wp_perkec"><a href="#tabs-daf_induk_wp_perkec" data-toggle="tab">Daftar Induk WP Per Kecamatan</a></li>
						<li data-rpt="daf_wp_baru"><a href="#tabs-daf_wp_baru" data-toggle="tab">WP Baru</a></li>
						<li data-rpt="daf_wp_baru_jenis"><a href="#tabs-daf_wp_baru_jenis" data-toggle="tab">WP Baru Per Jenis Usaha</a></li>
						<li data-rpt="daf_wp_baru_kec"><a href="#tabs-daf_wp_baru_kec" data-toggle="tab">WP Baru Per Kecamatan</a></li>
						<li data-rpt="daf_wp_jenis_perkec"><a href="#tabs-daf_wp_jenis_perkec" data-toggle="tab">WP Per Jenis Usaha Grup Per Kecamatan</a></li>
						<li data-rpt="daf_wp_tutup"><a href="#tabs-daf_wp_tutup" data-toggle="tab">WP Tutup</a></li>
						<li data-rpt="daf_wp_ijin_berakhir"><a href="#tabs-daf_wp_ijin_berakhir" data-toggle="tab">WP Ijin Berakhir</a></li>
						<li data-rpt="daf_rekap_kec"><a href="#tabs-daf_rekap_kec" data-toggle="tab">WP Per Kecamatan</a></li>
					</ul>
				</div>
			</div>

			<div class="col-sm-8">		
				<div class="tab-content">
					<div class="tab-pane active" id="tabs-daf_induk_wp">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
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
					
					<div class="tab-pane" id="tabs-daf_induk_wp_perjenis">
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
							<div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
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
				
					<div class="tab-pane" id="tabs-daf_induk_wp_perkec">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kecamatan</label> 
								<div class="col-sm-4">
									<?=$select_kecamatan;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
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
					
					<div class="tab-pane" id="tabs-daf_wp_baru">
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
							<!--div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
								</div>
							</div-->
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
					
					<div class="tab-pane" id="tabs-daf_wp_baru_jenis">
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
							<!--div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
								</div>
							</div-->
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
					
					<div class="tab-pane" id="tabs-daf_wp_baru_kec">
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
								<label class="control-label col-sm-2">Kecamatan</label> 
								<div class="col-sm-4">
									<?=$select_kecamatan;?>
								</div>
							</div>
							<!--div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
								</div>
							</div-->
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
					
					<div class="tab-pane" id="tabs-daf_wp_jenis_perkec">
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
							<div class="form-group">
								<label class="control-label col-sm-2">Status</label> 
								<div class="col-sm-4">
									<select class="form-control"  name="kondisi" id="kondisi" >
										<option value="">Semua</option>
										<option value="1">Aktif</option>
										<option value="2">Tutup</option>
										<option value="3">Tutup Sementara</option>
									</select>
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
					
					<div class="tab-pane" id="tabs-daf_wp_tutup">
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
					
					<div class="tab-pane" id="tabs-daf_wp_ijin_berakhir">
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
					
					<div class="tab-pane" id="tabs-daf_rekap_kec">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control"  id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
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