<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
function show_rpt(){
	var rptparams;
	var rpt = $(".nav-list li.active").data('rpt');
	switch(rpt) {
		case 'tgh_sdh_byr':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
			}
			break;
		case 'tgh_sdh_byr_perkec':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				kecid:  $("div.tab-pane.active #kecid").val(),
			}
			break;
		case 'tgh_sdh_byr_perkel':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				kecid:  $("div.tab-pane.active #kecid").val(),
			}
			break;
		case 'tgh_sdh_byr_jnskec':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
				kecid:  $("div.tab-pane.active #kecid").val(),
			}
			break;
		case 'tgh_sdh_byr_jnskel':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
				kecid:  $("div.tab-pane.active #kecid").val(),
				kelid:  $("div.tab-pane.active #kelid").val(),
			}
			break;
		case 'tgh_jatuh_tempo_blm_byr':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'tgh_jatuh_tempo_sdh_byr':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
				usahaid:  $("div.tab-pane.active #usahaid").val(),
			}
			break;
		case 'tgh_peringkat_byr':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tglawal:  $("div.tab-pane.active #tglawal").val(),
				tglakhir: $("div.tab-pane.active #tglakhir").val(),
			}
			break;
		case 'tgh_surat_teguran':
			rptparams = {
				rpt: rpt,
				tglcetak: $("div.tab-pane.active #tglcetak").val(),
				tahun:  $("div.tab-pane.active #tahun").val(),
				bulan: $("div.tab-pane.active #bulan").val(),
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
	var url  = '<?=active_module_url($this->router->fetch_class());?>cetak/pdf/?'+data;
	
	var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
	window.open(url, 'Laporan', winparams);
}

function get_kelurahan(kec_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>subjek_pajak/get_kelurahan/"+kec_id,
		success: function (j) {
			var data = $.parseJSON(j);
			var select;
			if ($('div.tab-pane.active').has('select#kelid')) {
				select = $('[id=kelid]');
			} else {
				select = $('div.tab-pane.active select#kelid');
			}

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
	
	/* INIT */
	get_kelurahan($('#kecid').val());
});
</script>
<div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>LAPORAN PENAGIHAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		
		<div class="col-sm-12">
		<p>&nbsp;</p>
			<div class="col-sm-4">
				<div class="sidebar-nav">
					<ul class="nav nav-list">
						<!--li class="nav-header">Laporan</li-->
						<li data-rpt="tgh_sdh_byr" class="active"><a href="#tabs-tgh_sdh_byr" data-toggle="tab">WP Sudah Bayar (Semua)</a></li>
						<li data-rpt="tgh_sdh_byr_perkec"><a href="#tabs-tgh_sdh_byr_perkec" data-toggle="tab">WP Sudah Bayar Per Kecamatan</a></li>
						<li data-rpt="tgh_sdh_byr_perkel"><a href="#tabs-tgh_sdh_byr_perkel" data-toggle="tab">WP Sudah Bayar Per Kelurahan</a></li>
						<li data-rpt="tgh_sdh_byr_jnskec"><a href="#tabs-tgh_sdh_byr_jnskec" data-toggle="tab">WP Sudah Bayar Per Objek/ Kecamatan</a></li>
						<li data-rpt="tgh_sdh_byr_jnskel"><a href="#tabs-tgh_sdh_byr_jnskel" data-toggle="tab">WP Sudah Bayar Per Objek/ Kelurahan</a></li>
						<li data-rpt="tgh_jatuh_tempo_blm_byr"><a href="#tabs-tgh_jatuh_tempo_blm_byr" data-toggle="tab">WP Jatuh Tempo (Belum Bayar)</a></li>
						<li data-rpt="tgh_jatuh_tempo_sdh_byr"><a href="#tabs-tgh_jatuh_tempo_sdh_byr" data-toggle="tab">WP Jatuh Tempo (Sudah Bayar)</a></li>
						<li data-rpt="tgh_peringkat_byr"><a href="#tabs-tgh_peringkat_byr" data-toggle="tab">Peringkat Bayar</a></li>
						<li data-rpt="tgh_surat_teguran"><a href="#tabs-tgh_surat_teguran" data-toggle="tab">Surat Teguran Pajak Daerah</a></li>
					</ul>
				</div>
			</div>

			<div class="col-sm-8">		
				<div class="tab-content">
					<div class="tab-pane active" id="tabs-tgh_sdh_byr">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
					
					<div class="tab-pane" id="tabs-tgh_sdh_byr_perkec">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kecamatan</label> 
								<div class="col-sm-4">
									<?=$select_kecamatan;?>
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
				
					<div class="tab-pane" id="tabs-tgh_sdh_byr_perkel">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kecamatan</label> 
								<div class="col-sm-4">
									<?=$select_kecamatan;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kelurahan</label> 
								<div class="col-sm-4">
									<select class="form-control" id="kelid"><option>&nbsp;</option></select>
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
					
					<div class="tab-pane" id="tabs-tgh_sdh_byr_jnskec">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Usaha</label> 
								<div class="col-sm-4">
									<?=$select_usaha;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kecamatan</label> 
								<div class="col-sm-4">
									<?=$select_kecamatan;?>
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
					
					<div class="tab-pane" id="tabs-tgh_sdh_byr_jnskel">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Usaha</label> 
								<div class="col-sm-4">
									<?=$select_usaha;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kecamatan</label> 
								<div class="col-sm-4">
									<?=$select_kecamatan;?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Kelurahan</label> 
								<div class="col-sm-4">
									<select class="form-control" id="kelid"><option>&nbsp;</option></select>
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
					
					<div class="tab-pane" id="tabs-tgh_jatuh_tempo_blm_byr">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
					
					<div class="tab-pane" id="tabs-tgh_jatuh_tempo_sdh_byr">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
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
					
					<div class="tab-pane" id="tabs-tgh_peringkat_byr">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2">Periode</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglawal" name="tglawal" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" /> s.d 
									<input class="form-control" style="width:100px;" id="tglakhir" name="tglakhir" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
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
					
					<div class="tab-pane" id="tabs-tgh_surat_teguran">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2">Tanggal Cetak</label> 
								<div class="col-sm-4">
									<input class="form-control" style="width:100px;" id="tglcetak" name="tglcetak" type="text" value="<?=date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
								</div>
							</div>
							<!--div class="form-group">
								<label class="control-label col-sm-2">Bulan/Tahun</label> 
								<div class="col-sm-4">
									<select class="form-control" name="bulan" id="bulan" class="form-control" >
										<option value="1">JANUARI</option><option value="2">PEBRUARI</option><option value="3">MARET</option><option value="4">APRIL</option>
										<option value="5">MEI</option><option value="6">JUNI</option><option value="7">JULI</option><option value="8">AGUSTUS</option>
										<option value="9">SEPTEMBER</option><option value="10">OKTOBER</option><option value="11">NOVEMBER</option><option value="12">DESEMBER</option>
									</select>	
									<input class="form-control"  id="tahun" name="tahu" type="text" value="<?=date('Y');?>" placeholder="yyyy" />
								</div>
							</div-->
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
					
				</div>
			</div>
		</div>

	</div>
</div>
<? $this->load->view('_foot'); ?>