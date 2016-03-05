<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
/*
1. get wp
2. get usaha / cu
3. get/set masa
4. get/set jatuh tempo
5. get/set tarif
6.
*/
/*
$.fn.refresh = function() {
    return $(this.selector);
};
	$("#usaha_id").on('change', function() {
		alert('The option with value ' + $(this).val());
	});

*/

function get_pajak(usaha_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak/"+usaha_id,
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);

			var select = $('#pajak_id');
			var pajak_id = '<?=$dt['pajak_id']?>';

			select.html("");
			$.each(data, function(i, val){
				if(val['id'] == pajak_id)
					select.append($('<option />', { value: val['id'], text: val['pajaknm'], selected: "selected" }));
				else
					select.append($('<option />', { value: val['id'], text: val['pajaknm'] }));
			});
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
	var masa_dari = $('#masadari').val();

	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak_detail/"+pajak_id+"/"+masa_dari,
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);

			$('#tarif').val(data['tarif']);
			$('#minimal_omset').val(data['minimal_omset']);
			$('#r_bayarid').val(data['masapajak']);
			$('#r_nsr').val(data['reklame']);
			$('#m_njop').val(data['reklame']);
			$('#rekeningkd').val(data['rekeningkd']);
			$('#rekening_id').val(data['rekening_id']);
			$('#jatuhtempo').val(data['jatuhtempo']);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function hitung_pajak() {
	  isNaN(parseFloat("123"))? "a" : "b"
	  var dasar      = parseFloat($('#dasar').autoNumeric('get'));
	  dasar          = isNaN(dasar) ? 0 : dasar;
	  var tarif      = parseFloat($('#tarif').autoNumeric('get'));
	  tarif          = isNaN(tarif) ? 0 : tarif;
	  var denda      = parseFloat($('#denda').autoNumeric('get'));
	  denda          = isNaN(denda) ? 0 : denda;
	  var bunga      = parseFloat($('#bunga').autoNumeric('get'));
	  bunga          = isNaN(bunga) ? 0 : bunga;
	  var setoran    = parseFloat($('#setoran').autoNumeric('get'));
	  setoran        = isNaN(setoran) ? 0 : setoran;
	  var lain2      = parseFloat($('#lain2').autoNumeric('get'));
	  lain2          = isNaN(lain2) ? 0 : lain2;
	  var kompensasi = parseFloat($('#kompensasi').autoNumeric('get'));
	  kompensasi     = isNaN(kompensasi) ? 0 : kompensasi;
	  var kenaikan   = parseFloat($('#kenaikan').autoNumeric('get'));
	  kenaikan       = isNaN(kenaikan) ? 0 : kenaikan;

	  var pajak = (dasar*tarif) + denda + bunga + kenaikan + lain2 - setoran - kompensasi;
	  $('#pajak').autoNumeric('set', pajak);
}

<!-- // -->
var mID;
var oTable;
var xRow;

$(document).ready(function() {
	function get_cu(customer_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/get_cu/"+customer_id,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);
				$("#npwpd").val(data['npwpd']);
				$("#nama").val(data['customernm']);
				$("#so").val(data['so']);
				
				$('#usaha_id').html(data['usaha']);
		
				
				/*		
				$("#usaha_id").val(data['usaha_id']).attr('selected',true);
				
				alert(data['usaha_id']);
				
				$('#usaha_id').prop('selectedIndex', 0);
				
				$('#usaha_id').removeAttr('selected');
				$("#usaha_id option:first").attr('selected',true);
				$('#usaha_id').refresh();
				
				var select = $('#usaha_id');
				var cu_id = $('#customer_usaha_id').val();
				var usaha_id;

				select.html("");
				$.each(data, function(i, val){
					if(data[0]['customer_usaha_id'] == cu_id) {
						usaha_id = val['usaha_id'];
						select.append($('<option />', { value: val['usaha_id'], text: val['usahanm'], selected: "selected" }));
					} else
						select.append($('<option />', { value: val['usaha_id'], text: val['usahanm'] }));
				});
				*/
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	}

	function set_jatuh_tempo() {
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
		var jtp_tgl = jatuhtempotgl_dtp.date;
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
		var masa_bayar = parseInt($('#r_bayarid').val());
		var masa_dr = masadari_dtp.date;

		switch (masa_bayar) {
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

	oTable = $('#table_dlg1').dataTable({
		"iDisplayLength": 2,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [			
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "60px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "220px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": false, "bVisible": false, "sWidth": "200px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
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

	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>sptpd';
	});

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else
			get_cu(mID);
			
			/* 		
			alert($('#usaha_id').val());
			$('#usaha_id').trigger('refresh');
			var usaha_id = $('#usaha_id').val();
			get_pajak(usaha_id); 
			*/
			$('#usaha_id').trigger('change');
			
			/* 
			$("#pajak_id option:first").attr('selected','selected');
			get_pajak_detail();
			 */
			$('#cuDialog').modal('hide');
	});

	var terimatgl_dtp = $('#terimatgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		set_jatuh_tempo();
		terimatgl_dtp.hide();
	}).data('datepicker');

	var masadari_dtp = $('#masadari').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		set_masa_sd();
		set_jatuh_tempo();
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

	$('#dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});
	$('#tarif').autoNumeric('init', {aSep: '.', aDec: ',', vMax: '999999999999.99'});

	$('#dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak, #tarif').keypress(function() {
		hitung_pajak();
	});

	/* init page */
	get_cu(<?=$dt['customer_id']?>);
	hitung_pajak();
});
</script>

<!-- Modal -->
<div id="cuDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="cuDialogLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="cuDialogLabel">Subjek Pajak / Jenis Usaha</h3>
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
			<a href="#"><strong>PENDATAAN (SPTPD)</strong></a>
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
		<input class="form-control" type="text" name="id" value="<?=$dt['id']?>" placeholder="spt_id"/>
		<input class="form-control" type="text" name="so" id="so" value="<?=$dt['so']?>" placeholder="so"/>
		<input class="form-control" type="text" name="customer_id" id="customer_id" value="<?=$dt['customer_id']?>" placeholder="c_id"/>
		<input class="form-control" type="text" name="customer_usaha_id" id="customer_usaha_id" value="<?=$dt['customer_usaha_id']?>" placeholder="cu_id"/>
		<input class="form-control" type="text" name="r_bayarid" id="r_bayarid" value="<?=$dt['r_bayarid']?>" placeholder="r_bayarid" />
		<input class="form-control" type="text" name="minimal_omset" id="minimal_omset" value="<?=$dt['minimal_omset']?>" placeholder="minimal_omset" />
		<input class="form-control" type="text" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" placeholder="r_nsr" />
		<input class="form-control" type="text" name="rekening_id" id="rekening_id" value="<?=$dt['rekening_id']?>" placeholder="rekening_id" />
		<input class="form-control" type="text" name="rekeningkd" id="rekeningkd" value="<?=$dt['rekeningkd']?>" placeholder="rekeningkd" />
		<input class="form-control" type="text" name="jatuhtempo" id="jatuhtempo" value="<?=$dt['jatuhtempo']?>" placeholder="jatuhtempo" />

		<button type="submit" class="btn btn-primary">Simpan</button>
		<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>

		<div class="form-group">
			<label class="control-label col-sm-1" for="sptno">No. SPTPD</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="tahun" id="tahun" value="<?=$dt['tahun']?>" />
				<input class="form-control" type="text" name="sptno" id="sptno" value="<?=$dt['sptno']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="terimatgl">Tanggal Terima</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="npwpd">Subjek Pajak</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" />
				<input class="form-control" type="text" name="nama" id="nama" value="" placeholder="Nama Subjek Pajak" />
				<button type="button" class="btn" data-toggle="modal" data-target="#cuDialog">Cari</button>
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
			<label class="control-label col-sm-1" for="type_id">Jenis Dokumen</label>
			<div class="col-sm-4">
				<?=$select_sptpd_type;?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="masadari">Masa Pajak</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="masadari" id="masadari" value="<?=$dt['masadari']?>" placeholder="dd-mm-yyyy" required/> s/d
				<input class="form-control" type="text" name="masasd" id="masasd" value="<?=$dt['masasd']?>" placeholder="dd-mm-yyyy" required/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="jatuhtempotgl">Jatuh Tempo</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="jatuhtempotgl" id="jatuhtempotgl" value="<?=$dt['jatuhtempotgl']?>" placeholder="dd-mm-yyyy" required/>
			</div>
		</div>


		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#">Perhitungan</a>
			</li>
		</ul>

		<div class="row">
			<div class="span3">
				<div class="form-group">
					<label class="control-label col-sm-1" for="dasar">Dasar</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="dasar" id="dasar" value="<?=$dt['dasar']?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-1" for="tarif">Tarif</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="tarif" id="tarif" value="<?=$dt['tarif']?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-1" for="denda">Denda</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="denda" id="denda" value="<?=$dt['denda']?>" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="form-group">
					<label class="control-label col-sm-1" for="bunga">Bunga</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="bunga" id="bunga" value="<?=$dt['bunga']?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-1" for="setoran">Setoran</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="setoran" id="setoran" value="<?=$dt['setoran']?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-1" for="lain2">Lain-lain</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="lain2" id="lain2" value="<?=$dt['lain2']?>" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="form-group">
					<label class="control-label col-sm-1" for="kenaikan">Kenaikan</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="kenaikan" id="kenaikan" value="<?=$dt['kenaikan']?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-1" for="kompensasi">Kompensasi</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="kompensasi" id="kompensasi" value="<?=$dt['kompensasi']?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-1" for="pajak"><strong>Pajak Terhutang</strong></label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="pajak" id="pajak" />
					</div>
				</div>
			</div>
		</div>


		<!--
		<div class="form-group">
			<label class="control-label col-sm-1" for="pajak_id">pajak_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pajak_id" id="pajak_id" value="<?=$dt['pajak_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="terimanip">terimanip</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="terimanip" id="terimanip" value="<?=$dt['terimanip']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="kirimtgl">kirimtgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kirimtgl" id="kirimtgl" value="<?=$dt['kirimtgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="jatuhtempotgl">jatuhtempotgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="jatuhtempotgl" id="jatuhtempotgl" value="<?=$dt['jatuhtempotgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="type_id">type_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="type_id" id="type_id" value="<?=$dt['type_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="so">so</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="so" id="so" value="<?=$dt['so']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="masadari">masadari</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="masadari" id="masadari" value="<?=$dt['masadari']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="masasd">masasd</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="masasd" id="masasd" value="<?=$dt['masasd']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="minimal_omset">minimal_omset</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="minimal_omset" id="minimal_omset" value="<?=$dt['minimal_omset']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="air_manfaat_id">air_manfaat_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="air_manfaat_id" id="air_manfaat_id" value="<?=$dt['air_manfaat_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="air_zona_id">air_zona_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="air_zona_id" id="air_zona_id" value="<?=$dt['air_zona_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="meteran_awal">meteran_awal</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="meteran_awal" id="meteran_awal" value="<?=$dt['meteran_awal']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="meteran_akhir">meteran_akhir</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="meteran_akhir" id="meteran_akhir" value="<?=$dt['meteran_akhir']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="volume">volume</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="volume" id="volume" value="<?=$dt['volume']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="satuan">satuan</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="satuan" id="satuan" value="<?=$dt['satuan']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_nsr">r_nsr</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_nsrno">r_nsrno</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_nsrno" id="r_nsrno" value="<?=$dt['r_nsrno']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_nsrtgl">r_nsrtgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_nsrtgl" id="r_nsrtgl" value="<?=$dt['r_nsrtgl']?>" />
			</div>
		</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_tarifid">r_tarifid</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_tarifid" id="r_tarifid" value="<?=$dt['r_tarifid']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_kontrak">r_kontrak</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_kontrak" id="r_kontrak" value="<?=$dt['r_kontrak']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_lama">r_lama</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_lama" id="r_lama" value="<?=$dt['r_lama']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_jalanklas_id">r_jalanklas_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_jalanklas_id" id="r_jalanklas_id" value="<?=$dt['r_jalanklas_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_jalan_id">r_jalan_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_jalan_id" id="r_jalan_id" value="<?=$dt['r_jalan_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_lokasi">r_lokasi</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_lokasi" id="r_lokasi" value="<?=$dt['r_lokasi']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_judul">r_judul</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_judul" id="r_judul" value="<?=$dt['r_judul']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_panjang">r_panjang</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_panjang" id="r_panjang" value="<?=$dt['r_panjang']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_lebar">r_lebar</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_lebar" id="r_lebar" value="<?=$dt['r_lebar']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_muka">r_muka</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_muka" id="r_muka" value="<?=$dt['r_muka']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_banyak">r_banyak</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_banyak" id="r_banyak" value="<?=$dt['r_banyak']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_luas">r_luas</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_luas" id="r_luas" value="<?=$dt['r_luas']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="enabled">enabled</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="enabled" id="enabled" value="<?=$dt['enabled']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="unit_id">unit_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="unit_id" id="unit_id" value="<?=$dt['unit_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="create_date">create_date</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="create_date" id="create_date" value="<?=$dt['create_date']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="create_uid">create_uid</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="create_uid" id="create_uid" value="<?=$dt['create_uid']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="write_date">write_date</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="write_date" id="write_date" value="<?=$dt['write_date']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="write_uid">write_uid</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="write_uid" id="write_uid" value="<?=$dt['write_uid']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="customer_id">customer_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="customer_id" id="customer_id" value="<?=$dt['customer_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_nsl_kecamatan_id">r_nsl_kecamatan_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_nsl_kecamatan_id" id="r_nsl_kecamatan_id" value="<?=$dt['r_nsl_kecamatan_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_nsl_type_id">r_nsl_type_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_nsl_type_id" id="r_nsl_type_id" value="<?=$dt['r_nsl_type_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_nsl_nilai">r_nsl_nilai</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_nsl_nilai" id="r_nsl_nilai" value="<?=$dt['r_nsl_nilai']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_kelurahan_id">r_kelurahan_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_kelurahan_id" id="r_kelurahan_id" value="<?=$dt['r_kelurahan_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="isprint_dc">isprint_dc</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="isprint_dc" id="isprint_dc" value="<?=$dt['isprint_dc']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="notes">notes</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="notes" id="notes" value="<?=$dt['notes']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_lokasi_id">r_lokasi_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_lokasi_id" id="r_lokasi_id" value="<?=$dt['r_lokasi_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="rekening_id">rekening_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="rekening_id" id="rekening_id" value="<?=$dt['rekening_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="no_skpd_lama">no_skpd_lama</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="no_skpd_lama" id="no_skpd_lama" value="<?=$dt['no_skpd_lama']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1" for="r_calculated">r_calculated</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="r_calculated" id="r_calculated" value="<?=$dt['r_calculated']?>" />
			</div>
		</div>
		-->

		<hr />
	<?php echo form_close();?>
</div>
<? $this->load->view('_foot'); ?>