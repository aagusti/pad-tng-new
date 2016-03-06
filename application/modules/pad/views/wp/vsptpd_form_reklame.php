<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/wp/_navbar'); ?>

<style>
.nav-tabs > .active > a, .nav-pills > .active > a:hover {
    color: blue;
}
.form-horizontal .controls {
  margin-left: 120px;  /* changed from 180px to 140px */
}
.form-horizontal .form-group {
    margin-bottom: 1px;
}
.form-horizontal .control-label col-sm-1{
	text-align:left;
	width: 120px; /* changed from 160px to 120px */
}
.form-horizontal input  {
	height: 14px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}
.form-horizontal select  {
	height: 24px !important;
	padding: 2px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}

button {
	height: 24px !important;
	padding: 4px 8px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}

hr {
  border: 0;
  border-bottom: 1px solid #dddddd;
}


.table.dataTable {
	margin-bottom: 8px;
	font-size: 10px;
}
.dataTables_filter input {
	height: 14px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 1px !important;
}
.modal {
    top: 10%;
	width: 800px;
	margin-left: -400px;
	--max-height: 250px;
}
.modal-body {
	--padding: 5px;
}
</style>

<script>
/*
1. get wp
2. get usaha / cu
3. get/set masa
4. get/set jatuh tempo
5. get/set tarif
6.
*/

function clearconsole() { 
	console.log(window.console);
	if(window.console || window.console.firebug) {
		console.clear();
	}
}

function get_pajak(cu_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak/"+cu_id,
		async: false,
		success: function (data) {
			$('#pajak_id').html(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function set_so(type_id) {
	if (type_id == <?=pad_dok_self_id()?>)
		$('#so').val('S');
	else
		$('#so').val('O');
}

function get_pajak_detail() {
	var pajak_id = $('#pajak_id').val();
	var masadari = $('#masadari').val();
	if (masadari == '') {
		var sekarang = new Date();
		/*
		$('#masadari').val(sekarang.getDate()+"-"+sekarang.getMonth()+"-"+sekarang.getFullYear());
		masadari = $('#masadari').val();
		*/
		masadari = sekarang.getDate()+"-"+sekarang.getMonth()+"-"+sekarang.getFullYear();
	}

	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak_detail/"+pajak_id+"/"+masadari,
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);

			var tarif = parseFloat(data['tarif']);
			$('#tarif').autoNumeric('set', tarif);
			var nsr = parseFloat(data['reklame']);
			$('#r_nsr').autoNumeric('set', nsr);

			$('#minimal_omset').val(data['minimal_omset']);
			$('#r_bayarid').val(data['masapajak']);
			$('#m_njop').val(data['reklame']);
			$('#rekeningkd').val(data['rekeningkd']);
			$('#rekening_id').val(data['rekening_id']);
			$('#jatuhtempo').val(data['jatuhtempo']);
		},
		error: function (xhr, desc, er) {
			alert(er);
		},
		complete: function() {
			/* clearconsole(); */
		}
		
	});
}

function get_jalan(jalan_kelas_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_jalan/"+jalan_kelas_id,
		async: false,
		success: function (data) {
			$('#r_jalan_id').html(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function hitung_luas() {
	var pj      = parseFloat($('#r_panjang').autoNumeric('get'));
	pj          = isNaN(pj) ? 0 : pj;
	var lb      = parseFloat($('#r_lebar').autoNumeric('get'));
	lb          = isNaN(lb) ? 0 : lb;
	var mk      = parseFloat($('#r_muka').autoNumeric('get'));
	mk          = isNaN(mk) ? 0 : mk;
	var by      = parseFloat($('#r_banyak').autoNumeric('get'));
	by          = isNaN(by) ? 0 : by;

	var luas = pj * lb * mk * by;
	luas = isNaN(luas) ? 0 : luas;
	$('#r_luas').autoNumeric('set', luas);
}


<!-- // -->
var mID;
var cuID;
var oTable;

$(document).ready(function() {
	function get_cu(customer_id, cu_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/get_cu/"+customer_id+"/"+cu_id+"/"+<?=$this->uri->segment(4);?>,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);
				$("#npwpd").val(data['npwpd']);
				$("#customer_id").val(data['customer_id']);
				$("#nama").val(data['customernm']);
				/* $("#customer_usaha_id").val(data['customer_usaha_id']); */
				$("#so").val(data['so']);

				/* $('#usaha_id').html(data['usaha']); //change to */
				$('#customer_usaha_id').html(data['usaha']);
				/* $("#customer_usaha_id option").not(":selected").attr("disabled", "disabled"); */
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	}

	function set_jatuh_tempo() {
		if ($('#masadari').val() == '' || $('#terimatgl').val() == '') return;

		var jt = parseInt($('#jatuhtempo').val());
		var masa_dr = masadari_dtp.date;
		var tetap_tgl = terimatgl_dtp.date;
		switch (jt) {
			/* jatuh tempo akhir bulan masa + 1 */
			case 0:
				jtp = new Date(masa_dr.getFullYear(), masa_dr.getMonth()+2, 1)-1;
				jatuhtempotgl_dtp.setValue(jtp);
				jatuhtempotgl_dtp.update();
				break;
			/* 30 hari dari tgl terima/tetap */
			case 1:
				jtp = new Date(tetap_tgl.getFullYear(), tetap_tgl.getMonth(), tetap_tgl.getDate() +30);
				jatuhtempotgl_dtp.setValue(jtp);
				jatuhtempotgl_dtp.update();
				break;
		}

		/* adjusment tgl jatuh tempo */
		var jtp_tgl = new Date(jtp); /* jatuhtempotgl_dtp.date; */
		var jtp_self = <?=pad_spt_due_date();?>;
		if ($('#rekeningkd').val() != '41107') {
			if ($('#so').val() == 'S') {
				jtp = new Date(jtp_tgl.getFullYear(), jtp_tgl.getMonth(), jtp_self);
				jatuhtempotgl_dtp.setValue(jtp);
				jatuhtempotgl_dtp.update();
			}

			jtp_tgl = jatuhtempotgl_dtp.date;
			if (jtp_tgl.getDay() == 6) {
				jtp = new Date(jtp_tgl.getFullYear(), jtp_tgl.getMonth(), jtp_tgl.getDate()+2);
				jatuhtempotgl_dtp.setValue(jtp);
				jatuhtempotgl_dtp.update();
			}
			if (jtp_tgl.getDay() == 0) {
				jtp = new Date(jtp_tgl.getFullYear(), jtp_tgl.getMonth(), jtp_tgl.getDate()+1);
				jatuhtempotgl_dtp.setValue(jtp);
				jatuhtempotgl_dtp.update();
			}
		}
	}

	function set_masa_sd() {
		if ($('#masadari').val() == '') return;

		var masa_pajak = parseInt($('#r_bayarid').val());
		var masa_dr = masadari_dtp.date;

		switch (masa_pajak) {
			/* tahunan */
			case 1:
				masa_sd = new Date(masa_dr.getFullYear()+1, masa_dr.getMonth(), masa_dr.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* semesteran */
			case 2:
				masa_sd = new Date(masa_dr.getFullYear(), masa_dr.getMonth()+6, masa_dr.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* triwulanan */
			case 3:
				masa_sd = new Date(masa_dr.getFullYear(), masa_dr.getMonth()+3, masa_dr.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* bulanan */
			case 4:
				masa_sd = new Date(masa_dr.getFullYear(), masa_dr.getMonth()+1, masa_dr.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* mingguan */
			case 5:
				masa_sd = new Date(masa_dr.getFullYear(), masa_dr.getMonth(), masa_dr.getDate()+7);
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* harian */
			case 6:
				masa_sd = new Date(masa_dr.getFullYear(), masa_dr.getMonth(), masa_dr.getDate()+1);
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
		}
	}

	function hitung_pajak() {
		var kontrak  = parseFloat($('#r_kontrak').autoNumeric('get'));
		var nsr      = parseFloat($('#r_nsr').autoNumeric('get'));
		var luas     = parseFloat($('#r_luas').autoNumeric('get'));
		var tarif    = parseFloat($('#tarif').autoNumeric('get'));

		kontrak      = isNaN(kontrak) ? 0 : kontrak;
		nsr          = isNaN(nsr)     ? 0 : nsr;
		luas         = isNaN(luas)    ? 0 : luas;
		tarif        = isNaN(tarif)   ? 0 : tarif;

		var ndiv  = 1;
		var days  = 24*60*60*1000;
		var nday  = masasd_dtp.date - masadari_dtp.date;
		var nhari = Math.round(nday / days) + 1;
		var pajak = nsr * luas;

		var masa_pajak = parseInt($('#r_bayarid').val());
		switch (masa_pajak) {
			/* tahunan */
			case 1:
				ndiv = 1;
				break;
			/* semesteran */
			case 2:
				ndiv = parseInt(nhari/180);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 180);
				if (nhari > 60) ndiv++;

				pajak = pajak * ndiv;
				break;
			/* triwulanan */
			case 3:
				ndiv = parseInt(nhari/90);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 90);
				if (nhari > 30) ndiv++;

				pajak = pajak * ndiv;
				break;
			/* bulanan */
			case 4:
				ndiv = parseInt(nhari/30);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 30);
				if (nhari > 10) ndiv++;

				pajak = pajak * ndiv;
				break;
			/* mingguan */
			case 5:
				ndiv = parseInt(nhari/7);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 7);
				if (nhari > 2) ndiv++;

				pajak = pajak * ndiv;
				break;
			/* harian */
			case 6:
				ndiv = nhari;
				pajak = pajak * ndiv;
				break;
		}

		$('#r_lama').val(ndiv);

		var dasar = (pajak < kontrak) ? kontrak : pajak;
		$('#dasar').autoNumeric('set', dasar);
		$('#r_calculated').autoNumeric('set', pajak);

		pajak = dasar * tarif;

		var kenaikan = 0;
		var kompensasi = 0;
		var tk = parseInt($('#r_tarifid').val());
		switch (tk) {
			case 2:
				kenaikan = pajak * 0.25;
				break;
			case 3:
				kompensasi = pajak * 0.25;
				break;
			case 4:
				kenaikan = pajak * 0.25;
				kompensasi = pajak * 0.25;
				break;
		}
		$('#kenaikan').autoNumeric('set', kenaikan);
		$('#kompensasi').autoNumeric('set', kompensasi);

		pajak = pajak + kenaikan - kompensasi;
		$('#pajak').autoNumeric('set', pajak);
	}

	oTable = $('#table_dlg1').dataTable({
		"iDisplayLength": 9,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [6], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "60px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "220px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": false, "bVisible": false, "sWidth": "200px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = ''; cuID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					cuID = data[6];

					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			mID = ''; cuID = '';
		},
		"oLanguage": {
			"sProcessing":   "<i class='fa fa-refresh fa-spin' style='font-size: 70px;'></i>",
			"sLengthMenu":   "Tampilkan _MENU_ entri",
			"sZeroRecords":  "Tidak ada data",
			"sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
			"sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
			"sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
			"sInfoPostFix":  "",
			"sSearch":       "Cari : ",
			"sUrl":          "",
			"oPaginate": {
				"sFirst":    "&laquo;",
				"sPrevious": "&lsaquo;",
				"sNext":     "&rsaquo;",
				"sLast":     "&raquo;",
			}
		},
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=active_module_url('subjek_pajak/grid_for_spt/'.pad_reklame_id());?>"
	});

	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url($this->uri->segment(2));?>';
	});

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			get_cu(mID, cuID);
			$('#customer_usaha_id').trigger('change');
			/* get_pajak_detail(); */

			$('#cuDialog').modal('hide');
		}
	});

	$('#dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak, #r_calculated, #r_kontrak, #r_nsr, #r_banyak').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});
	$('#r_panjang, #r_lebar, #r_muka, #r_luas, #tarif').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99'
	});

	$('#r_panjang, #r_lebar, #r_muka, #r_banyak').keypress(function() {
		hitung_luas();
		hitung_pajak();
	});

	$('#dasar, #tarif, #r_kontrak').keypress(function() {
		hitung_pajak();
	});

	$('#customer_usaha_id').change(function() {
		get_pajak(this.value);
		get_pajak_detail();

		set_masa_sd();
		set_jatuh_tempo();
		hitung_pajak();
	});

	$('#pajak_id').change(function() {
		get_pajak_detail();

		set_masa_sd();
		set_jatuh_tempo();
		hitung_pajak();
	});

	$('#r_jalanklas_id').change(function() {
		get_jalan(this.value);
	});

	$('#r_tarifid').change(function() {
		hitung_pajak();
	});

	$('#nopd').typeahead({
		source: function(query, process){
			$.getJSON('<?=active_module_url('objek_pajak/get_typeahead_nopd/'.$this->uri->segment(4));?>'+query, function(response) {
				var data = new Array;
				for(var i in response) {
					data.push(response[i]['id'] +"#"+ response[i]['nopd'] +"#"+ response[i]['customer_id'] +"#"+ response[i]['usaha_id'] +"#"+ response[i]['customernm'] +"#"+ response[i]['usahanm']);
				}
				return process(data);
			});
		},
		matcher: function (item) {
			var parts = item.split('#');
			return parts[1].toLowerCase().indexOf(this.query.trim().toLowerCase()) != -1;
		},
		highlighter: function(item) {
			var parts = item.split('#'),
				regex = new RegExp('(' + this.query + ')', 'i'),
				nopd = parts[1].replace(regex, "<strong>$1</strong>"),
				html = '<div class="typeahead">';

			html += '<div class="left">';
			html += '<div>'+nopd+'</div>';
			html += '<div>'+parts[4]+' - '+parts[5]+'</div>';
			html += '</div>';
			html += '<div class="clear"></div>';
			html += '</div>';
			return html;
		},
		updater: function(item) {
			var parts = item.split('#');

			get_cu(parts[2], parts[0]);
			$('#customer_usaha_id').trigger('change');
			get_pajak_detail();

			return parts[1];
		},
	});

	var now = new Date();
	var terimatgl_dtp = $('#terimatgl').datepicker({
		format: 'dd-mm-yyyy',
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? 'disabled' : '';
		},
	}).on('changeDate', function(ev) {
		set_jatuh_tempo();
		terimatgl_dtp.hide();
	}).data('datepicker');

	var masadari_dtp = $('#masadari').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		/* set_masa_sd();
		set_jatuh_tempo(); */
		$('#pajak_id').trigger('change');
		masadari_dtp.hide();
	}).data('datepicker');

	var masasd_dtp = $('#masasd').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		masasd_dtp.hide();
	}).data('datepicker');

	var jatuhtempotgl_dtp = $('#jatuhtempotgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		jatuhtempotgl_dtp.hide();
	}).data('datepicker');

	var r_nsrtgl_dtp = $('#r_nsrtgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		r_nsrtgl_dtp.hide();
	}).data('datepicker');


	/* init page */
	var c_id  = parseInt(<?=$dt['customer_id']?>);
	var cu_id = parseInt(<?=$dt['customer_usaha_id']?>);
	if (!isNaN(c_id)) {
		get_cu(c_id, cu_id);
		hitung_pajak();
		
		/* penyesuaian karena wp isi sendiri */
		$('#customer_usaha_id').trigger('change');
		$("#type_id option").not(":selected").attr("disabled", "disabled");
		$("#terimatgl, #jatuhtempotgl, type_id, #tarif, #denda, #bunga, #setoran, #lain2, #kenaikan, #kompensasi").attr("readonly", "readonly");
	}
});

$(document).keypress(function(event){
	if (event.which == '13') {
		event.preventDefault();
	}
});

</script>

<div class="content">
	<!-- Modal -->
	<div id="cuDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="cuDialogLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="cuDialogLabel">Subjek Pajak dan Jenis Usaha</h3>
		</div>
		<div class="modal-body">
			<table class="table" id="table_dlg1">
				<thead>
					<tr>
						<th>index</th>
						<th>NPWPD</th>
						<th>Subjek Pajak</th>
						<th>Pemilik/Pengelola</th>
						<th>Alamat</th>
						<th>Jenis Usaha</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" id="btn_dialog_ok">OK!</button>
			<button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
		</div>
	</div>


	<div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>PENETAPAN REKLAME</strong></a>
			</li>
		</ul>

		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		}
		?>

		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
			<input class="form-control" type="hidden" name="id" value="<?=$dt['id']?>" placeholder="spt_id"/>
			<input class="form-control" type="hidden" name="so" id="so" value="<?=$dt['so']?>" placeholder="so"/>
			<input class="form-control" type="hidden" name="customer_id" id="customer_id" value="<?=$dt['customer_id']?>" placeholder="c_id"/>
			<!--input class="form-control" type="hidden" name="customer_usaha_id" id="customer_usaha_id" value="<?=$dt['customer_usaha_id']?>" placeholder="cu_id"/-->
			<input class="form-control" type="hidden" name="r_bayarid" id="r_bayarid" value="<?=$dt['r_bayarid']?>" placeholder="r_bayarid" />
			<input class="form-control" type="hidden" name="minimal_omset" id="minimal_omset" value="<?=$dt['minimal_omset']?>" placeholder="minimal_omset" />
			<!--input class="form-control" type="hidden" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" placeholder="r_nsr" /-->
			<input class="form-control" type="hidden" name="rekening_id" id="rekening_id" value="<?=$dt['rekening_id']?>" placeholder="rekening_id" />
			<input class="form-control" type="hidden" name="rekeningkd" id="rekeningkd" value="<?=$dt['rekeningkd']?>" placeholder="rekeningkd" />
			<input class="form-control" type="hidden" name="jatuhtempo" id="jatuhtempo" value="<?=$dt['jatuhtempo']?>" placeholder="jatuhtempo" />
			<input class="form-control" type="hidden" name="r_lama" id="r_lama" value="<?=$dt['r_lama']?>" placeholder="r_lama" />

			<div class="form-group">
				<label class="control-label col-sm-1" for="sptno">No. SPTPD</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="tahun" id="tahun" value="<?=$dt['tahun']?>" readonly />
					<input class="form-control" type="text" name="sptno" id="sptno" value="<?=$dt['sptno']?>" readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="terimatgl">Tanggal Terima</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" />
					&nbsp;&nbsp;&nbsp;Jenis Dokumen&nbsp;<?=$select_sptpd_type;?>
				</div>
			</div>
			<!--div class="form-group">
				<label class="control-label col-sm-1" for="nopd">NOPD</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="nopd" id="nopd" value="<?=$dt['nopd']?>" placeholder="NOPD" autocomplete="off" />
				</div>
			</div-->
			<div class="form-group">
				<label class="control-label col-sm-1" for="npwpd">Subjek Pajak</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" autocomplete="off"  readonly />
					<input class="form-control" type="text" name="nama" id="nama" value="" placeholder="Nama Subjek Pajak" autocomplete="off"  readonly />
					<button type="button" class="btn hide" data-toggle="modal" data-target="#cuDialog">Cari</button>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="usaha_id">Jenis Usaha</label>
				<div class="col-sm-4">
					<?=$select_usaha;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="pajak_id">Jenis Pajak</label>
				<div class="col-sm-4">
					<?=$select_pajak;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="masadari">Masa Pajak</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="masadari" id="masadari" value="<?=$dt['masadari']?>" placeholder="dd-mm-yyyy" required/> s/d
					<input class="form-control" type="text" name="masasd" id="masasd" value="<?=$dt['masasd']?>" placeholder="dd-mm-yyyy" required/> &nbsp;&nbsp;&nbsp; Jatuh Tempo
					<input class="form-control" type="text" name="jatuhtempotgl" id="jatuhtempotgl" value="<?=$dt['jatuhtempotgl']?>" placeholder="dd-mm-yyyy" required/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="r_nsrno">No. / Tgl. NSR</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="r_nsrno" id="r_nsrno" value="<?=$dt['r_nsrno'];?>"/> /
					<input class="form-control" type="text" name="r_nsrtgl" id="r_nsrtgl" value="<?=$dt['r_nsrtgl']?>" placeholder="dd-mm-yyyy" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="r_judul">Judul Reklame</label>
				<div class="col-sm-4">
					<input class="input-xxlarge" type="text" name="r_judul" id="r_judul" value="<?=$dt['r_judul'];?>"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="r_jalanklas_id">Klas Jalan</label>
				<div class="col-sm-4">
					<?=$select_jalan_kelas;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="r_jalan_id">Jalan / Kecamatan</label>
				<div class="col-sm-4">
					<?=$select_jalan;?> /
					<!--r_lokasi_id-->
					<?=$select_lokasi;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="r_lokasi">Lokasi Pasang</label>
				<div class="col-sm-4">
					<textarea class="input-xxlarge" name="r_lokasi" rows="1" cols="50" ><?=$dt['r_lokasi']?></textarea>
				</div>
			</div>


			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#"><strong>Perhitungan</strong></a>
				</li>
			</ul>

			<div class="form-group">
				<label class="control-label col-sm-1" for="r_panjang">Ukuran</label>
				<div class="col-sm-4">
					Pj: <input class="form-control" type="text" style="text-align:right;" name="r_panjang" id="r_panjang" value="<?=$dt['r_panjang']?>" required />
					Lb: <input class="form-control" type="text" style="text-align:right;" name="r_lebar" id="r_lebar" value="<?=$dt['r_lebar']?>" required />
					Mk: <input class="form-control" type="text" style="text-align:right;" name="r_muka" id="r_muka" value="<?=$dt['r_muka']?>" required />
					Jml: <input class="form-control" type="text" style="text-align:right;" name="r_banyak" id="r_banyak" value="<?=$dt['r_banyak']?>" required />
					Luas: <input class="form-control" type="text" style="text-align:right;" name="r_luas" id="r_luas" value="<?=$dt['r_luas']?>" required />
				</div>
			</div>
			<div class="row">
				<div class="span3" style="width:376px;">
					<div class="form-group">
						<label class="control-label col-sm-1" for="r_kontrak">Nilai Kontrak</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="r_kontrak" id="r_kontrak" value="<?=$dt['r_kontrak']?>" required />
						</div>
					</div>
					<div class="form-group hide">
						<label class="control-label col-sm-1" for="dasar">Dasar</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="dasar" id="dasar" value="<?=$dt['dasar']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-1" for="tarif">Tarif</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="tarif" id="tarif" value="<?=$dt['tarif']?>" required />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-1" for="r_tarifid">Tarif Lainnya</label>
						<div class="col-sm-4">
							<?=$select_tarif;?>
						</div>
					</div>
				</div>
				<div class="span3" style="width:220px;">
					<div class="form-group hide">
						<label class="control-label col-sm-1" for="kenaikan">Kenaikan</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="kenaikan" id="kenaikan" value="<?=$dt['kenaikan']?>" readonly />
						</div>
					</div>
					<div class="form-group hide">
						<label class="control-label col-sm-1" for="kompensasi">Kompensasi</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="kompensasi" id="kompensasi" value="<?=$dt['kompensasi']?>" readonly />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-1" for="r_nsr">NSR</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" readonly />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-1" for="r_calculated">Calculated</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="r_calculated" id="r_calculated" value="<?=$dt['r_calculated']?>" readonly />
						</div>
					</div>
					<div class="form-group success">
						<label class="control-label col-sm-1" for="pajak"><strong>Pajak Terhutang</strong></label>
						<div class="col-sm-4">
							<input class="input-small " type="text" style="text-align:right;" name="pajak" id="pajak" readonly />
						</div>
					</div>
				</div>
			</div>
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
	</div>
</div>
<? $this->load->view('_foot'); ?>