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
		case 'tap_register':
		case 'tap_register_airtanah':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
			}
			break;
		case 'tap_register_reklame':
		case 'tap_register_reklame_jthtempo':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
				naskah: $("div.tab-pane.active #naskah_reklame").val(),
			}
			break;
		case 'tap_kendali_self':
		case 'tap_kendali_self_sdh_bayar':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				// rangetgl: $("div.tab-pane.active #rangetgl").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				tglawalbayar:  $("div.tab-pane.active #tglawalbayar").val(),
				tglakhirbayar: $("div.tab-pane.active #tglakhirbayar").val(),
			}
			break;
		case 'tap_kendali_blm_bayar_self':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'tap_kendali':
		case 'tap_kendali_blm_bayar':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
			}
			break;
		case 'tap_kendali_reklame':
		case 'tap_kendali_reklame_sdh_bayar':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				// rangetgl: $("div.tab-pane.active #rangetgl").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				tglawalbayar:  $("div.tab-pane.active #tglawalbayar").val(),
				tglakhirbayar: $("div.tab-pane.active #tglakhirbayar").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
				naskah:   $("div.tab-pane.active #naskah_reklame").val(),
				insidentil: $("div.tab-pane.active #insidentil").val(),
			}
			break;
		case 'tap_kendali_reklame_blm_bayar':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
				naskah:   $("div.tab-pane.active #naskah_reklame").val(),
				insidentil: $("div.tab-pane.active #insidentil").val(),
			}
			break;
		case 'tap_kendali_airtanah':
		case 'tap_kendali_airtanah_sdh_bayar':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				// rangetgl: $("div.tab-pane.active #rangetgl").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				tglawalbayar:  $("div.tab-pane.active #tglawalbayar").val(),
				tglakhirbayar: $("div.tab-pane.active #tglakhirbayar").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
			}
			break;
		case 'tap_kendali_airtanah_blm_bayar':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				type_id:  $("div.tab-pane.active #type_id").val(),
			}
			break;
		case 'tap_kendali_reklame_jthtempo_rek':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
				rekeningid: $("div.tab-pane.active #rekeningid").val(),
			}
			break;
		case 'tap_kendali_airtanah_skpd':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'tap_kendali_airtanah_tgl':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'tap_kendali_airtanah_masa':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'tap_catat_vol_airtanah':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
			}
			break;
		default:
			rptparams = {
				rpt: rpt,
				nama: "noname",
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
	
	var tglawalbayar = $('[id=tglawalbayar]').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		$("[id=tglawalbayar]").datepicker('hide');
		$('div.tab-pane.active #tglakhirbayar')[0].focus();
	}).data('datepicker');
	
	var tglakhirbayar = $('[id=tglakhirbayar]').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		$("[id=tglakhirbayar]").datepicker('hide');
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
				<a href="#"><strong>LAPORAN PENETAPAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="col-sm-12">
		<p>&nbsp;</p>
			<div class="col-sm-4">
				<div class="sidebar-nav">
					<ul class="nav nav-list">
						<li class="nav-header">Laporan Register</li>
						<li data-rpt="tap_register" class="active"><a href="#tabs-tap_register" data-toggle="tab">Register Penetapan</a></li>
						<li data-rpt="tap_register_airtanah"><a href="#tabs-tap_register" data-toggle="tab">Register Penetapan Air Tanah</a></li>
						<li data-rpt="tap_register_reklame"><a href="#tabs-tap_register_reklame" data-toggle="tab">Register Penetapan Reklame</a></li>
						<li data-rpt="tap_register_reklame_jthtempo"><a href="#tabs-tap_register_reklame_jthtempo" data-toggle="tab">Reklame Jatuh Tempo (Masa Berakhir)</a></li>
						<li class="nav-header">Laporan Kendali</li>
						<!--li data-rpt="tap_kendali"><a href="#tabs-tap_kendali" data-toggle="tab">Laporan Kendali</a></li>
						<li data-rpt="tap_kendali_blm_bayar"><a href="#tabs-tap_kendali" data-toggle="tab">Laporan Kendali Belum Bayar</a></li-->
						<li data-rpt="tap_kendali_self"><a href="#tabs-tap_kendali_self" data-toggle="tab">Self Assessment</a></li>
						<li data-rpt="tap_kendali_self_sdh_bayar"><a href="#tabs-tap_kendali_self" data-toggle="tab">Self Assessment Sudah Bayar</a></li>
						<li data-rpt="tap_kendali_blm_bayar_self"><a href="#tabs-tap_kendali_blm_bayar_self" data-toggle="tab">Self Assessment Belum Bayar</a></li>
						<li data-rpt="tap_kendali_airtanah"><a href="#tabs-tap_at_kendali" data-toggle="tab">Pajak Air Tanah</a></li>
						<li data-rpt="tap_kendali_airtanah_sdh_bayar"><a href="#tabs-tap_at_kendali" data-toggle="tab">Pajak Air Tanah Sudah Bayar</a></li>
						<li data-rpt="tap_kendali_airtanah_blm_bayar"><a href="#tabs-tap_at_kendali_blm_bayar" data-toggle="tab">Pajak Air Tanah Belum Bayar</a></li>
						<li data-rpt="tap_kendali_reklame"><a href="#tabs-tap_kendali_reklame" data-toggle="tab">Pajak Reklame</a></li>
						<li data-rpt="tap_kendali_reklame_sdh_bayar"><a href="#tabs-tap_kendali_reklame" data-toggle="tab">Pajak Reklame Sudah Bayar</a></li>
						<li data-rpt="tap_kendali_reklame_blm_bayar"><a href="#tabs-tap_kendali_reklame_blm_bayar" data-toggle="tab">Pajak Reklame Belum Bayar</a></li>
                        
						<!--li data-rpt="tap_kendali_reklame_jthtempo_rek"><a href="#tabs-tap_kendali_reklame_jthtempo_rek" data-toggle="tab">Reklame Jatuh Tempo dan Pemasangan (Jatuh Tempo Per Rekening)</a></li>
						<li class="nav-header">Laporan Kendali Air Bawah Tanah</li>
						<li data-rpt="tap_kendali_airtanah"><a href="#tabs-tap_kendali_airtanah" data-toggle="tab">Air Bawah Tanah</a></li>
						<li data-rpt="tap_kendali_airtanah_skpd"><a href="#tabs-tap_kendali_airtanah_skpd" data-toggle="tab">Air Bawah Tanah Jatuh Tempo (Per Tgl SKPD)</a></li>
						<li data-rpt="tap_kendali_airtanah_tgl"><a href="#tabs-tap_kendali_airtanah_tgl" data-toggle="tab">Air Bawah Tanah Jatuh Tempo (Per Tgl Jatuh Tempo)</a></li>
						<li data-rpt="tap_kendali_airtanah_masa"><a href="#tabs-tap_kendali_airtanah_masa" data-toggle="tab">Air Bawah Tanah Jatuh Tempo (Per Masa Pajak)</a></li>
						<li data-rpt="tap_catat_vol_airtanah"><a href="#tabs-tap_catat_vol_airtanah" data-toggle="tab">Pencatatan Volume Meter Air Bawah Tanah</a></li-->
					</ul>
				</div>
			</div>

			<div class="col-sm-8">
				<div class="tab-content">
					<div class="tab-pane active" id="tabs-tap_register">
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
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
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
                    
					<div class="tab-pane" id="tabs-tap_register_reklame">
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
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Naskah Reklame</label> 
								<div class="col-sm-4">
									<input class="form-control"style="width:270px;" id="naskah_reklame" name="naskah_reklame" type="text" value="" />
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
					
					<div class="tab-pane" id="tabs-tap_register_reklame_jthtempo">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tgl. Masa Berakhir</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Naskah Reklame</label> 
								<div class="col-sm-4">
									<input class="form-control"style="width:270px;" id="naskah_reklame" name="naskah_reklame" type="text" value="" />
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
					
					<div class="tab-pane" id="tabs-tap_kendali">
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
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
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
                    
					<div class="tab-pane" id="tabs-tap_kendali_self">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<!--div class="form-group">
								<label class="control-label col-sm-2">Range Berdasarkan</label> 
								<div class="col-sm-4">
									<?=$select_rangetgl;?>
								</div>
							</div-->
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Pendataan</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Penerimaan</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawalbayar" name="tglawalbayar" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhirbayar" name="tglakhirbayar" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
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
                    
					<div class="tab-pane" id="tabs-tap_kendali_blm_bayar_self">
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
                    
					<div class="tab-pane" id="tabs-tap_at_kendali">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Penetapan</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Penerimaan</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawalbayar" name="tglawalbayar" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhirbayar" name="tglakhirbayar" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
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
				
					<div class="tab-pane" id="tabs-tap_at_kendali_blm_bayar">
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
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
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
				
					<div class="tab-pane" id="tabs-tap_kendali_reklame">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Penetapan</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Penerimaan</label> 
								<div class="col-sm-4">
									<input class="form-control" id="tglawalbayar" name="tglawalbayar" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" id="tglakhirbayar" name="tglakhirbayar" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Permanen /Tidak</label> 
								<div class="col-sm-4">
                                    <select name="insidentil" id="insidentil" class="form-control" required >
                                        <option value="-1">SEMUA</option>
                                        <option value="1">NON PERMANEN</option>
                                        <option value="0">PERMANEN</option>
                                    </select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Naskah Reklame</label> 
								<div class="col-sm-4">
									<input class="form-control"style="width:270px;" id="naskah_reklame" name="naskah_reklame" type="text" value="" />
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
					
					<div class="tab-pane" id="tabs-tap_kendali_reklame_blm_bayar">
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
								<label class="control-label col-sm-2">Jenis Penetapan</label> 
								<div class="col-sm-4">
									<?=$select_type_id;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Permanen/Tidak</label> 
								<div class="col-sm-4">
                                    <select name="insidentil" id="insidentil" class="form-control" required >
                                        <option value="-1">SEMUA</option>
                                        <option value="1">NON PERMANEN</option>
                                        <option value="0">PERMANEN</option>
                                    </select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Naskah Reklame</label> 
								<div class="col-sm-4">
									<input class="form-control"style="width:270px;" id="naskah_reklame" name="naskah_reklame" type="text" value="" />
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