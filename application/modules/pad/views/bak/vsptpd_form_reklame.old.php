<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.form-control {
  height: 24px;
  padding: 0px 5px;
  font-size: 12px;
}

.form-group {
  margin-bottom: 5px;
}

.form-horizontal 
.control-label {
  padding-top: 3px;
}

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

function set_so(type_id) {
	if (type_id == <?=pad_dok_self_id()?>)
		$('#so').val('S');
	else
		$('#so').val('O');
}

function get_nsr_val(nsr_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_nsr/"+nsr_id,
		async: false,
		success: function (data) {
            $('#r_nsr').autoNumeric('set', data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
     // clearconsole();
}
var global_pajak_id;
function get_pajak(cu_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak/"+cu_id+"/"+global_pajak_id,
		async: false,
		success: function (data) {
			$('#pajak_id').html(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
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
			//var nsr = parseFloat(data['reklame']);
			//$('#r_nsr').autoNumeric('set', nsr);
			var njop = parseFloat(data['reklame']);
			$('#r_njop').autoNumeric('set', njop);
			$('#minimal_omset').val(data['minimal_omset']);
			$('#r_bayarid').val(data['masapajak']);
			$('#kode').val(data['kode']);
			$('#rekening_id').val(data['rekening_id']);
			$('#jatuhtempo').val(data['jatuhtempo']);
			document.getElementById('nopd').value = nopd ;
		},
		error: function (xhr, desc, er) {
			alert(er);
		},
		complete: function() {
			/* clearconsole(); */
		}
		
	});
}

function get_pajak_detail2(masadari) {
	var pajak_id = $('#pajak_id').val();
	//var masadari = $('#masadari').val();
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak_detail/"+pajak_id+"/"+masadari,
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);

			var tarif = parseFloat(data['tarif']);
			$('#tarif').autoNumeric('set', tarif);
			//var nsr = parseFloat(data['reklame']);
			//$('#r_nsr').autoNumeric('set', nsr);
			var njop = parseFloat(data['reklame']);
			$('#r_njop').autoNumeric('set', njop);
			$('#minimal_omset').val(data['minimal_omset']);
			$('#r_bayarid').val(data['masapajak']);
			$('#kode').val(data['kode']);
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


function get_klas_jalan(jalan_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_klas_jalan/"+jalan_id,
		async: false,
		success: function (data) {
			$('#r_jalanklas_id').html(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function get_jalan_val(jalan_kelas_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_jalan_val/"+jalan_kelas_id,
		async: false,
		success: function (data) {
			$('#r_jalanklas_val').val(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function get_lokasi_pasang_val(lokasi_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_lokasi_pasang_val/"+lokasi_id,
		async: false,
		success: function (data) {
			$('#r_lokasi_pasang_val').val(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function get_sudut_pandang_val(sudut_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_sudut_pandang_val/"+sudut_id,
		async: false,
		success: function (data) {
			$('#r_sudut_pandang_val').val(data);
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
    /*
	var mk      = parseFloat($('#r_muka').autoNumeric('get'));
	mk          = isNaN(mk) ? 0 : mk;
	var by      = parseFloat($('#r_banyak').autoNumeric('get'));
	by          = isNaN(by) ? 0 : by;
    */
    
	//var luas = pj * lb * mk * by;
    var luas = pj * lb;
	luas = isNaN(luas) ? 0 : luas;
	$('#r_luas').autoNumeric('set', luas);
}


<!-- // -->
var mID;
var cuID;
var pID;
var nopd;
var wpnama;
var wpalamat;

var namakonter;
var oTable;

$(document).ready(function() {
	function get_cu(customer_id, cu_id, def_pajak_id) {

		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/get_cu/"+customer_id+"/"+cu_id+"/"+def_pajak_id,
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
				$("#customer_usaha_id option").not(":selected").attr("disabled", "disabled");             
				var nama = document.getElementById('nama').value;
				document.getElementById('display_nama').innerHTML = nama ;
				document.getElementById('wpnama').value = wpnama ;
				document.getElementById('wpalamat').value = wpalamat ;

			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		global_pajak_id=def_pajak_id;
	}

	function set_jatuh_tempo(bulantahun) {
		//if ($('#masadari').val() == '' || $('#terimatgl').val() == '') return;
		if ($('#terimatgl').val() == '') return;
		var jt = parseInt($('#jatuhtempo').val());
		//var masadari = masadari_dtp.date;
		var tetap_tgl = terimatgl_dtp.date;
		switch (jt) {
			/* jatuh tempo akhir bulan masa + 1 */
			case 0:
				//jtp = new Date(masadari.getFullYear(), masadari.getMonth()+2, 1)-1;
				var year = parseInt(bulantahun.substr(3,6));
				var month = parseInt(bulantahun.substr(0, 2));

				if (year%4==0){var feb=29;}else{var feb=28;}

				var nextmonth = month + 1 ;
				if (nextmonth == 13) {nextmonth=1; year=year+1;}
				//jtp = new Date(year, month + 1, 0);
				//jtp = new Date( (new Date(year, month,1))-1 );;
				if (month==1)
				jtp = feb+"-"+nextmonth+"-"+year ;
				if (month==0 || month==3 || month==5 || month==7 || month==8 || month==10 || month==12)
				jtp = 30+"-"+nextmonth+"-"+year ;
				if (month==2 || month==4 || month==6 || month==9 || month==11 )
				jtp = 31+"-"+nextmonth+"-"+year ;
				document.getElementById('jatuhtempotgl').value = jtp ;
				break;
			/* 30 hari dari tgl terima/tetap 		
			case 1:
				jtp = new Date(tetap_tgl.getFullYear(), tetap_tgl.getMonth(), tetap_tgl.getDate() +30);
				jatuhtempotgl_dtp.setValue(jtp);
				jatuhtempotgl_dtp.update();
				break;
					*/	

				}
			}
        
		/* adjusment tgl jatuh tempo 
		var jtp_tgl = new Date(jtp);
		var jtp_self = <?=pad_spt_due_date();?>;
        var day_in_month = new Date(jtp_tgl.getFullYear(), jtp_tgl.getMonth() + 1, 0);
        if(jtp_self > day_in_month) jtp_self = day_in_month;        
		if ($('#kode').val() != '41107') {
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
 		*/
	function set_masa_sd() {
		if ($('#masadari').val() == '') return;

		var masa_pajak = parseInt($('#r_bayarid').val());
		var masadari = masadari_dtp.date;

		switch (masa_pajak) {
			/* tahunan */
			case 1:
				masa_sd = new Date(masadari.getFullYear()+1, masadari.getMonth(), masadari.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* semesteran */
			case 2:
				masa_sd = new Date(masadari.getFullYear(), masadari.getMonth()+6, masadari.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* triwulanan */
			case 3:
				masa_sd = new Date(masadari.getFullYear(), masadari.getMonth()+3, masadari.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* bulanan */
			case 4:
				masa_sd = new Date(masadari.getFullYear(), masadari.getMonth()+1, masadari.getDate())-1;
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* mingguan */
			case 5:
				masa_sd = new Date(masadari.getFullYear(), masadari.getMonth(), masadari.getDate()+7);
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
			/* harian */
			case 6:
				masa_sd = new Date(masadari.getFullYear(), masadari.getMonth(), masadari.getDate()+1);
				masasd_dtp.setValue(masa_sd);
				masasd_dtp.update();
				break;
		}
	}

	function hitung_pajak() {
		var njop = parseFloat($('#r_njop').autoNumeric('get'));
		var luas = parseFloat($('#r_luas').autoNumeric('get'));
        
		luas     = isNaN(luas) ? 0 : luas;

        var hitung1 = luas * njop;
        var tinggi  = parseFloat($('#r_tinggi').autoNumeric('get'));
        
        tinggi = isNaN(tinggi) ? 0 : tinggi;
        
        var tinggi_val = 100000;
        var pajak_selected = $("#pajak_id option:selected").text();
        pajak_selected = pajak_selected.toLowerCase();
        if(pajak_selected.indexOf("megatron")>-1 ||
           pajak_selected.indexOf("video")>-1 ||
           pajak_selected.indexOf("dinamic")>-1 ||
           pajak_selected.indexOf("display")>-1 ||
           pajak_selected.indexOf("bando")>-1)
            tinggi_val = 150000;
            
        var hitung2 = tinggi * tinggi_val;
        var hitung3 = hitung1 + hitung2;
        
        var klas_jalan    = parseFloat($('#r_jalanklas_val').val());
        var lokasi_pasang = parseFloat($('#r_lokasi_pasang_val').val());
        var sudut_pandang = parseFloat($('#r_sudut_pandang_val').val());
        
        klas_jalan    = isNaN(klas_jalan) ? 0 : klas_jalan;
        lokasi_pasang = isNaN(lokasi_pasang) ? 0 : lokasi_pasang;
        sudut_pandang = isNaN(sudut_pandang) ? 0 : sudut_pandang;
        
        var skor = klas_jalan + lokasi_pasang + sudut_pandang;
		var nsr  = parseFloat($('#r_nsr').autoNumeric('get'));
        
		nsr = isNaN(nsr)  ? 0 : nsr;
        
        var hitung4 = nsr * skor;
        var hitung5 = hitung3 + hitung4;
		var tarif   = parseFloat($('#tarif').autoNumeric('get'));
        
		tarif = isNaN(tarif)   ? 0 : tarif;
        
        var pajak = hitung5 * tarif;    //menurut nota    
        pajak = Math.round(pajak);
        
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

        var hitung6 = pajak + kenaikan - kompensasi;
        
		var ndiv  = 1;
		var days  = 24*60*60*1000;
		var nday  = masasd_dtp.date - masadari_dtp.date;
		var nhari = Math.round(nday / days) + 1;

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
				break;
			/* triwulanan */
			case 3:
				ndiv = parseInt(nhari/90);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 90);
				if (nhari > 30) ndiv++;
				break;
			/* bulanan */
			case 4:
				ndiv = parseInt(nhari/30);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 30);
				if (nhari > 10) ndiv++;
				break;
			/* mingguan */
			case 5:
				ndiv = parseInt(nhari/7);
				if (ndiv == 0) ndiv = 1;

				nhari = nhari - (ndiv * 7);
				if (nhari > 2) ndiv++;
				break;
			/* harian */
			case 6:
				ndiv = nhari;
				break;
		}
        
        var mk = parseFloat($('#r_muka').autoNumeric('get'));
        var by = parseFloat($('#r_banyak').autoNumeric('get'));
        
        mk = isNaN(mk) ? 0 : mk;
        by = isNaN(by) ? 0 : by;
        
        var hitung7 = hitung6 * mk * by * ndiv;
        
        var dasar = (hitung7-kenaikan+kompensasi)/tarif;
		// var denda = parseFloat($('#denda').autoNumeric('get'));
		var total = hitung7; // + denda;
        total = Math.round(total);
        
		$('#r_lama').val(ndiv);
		// $('#dasar').autoNumeric('set', dasar);
		$('#dasar').autoNumeric('set', pajak);
		$('#kenaikan').autoNumeric('set', kenaikan);
		$('#kompensasi').autoNumeric('set', kompensasi);
		$('#pajak').autoNumeric('set', total);
		$('#r_calculated').autoNumeric('set', total);
        
        // console.log('hitung1: '+hitung1);
        // console.log('hitung2: '+hitung2);
        // console.log('hitung3: '+hitung3);
        // console.log('hitung4: '+hitung4);
        // console.log('hitung5: '+hitung5);
        // console.log('hitung6: '+hitung6);
        // console.log('hitung7: '+hitung7);
        // console.log('dasar: '+dasar);
        // console.log('total: '+total);
	}

	oTable = $('#table_dlg1').dataTable({
		"iDisplayLength": 11,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			//{ "aTargets": [6], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			//{ "aTargets": [7], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			//{ "aTargets": [8], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },

		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = ''; cuID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					nopd = data[1];
					cuID = data[7];
					pID = data[9];
					wpnama =data[10];
					wpalamat =  data[11];
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

	var tb_array = [
		'<br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);


	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url($this->uri->segment(2));?>';
	});

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			get_cu(mID, cuID, pID, nopd);
			$('#customer_usaha_id').trigger('change');
			/* get_pajak_detail(); */

			$('#cuDialog').modal('hide');
		}
	});

	$('#r_njop, #dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak, #r_calculated, #r_kontrak, #r_nsr, #r_banyak').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});
    
	$('#r_panjang, #r_lebar, #r_muka, #tarif, #r_tinggi, #r_luas').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
	});

	$('#r_panjang, #r_lebar, #r_muka, #r_banyak').keyup(function() {
		hitung_luas();
		hitung_pajak();
	});

	$('#dasar, #tarif, #r_kontrak').keyup(function() {
		hitung_pajak();
	});

	$('#customer_usaha_id').change(function() {
		get_pajak(this.value);
		get_pajak_detail();

		set_masa_sd();
		//set_jatuh_tempo();
		hitung_pajak();
	});

	$('#pajak_id').change(function() {
		get_pajak_detail();

		set_masa_sd();
		set_jatuh_tempo();
		hitung_pajak();
	});
    
	$('#r_nsr_id').change(function() {
		get_nsr_val(this.value);
		hitung_pajak();
	});

	$('#r_jalan_id').change(function() {
		get_klas_jalan(this.value);
		$('#r_jalanklas_id').trigger('change');
	});
    
	$('#r_jalanklas_id').change(function() {
        get_jalan_val(this.value);
		hitung_pajak();
	});
    
	$('#r_lokasi_pasang_id').change(function() {
		get_lokasi_pasang_val(this.value);
		hitung_pajak();
	});
    
	$('#r_sudut_pandang_id').change(function() {
		get_sudut_pandang_val(this.value);
		hitung_pajak();
	});
    
	$('#r_tarifid, #denda').change(function() {
		hitung_pajak();
	});

	$('#nopd').typeahead({
		source: function(query, process){
			$.getJSON('<?=active_module_url('objek_pajak/get_typeahead_nopd/'.$this->uri->segment(4));?>'+query, function(response) {
				var data = new Array;
				for(var i in response) {
					data.push(response[i]['id'] +"#"+ response[i]['nopd'] +"#"+ response[i]['customer_id'] +"#"+ response[i]['usaha_id'] +"#"+ response[i]['customernm'] +"#"+ response[i]['usahanm']+"#"+ response[i]['def_pajak_id']);
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

			get_cu(parts[2], parts[0], parts[6]);
			$('#customer_usaha_id').trigger('change');
			// get_pajak_detail();

			return parts[1];
		},
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

    // Don't allow direct editing
    $('#terimatgl, #masadari, #masasd, #jatuhtempotgl').on('keypress', function(e) {
        e.preventDefault();
    });

    //masapajakbulan
    $("#masapajak_bulan").monthpicker();
		
	$('#masapajak_bulan').change(function() {
		var masadari = "01"+"-"+document.getElementById('masapajak_bulan').value ;
		var bulantahun = document.getElementById('masapajak_bulan').value ;
		document.getElementById('masadari').value = masadari;
		//get_pajak_detail2(masadari);
		set_jatuh_tempo(bulantahun);
		//hitung_pajak();
	});

	/* init page */
    $('#r_nsr_id').trigger('change');
    $('#r_jalan_id').trigger('change');
    $('#r_lokasi_pasang_id').trigger('change');
    $('#r_sudut_pandang_id').trigger('change');
    
   // $('#r_jalan_id').combobox({bsVersion: '2'});
    //$('#r_jalan_id').trigger('change')
    
	var c_id  = parseInt(<?=$dt['customer_id']?>);
	var cu_id = parseInt(<?=$dt['customer_usaha_id']?>);
	if (!isNaN(c_id)) {
		get_cu(c_id, cu_id);
		hitung_pajak();
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
<div id="cuDialog" class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Subjek Pajak - Usaha : REKLAME</h4>
				</div>
				<div class="modal-body">
					<table class="table" id="table_dlg1">
						<thead>
							<tr>
								<th>index</th>
								<th>NOPD</th>
								<th>Nama Usaha</th>								
								<th>NPWPD</th>
								<th>Subjek Pajak</th>
								<th>Pemilik</th>
								<th>Alamat</th>

							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<p>&nbsp;</p>	
				<p>&nbsp;</p>				
				<div class="modal-footer">
					<button class="btn btn-primary" id="btn_dialog_ok">OK!</button>
					<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Batal</button>
				</div>
			</div>
		</div>
	</div>


	<div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>PENDATAAN REKLAME</strong></a>
			</li>
		</ul>

		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		}
		?>

		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?>
		<p>&nbsp;</p>
			<div class="box box-primary box-solid">
			<div class="box-header with-border">
			<h3 class="box-title">Rincian Pendataan : <text id="display_nama"></text> </h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
			<div class="row">

			<!-- /.COLLAPSE-->
			<input class="form-control" type="hidden" name="id" value="<?=$dt['id']?>" placeholder="spt_id"/>
			<input class="form-control" type="hidden" name="so" id="so" value="<?=$dt['so']?>" placeholder="so"/>
			<input class="form-control" type="hidden" name="customer_id" id="customer_id" value="<?=$dt['customer_id']?>" placeholder="c_id"/>
			<!--input class="form-control" type="hidden" name="customer_usaha_id" id="customer_usaha_id" value="<?=$dt['customer_usaha_id']?>" placeholder="cu_id"/-->
			<input class="form-control" type="hidden" name="r_bayarid" id="r_bayarid" value="<?=$dt['r_bayarid']?>" placeholder="r_bayarid" />
			<input class="form-control" type="hidden" name="minimal_omset" id="minimal_omset" value="<?=$dt['minimal_omset']?>" placeholder="minimal_omset" />
			<!--input class="form-control" type="hidden" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" placeholder="r_nsr" /-->
			<input class="form-control" type="hidden" name="rekening_id" id="rekening_id" value="<?=$dt['rekening_id']?>" placeholder="rekening_id" />
			<input class="form-control" type="hidden" name="kode" id="kode" value="<?=$dt['kode']?>" placeholder="kode" />
			<input class="form-control" type="hidden" name="jatuhtempo" id="jatuhtempo" value="<?=$dt['jatuhtempo']?>" placeholder="jatuhtempo" />
			<input class="form-control" type="hidden" name="r_lama" id="r_lama" value="<?=$dt['r_lama']?>" placeholder="r_lama" />
            
			<input class="form-control" type="hidden" name="r_njop" id="r_njop" value="<?=$dt['r_njop']?>" placeholder="r_njop" />
			<input class="form-control" type="hidden" name="r_jalanklas_val" id="r_jalanklas_val" value="<?=$dt['r_jalanklas_val']?>" placeholder="r_jalanklas_val" />
			<input class="form-control" type="hidden" name="r_lokasi_pasang_val" id="r_lokasi_pasang_val" value="<?=$dt['r_lokasi_pasang_val']?>" placeholder="r_lokasi_pasang_val" />
			<input class="form-control" type="hidden" name="r_sudut_pandang_val" id="r_sudut_pandang_val" value="<?=$dt['r_sudut_pandang_val']?>" placeholder="r_sudut_pandang_val" />

			<div class="row col-sm-12">
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="sptno">No. SPTPD</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="tahun" id="tahun" value="<?=$dt['tahun']?>" readonly />
                        </div> 
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="sptno" id="sptno" value="<?=$dt['sptno']?>" readonly />
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-5"  for="usaha_id">Status</label>
                        <div class="col-sm-4">
                            <?=$select_status;?>
                        </div>
                    </div>   
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="terimatgl">Tgl.Terima</label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" />
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
					<label class="control-label col-sm-5" for="skpd_old">SKPD Sebelumnya</label>
					<div class="col-sm-7">
						<input class="form-control" type="text" name="skpd_old" id="skpd_old" />
					</div>
					</div> 
                    <div class="form-group col-sm-6">
						<label class="control-label col-sm-3" for="cara_bayar">Cara Bayar</label>
						<div class="col-sm-4">
							<select class="form-control" name="cara_bayar" id="cara_bayar">
								<?if ($dt['cara_bayar']==1) {?>
								<option value=1 > Transfer </option>
								<?}else if ($dt['cara_bayar']==2){?>
								<option value=2> Teller </option>
								<?}else if ($dt['cara_bayar']==3){?>
								<option value=3> ATM </option>
								<?}?>
								<option value=2> Teller </option>
								<option value=1 > Transfer </option>
								<option value=3> ATM </option>
							</select>
						</div>
					</div>  
					<div class="col-sm-12">
					</div>
                    <div class="span4 hidden">
                    	<div class="form-group">
                        <label class="control-label col-sm-3"  for="type_id">Jenis Dok.</label>
                        	<div class="col-sm-4">
                            <?=$select_sptpd_type;?>
                        </div>
                    </div>
                    </div>

                    <div class="form-group col-sm-6">

                        <label class="control-label col-sm-3"  for="nopd">NOPD</label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="nopd" id="nopd" value="<?=$dt['nopd']?>" placeholder="NOPD" autocomplete="off" />
                        </div>
                        <div class="form-group col-sm-3">
						<button type="button" class="btn btn-success" data-toggle="modal" data-target="#cuDialog">Cari</button>
						</div>
						<label class="control-label col-sm-3"  for="npwpd">Subjek Pajak</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" autocomplete="off"  readonly />
							<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" placeholder="Nama Subjek Pajak" autocomplete="off" readonly/>
						</div>
						<div class="form-group col-sm-12">
						</div>
						<label class="control-label col-sm-3"  for="pnama">Pemilik/Alamat</label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="wpnama" id="wpnama" value="" autocomplete="off" readonly />
							<input class="form-control" type="text" name="wpalamat" id="wpalamat" value="" autocomplete="off" readonly />
						</div>
					</div>	
			</div>		
			<div class="row col-sm-12">
					<div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="usaha_id">Jenis Usaha</label>
                        <div class="col-sm-6">
                            <?=$select_usaha;?>
                        </div>
                    </div>
 			</div>
			<div class="row col-sm-12">
					<div class="form-group col-sm-6">
						<label class="control-label col-sm-3"  for="pajak_id">Jenis Pajak</label>
						<div class="col-sm-4">
							<?=$select_pajak;?>
						</div>
					</div>
					<div class="col-sm-12">
					</div>
					<div class="form-group col-sm-6">
					<label class="control-label col-sm-3"  for="masapajak_bulan">Masa Pajak</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="masapajak_bulan" id="masapajak_bulan" value="<?$masapajak_bulan = substr($dt['masadari'], -7); echo $masapajak_bulan;?>" placeholder="mm-yyyy" required/>
                    </div>

				 	<label class="control-label col-sm-3 hide"  for="masadari">Masa Pajak</label>
                    <div class="col-sm-3 hide">
                        <input class="form-control" type="text" name="masadari" id="masadari" value="<?=$dt['masadari']?>" placeholder="dd-mm-yyyy" />
                    </div>
                    <label class="control-label col-sm-3 hide"  for="masasd">s / d </label>
                    <div class="col-sm-3 hide">                   
                        <input class="form-control" type="text" name="masasd" id="masasd" value="<?=$dt['masasd']?>" placeholder="dd-mm-yyyy"/>
                    </div> 
                    <div class="col-sm-12">
         			</div>
                    <label class="control-label col-sm-3"  for="jatuhtempotgl">Jatuh Tempo</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="jatuhtempotgl" id="jatuhtempotgl" value="<?=$dt['jatuhtempotgl']?>" placeholder="dd-mm-yyyy" required/>
                    </div>
                     <div class="col-sm-12">
         			</div>
					<label class="control-label col-sm-3"  for="r_judul">Naskah Reklame</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="r_judul" id="r_judul" value="<?=$dt['r_judul'];?>"/>
					</div>	
					</div>

            </div>
            <div class="row col-sm-12">
            <div class="form-group col-sm-6 hidden">
			<label class="control-label col-sm-3" for="doc_no">Dokumen No</label>
			<div class="col-sm-6">
				<input class="form-control" type="text" name="doc_no" id="doc_no" value="<?=$dt['doc_no']?>" />
			</div>
			</div>
			</div>
            <div class="row col-sm-12">
			</div>

            <div class="row col-sm-12">
            <br>
	            <div class="form-group col-sm-6">
	            	<div class="col-sm-12">
						<div class="col-sm-6 success">
						<b class="success"><u> UKURAN </u></b>
						</div>
						<div class="col-sm-6">
							&nbsp;
						</div>
					</div>
					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_panjang">Panjang</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_panjang" id="r_panjang" value="<?=$dt['r_panjang']?>" required />
						</div>
					</div>
					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_lebar">Lebar</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_lebar" id="r_lebar" value="<?=$dt['r_lebar']?>" required />
						</div>
					</div>
					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_tinggi">Tinggi</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_tinggi" id="r_tinggi" value="<?=$dt['r_tinggi']?>" required />
						</div>
					</div>
					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_muka">Muka</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_muka" id="r_muka" value="<?=$dt['r_muka']?>" required />
						</div>
					</div>
					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_banyak">Jumlah</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_banyak" id="r_banyak" value="<?=$dt['r_banyak']?>" required />
						</div>
	                </div>
	                <div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_luas">Luas</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_luas" id="r_luas" value="<?=$dt['r_luas']?>" readonly />
						</div>
					</div>
					<p>&nbsp;</p>
	            	<div class="col-sm-12">
						<div class="col-sm-6 success">
						<b class="success"><u> MEDIA </u></b>
						</div>
						<div class="col-sm-6">
							&nbsp;
						</div>
					</div>

					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_billboard">BillBoard</label>
						<div class="col-sm-6">
							<input class="form-control" type="number" style="text-align:right;" name="r_billboard" id="r_billboard" value="<?//=$dt['r_luas']?>"  min=0 />
						</div>
					</div>

					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_kain">Kain</label>
						<div class="col-sm-6">
							<input class="form-control" type="number" style="text-align:right;" name="r_kain" id="r_kain" value="<?//=$dt['r_luas']?>" min=0 />
						</div>
					</div>

					<div class="col-sm-6">
						<label class="control-label col-sm-6"  for="r_videotron">Videotron</label>
						<div class="col-sm-6">
							<input class="form-control" type="number" style="text-align:right;" name="r_videotron" id="r_videotron" value="<?//=$dt['r_luas']?>" min=0 />
						</div>
					</div>

					<div class="col-sm-12">
                        	<p>&nbsp;</p>
             			</div>
                </div>

	            <div class="form-group col-sm-6">
					<div class="form-group col-sm-12">
                        <label class="control-label col-sm-6"  for="r_nsr_id">Jenis NSR</label>
                        <div class="col-sm-6">
                            <?=$select_nsr;?>
                        </div>
                    </div>
					<div class="form-group col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-6"  for="r_nsr">Nilai NSR</label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="r_nsr" id="r_nsr" value="" placeholder="Nilai NSR" readonly />
                        </div>
                    </div>
                	</div>
                	<div class="form-group col-sm-12">
                        <label class="control-label col-sm-6"  for="r_jalan_id"> Jalan</label>
                        <div class="col-sm-6">
                            <?=$select_jalan;?>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="control-label col-sm-6"  for="r_jalanklas_id">Klas Jalan</label>
                        <div class="col-sm-6">
                            <?=$select_jalan_kelas;?>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="control-label col-sm-6"  for="r_lokasi_pasang_id">Lokasi Pasang</label>
                        <div class="col-sm-6">
                            <?=$select_lokasi_pasang;?>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="control-label col-sm-6"  for="r_sudut_pandang_id">Sudut Pandang</label>
                        <div class="col-sm-6">
                            <?=$select_sudut_pandang;?>
                        </div>
                    </div>
                </div>	
                </div>
                </div>
                </div>
              </div>  
			<!--div class="form-group">
				<label class="control-label col-sm-2"for="  terimatgl">Tanggal Terima</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" />
					&nbsp;&nbsp;&nbsp;Jenis Dokumen&nbsp;<?=$select_sptpd_type;?>
				</div>
			</div-->
			<!--div class="form-group">
				<label class="control-label col-sm-2"  for="r_nsrno">No. / Tgl. NSR</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="r_nsrno" id="r_nsrno" value="<?=$dt['r_nsrno'];?>"/> /
					<input class="form-control" type="text" name="r_nsrtgl" id="r_nsrtgl" value="<?=$dt['r_nsrtgl']?>" placeholder="dd-mm-yyyy" />
				</div>
			</div-->
			<!--div class="form-group">
				<label class="control-label col-sm-2"  for="r_jalan_id">Jalan</label>
				<div class="col-sm-4">
					<?=$select_jalan;?> /
					<?=$select_lokasi;?>
				</div>
			</div-->
			<!--div class="form-group">
				<label class="control-label col-sm-2"  for="r_lokasi">Lokasi Pasang</label>
				<div class="col-sm-4">
					<textarea class="form-control" name="r_lokasi" rows="1" cols="50" ><?=$dt['r_lokasi']?></textarea>
				</div>
			</div-->
			<div class="tabbable col-sm-12">
			<ul id="myTab" class="nav nav-tabs">
				<li class="active"><a href="#perhitungan" data-toggle="tab"><strong>Perhitungan</strong></a></li>
			</ul>
			<div class="tab-content">
			<div class="tab-pane fade in active" id="perhitungan">	
			<br>
				<div class="box box-primary box-solid">
				<div class="box-body">
				<div class="row">

			<div class="form-group col-sm-5">
				<label class="control-label col-sm-4"  for="tarif">Tarif</label>
				<div class="col-sm-6">
					<input class="form-control" type="text" style="text-align:right;" name="tarif" id="tarif" value="<?=$dt['tarif']?>" required readonly/>
				</div>
			<label class="control-label col-sm-4"  for="r_tarifid">Tarif Lainnya</label>
				<div class="col-sm-6">
				<?=$select_tarif;?>
				</div>
			</div>

			<div class="form-group col-sm-5">
				<label class="control-label col-sm-6"  for="r_calculated">Calculated</label>
				<div class="col-sm-6">
					<input class="form-control" type="text" style="text-align:right;" name="r_calculated" id="r_calculated" value="<?=$dt['r_calculated']?>" readonly />
				</div>
				<label class="control-label col-sm-6"  for="pajak"><strong>Pajak Terhutang</strong></label>
				<div class="col-sm-6">
					<input class="form-control" type="text" style="text-align:right;" name="pajak" id="pajak" readonly />
				</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			
			<div class="row col-sm-12">
			<p>&nbsp;</p>
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
			</div>

			<div class="row col-sm-6">

					<!--div class="form-group">
						<label class="control-label col-sm-2"  for="r_kontrak">Nilai Kontrak</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="r_kontrak" id="r_kontrak" value="<?=$dt['r_kontrak']?>" required />
						</div>
					</div-->
					<div class="form-group hide">
						<label class="control-label col-sm-4"  for="dasar">Dasar</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" style="text-align:right;" name="dasar" id="dasar" value="<?=$dt['dasar']?>" />
						</div>
					</div>

					<!--div class="form-group">
						<label class="control-label col-sm-2"  for="denda">Denda</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="denda" id="denda" value="<?=$dt['denda']?>" />
						</div>
					</div-->
					
				</div>
			</div>	
			<div class="row col-sm-6">
				<div class="form-group col-sm-12">
					<div class="form-group hide">
						<label class="control-label col-sm-6"  for="kenaikan">Kenaikan</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="kenaikan" id="kenaikan" value="<?=$dt['kenaikan']?>" readonly />
						</div>
					</div>
					<div class="form-group hide">
						<label class="control-label col-sm-6"  for="kompensasi">Kompensasi</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="kompensasi" id="kompensasi" value="<?=$dt['kompensasi']?>" readonly />
						</div>
					</div>
					<!--div class="form-group">
						<label class="control-label col-sm-2"  for="r_nsr">NSR</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" style="text-align:right;" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" readonly />
						</div>
					</div-->
					
				</div>
			</div>
			
		<?php echo form_close();?>
	</div>
</div>
<? $this->load->view('_foot'); ?>