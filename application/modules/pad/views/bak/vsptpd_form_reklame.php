<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.form-control {
  height: 24px;
  padding: 0px 5px;
  font-size: 12px;
}

.form-control2 {
padding-right: 0px;
padding-left: 0px;
}
.form-group {
  margin-bottom: 5px;
}

.form-horizontal 
.control-label {
  padding-top: 3px;
}

	@media (min-width:768px) {
	.modal-content {
	margin-left: -120px;
    width: 140%;
	}
	.modal-backdrop {
    position: fixed;
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

function get_nsr(kelas_jalan_id, media_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/get_nsr/"+kelas_jalan_id+"/"+media_id,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);
				var nsr = data['nsr'];
				if(nsr==null){
					alert("Nilai Sewa Reklame Tidak Ditemukan!");
				}else{
				$('#r_nsr').autoNumeric('set', nsr);
				$('#masatext').html(data['masatext']);
				$('#jenis_masa_view').val(data['jenismasa']);
				var ms = data['masa']; 
				$('#masa_id').val(ms);
				$('#jenis_masa').val(ms);

				if (ms==0){ //tahun
					var valmasa = ($('#r_lama_day').val())/365;
				}
				else if (ms==1){ //bulan
					var valmasa = ($('#r_lama_day').val())/30;
				}
				else if (ms==2){ //minggu
					var valmasa = ($('#r_lama_day').val())/7;
				}
				document.getElementById('r_lama').value = Math.round(valmasa);
				}
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
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
		masadari = sekarang.getDate()+"-"+sekarang.getMonth()+"-"+sekarang.getFullYear();


		/*
		$('#masadari').val(sekarang.getDate()+"-"+sekarang.getMonth()+"-"+sekarang.getFullYear());
		masadari = $('#masadari').val();
		*/
	}

	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak_detail/"+pajak_id+"/"+masadari,
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);
			var tarif = parseFloat(data['tarif']);
			$('#tarif').autoNumeric('set', tarif);
			document.getElementById('tarif_view').value = (tarif*100) + ' %' ;
			//var nsr = parseFloat(data['reklame']);
			//$('#r_nsr').autoNumeric('set', nsr);
			var njop = parseFloat(data['reklame']);
			$('#r_njop').autoNumeric('set', njop);
			$('#minimal_omset').val(data['minimal_omset']);
			$('#r_bayarid').val(data['masapajak']);
			$('#kode').val(data['kode']);
			$('#rekening_id').val(data['rekening_id']);
			$('#jatuhtempo').val(data['jatuhtempo']);
			$('#multiple').val(data['multiple']);

			//Checking NOPD supaya tidak undefined
			var nopd_tmp = isNaN(parseInt($('#nopd').val()));
			var nopdformat = document.getElementById('nopd').value;
			var nopdnonformat= nopdformat.replace(/\./g, "");
			
			if( nopdnonformat != document.getElementById('nopd').value){
				document.getElementById('nopd').value = nopd ;
			}else if (nopdformat != document.getElementById('nopd').value){
				document.getElementById('nopd').value = nopd ;
			}else if (nopd_tmp == true){
				document.getElementById('nopd').value = nopd ;
			}
		},
		error: function (xhr, desc, er) {
			alert(er);
		},
		complete: function() {
			/* clearconsole(); */
		}
		
	});
}


/*
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
*/

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
	var tg      = parseFloat($('#r_tinggi').autoNumeric('get'));
	tg          = isNaN(tg) ? 0 : tg;
    /*
	var mk      = parseFloat($('#r_muka').val());
	mk          = isNaN(mk) ? 0 : mk;
	var by      = parseFloat($('#r_banyak').autoNumeric('get'));
	by          = isNaN(by) ? 0 : by;
    */
    
	//var luas = pj * lb * mk * by;
    var luas = pj * lb * tg;
	luas = isNaN(luas) ? 0 : luas;
	$('#r_luas').autoNumeric('set', luas);
}

function convertToRupiah(nStr) {
   nStr += '';
   x = nStr.split('.');
   x1 = x[0];
   x2 = x.length > 1 ? '.' + x[1] : '';
   var rgx = /(\d+)(\d{3})/;
   while (rgx.test(x1))
   {
      x1 = x1.replace(rgx, '$1' + '.' + '$2');
   }
   return x1 + x2;
}

function get_media_reklame() {
	pjID = $('#pajak_id').val();
	kjID = $('#r_jalanklas_id').val();
	msDR = $('#masadari').val();
	//on Edit
	meID = $('#r_media_id_temp').val();


	if(pjID == null || kjID == null || msDR == null){
		alert("Pastikan Jenis Pajak, Kelas Jalan dan Masa sudah diisi!");
		$('#r_jalanklas_id').val('');
		$('#r_jalanklas').val('');
		$('#r_jalan').val('');
		$('#r_jalan_id').val('');
	}else{

	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_media_reklame/"+pjID+"/"+kjID+"/"+msDR+"/"+meID,
		async: false,
		success: function (data) {
			$('#r_media_id').html(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
	}
}

function get_jalan_reklame() {
	jlID = $('#r_jalan_id').val();
	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_jalan_reklame/"+jlID,
		async: false,
		success: function (data) {
			$('#r_jalan').val(data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

<!-- // -->
var mID;
var cuID;
var pID;
var nopd;

var namakonter;
var media;
var oTable;
var oTable2;
var oTable3;
var oTableKD;
var is_edit = 0;

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
				$("#wpnama").val(data['wpnama']);
				$("#wpalamat").val(data['wpalamat']);

			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		global_pajak_id=def_pajak_id;
	}

	<?
	$spt_uri = $this->uri->segment(6);
	if ($spt_uri != null){
		$spt_uri = $spt_uri;
	}else
		$spt_uri = 0;
	?>

	/* daftar MEDIA REKLAME - etc */
	oTableKD = $('#tableKD').dataTable({
	"iDisplayLength": 99,	
	"bJQueryUI": true,
	"bFilter": false,
	"bInfo": false,
	"sDom": '<"toolbar">frtip',
	//"bProcessing": true,
	//"bServerSide": true,
	"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [2], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [4], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [5], "bSearchable": false,  "bVisible": false,  "sWidth": "", "sClass": "" },
			{ "aTargets": [6], "bSearchable": false,  "bVisible": false,  "sWidth": "", "sClass": "" },
			{ "aTargets": [7], "bSearchable": false, "bVisible": true, "sWidth": "87px", "sClass": "right" },
			{ "aTargets": [8], "bSearchable": false, "bVisible": true, "sWidth": "87px", "sClass": "right" },
			{ "aTargets": [9], "bSearchable": false, "bVisible": true, "sWidth": "97px", "sClass": "right" },			
			{ "aTargets": [10], "bSearchable": false, "bVisible": true, "sWidth": "90px", "sClass": "right" },
			{ "aTargets": [11], "bSearchable": false, "bVisible": true, "sWidth": "120px", "sClass": "right" },
			{ "aTargets": [12], "bSearchable": false, "bVisible": true, "sWidth": "120px", "sClass": "right" },
			{ "aTargets": [13], "bSearchable": false,  "bVisible": true,  "sWidth": "120px", "sClass": "right" },
			{ "aTargets": [14], "bSearchable": false,  "bVisible": false,  "sWidth": "", "sClass": "" },
			{ "aTargets": [15], "bSearchable": false,  "bVisible": false,  "sWidth": "", "sClass": "" },
			{ "aTargets": [16], "bSearchable": false,  "bVisible": false,  "sWidth": "", "sClass": "" },
			{ "aTargets": [17], "bSearchable": false,  "bVisible": false,  "sWidth": "", "sClass": "" }],
	"sAjaxSource": "<? echo active_module_url('sptpd/grid_media/'.$spt_uri); ?>",
	});


	/* control kartu data hotel */
var i =0;
$('#btn_dth_new').click( function (e) {
	e.preventDefault();

	if ($('#r_luas').val()=='' || $('#r_nsr').val()==0 || $('#r_panjang').val()=='' || $('#r_lebar').val()=='') {
		alert ('Harap isi data dengan benar!')
		return;
	};
	var r_jalan_id  = parseFloat($('#r_jalan_id').val());
	var media_id  = parseFloat($('#r_media_id').val());
	var media  = $('#r_media').val();
	var panjang = parseFloat($('#r_panjang').autoNumeric('get'));
	var lebar = parseFloat($('#r_lebar').autoNumeric('get'));
	var tinggi = parseFloat($('#r_tinggi').autoNumeric('get'));
	var nsr = parseFloat($('#r_nsr').autoNumeric('get'));
	var jalan_klas_id = parseFloat($('#r_jalanklas_id').val());
	var jalan_klas = $('#r_jalanklas').val();
	var alamat_reklame = $('#alamat_reklame').val();
	var r_lama = $('#r_lama').val();
	var status_baliho = $('#status_baliho').val();
	var plt = parseFloat($('#r_luas').autoNumeric('get'));
	var sisi = parseFloat($('#r_muka').val());
	var banyak = parseFloat($('#r_banyak').val());
	var tambahan = parseFloat($('#r_tambahan').autoNumeric('get'))
	var jumlah = Math.round((panjang*lebar*tinggi*sisi*banyak*nsr*r_lama) + tambahan);

	var total = parseFloat($('#r_total').autoNumeric('get'));
	total = Math.round(total + jumlah);
	$('#r_total').autoNumeric('set', total);
	$('#dasar').autoNumeric('set', total);
	hitung_pajak();
	nsr_rp = convertToRupiah(nsr);
	jumlah_rp = convertToRupiah(jumlah);
	tambahan_rp = convertToRupiah(tambahan);



	var aiNew = oTableKD.fnAddData( [ media_id, media, jalan_klas_id, jalan_klas, 
									  panjang, lebar, tinggi, plt, sisi, banyak, r_lama, 
									  nsr_rp, tambahan_rp, jumlah_rp, alamat_reklame, jumlah, status_baliho, 
									  r_jalan_id, '<a class="edit" href="#"><i class="fa fa-edit"></i></a> | <a class="delete" href="#"><i class="fa fa-trash"></i>X</a>'] );
	var nRow = oTableKD.fnGetNodes( aiNew[0] );
	
	$('#r_tinggi').val(1);
	hitung_pajak();
	hitung_denda();

	//CLEAR
    $('#r_media_id').val(0);
    $('#r_media').val('');
    $('#r_jalan').val('');
    $('#r_jalan_id').val(0); 
    $('#r_jalanklas_id').val('');
    $('#r_jalanklas').val('');
    $('#r_panjang').val('');
    $('#r_lebar').val('');
    $('#r_tinggi').val(1);
    $('#r_luas').val('');
    $('#r_muka').val('');
    $('#r_banyak').val('');
    $('#r_lama').val('');
    $('#r_nsr').val('');
    $('#r_tambahan').val(0);
    $('#alamat_reklame').val('');
    $('#r_alamat').val('');
    $('#status_baliho').val(1);

    hitung_pajak();
    is_edit = 0; //grid edit control
	});

	$('#tableKD').on('click', 'a.delete',function (e) {
		var r = confirm("Hapus Baris Data Ini?");
		if (r == true) {
			e.preventDefault();
			var nRow = $(this).parents('tr')[0];
			var data = oTableKD.fnGetData( nRow );
			var val_jml = data[15];

			//hitung kembali row grid jika dihapus
			var total = parseFloat($('#r_total').autoNumeric('get'));
			total = total - val_jml;
			if(total<0){
				total=0;
			}
			$('#r_total').autoNumeric('set', total);
			$('#dasar').autoNumeric('set', total);
			hitung_pajak();
			oTableKD.fnDeleteRow( nRow );
		}
	});

	$('#tableKD').on('click', 'a.edit',function (e) {
		e.preventDefault();
		if(is_edit==0){
			var nRow = $(this).parents('tr')[0];
			var data = oTableKD.fnGetData( nRow );
	        $('#r_media_id_temp').val(data[0]);
	        $('#r_media').val(data[1]);
	        $('#r_jalanklas_id').val(data[2]);
	        $('#r_jalanklas').val(data[3]);
	        $('#r_panjang').autoNumeric('set', data[4]);
	        $('#r_lebar').autoNumeric('set', data[5]);
	        $('#r_tinggi').val(data[6]);
	        $('#r_luas').autoNumeric('set', data[7]);
	        $('#r_muka').val(data[8]);
	        $('#r_banyak').val(data[9]);
	        $('#r_lama').val(data[10]);
	        $('#r_nsr').val(data[11]);
	        $('#r_tambahan').val(data[12]);
	        $('#alamat_reklame').val(data[14]);
	        $('#status_baliho').val(data[16]);
	        $('#r_jalan_id').val(data[17]);
			var val_jml = data[15];
			var total = parseFloat($('#r_total').autoNumeric('get'));
			total = total - val_jml;
			$('#r_total').autoNumeric('set', total);
			$('#dasar').autoNumeric('set', total);
			hitung_pajak();
			oTableKD.fnDeleteRow( nRow );
			get_nsr(data[2], data[0]);
			get_jalan_reklame();
			get_media_reklame();
			is_edit = 1;
		}else{
			alert("Anda Sedang Mengedit Data Sebelumnya. Pastikan Anda Menambahkan ke Daftar Terlebih Dahulu");
		}
	});

	$("#myform").submit(function(eventObj){
		// if ($('#msg_confirm').hasClass('hide')==true) {
			// $('#msg_confirm').removeClass('hide');
			// location.hash = '#msg_confirm';
		// } else {
			// if ($('#submit_ok').is(":checked")) {
				var data = JSON.stringify({
					"dtKD": oTableKD.fnGetData()
				});

				$('<input type="hidden" name="dtKD"/>').val(data).appendTo('#myform');
				// return true;
			// } else
				// alert('Silahkan setujui dokumen ini terlebih dahulu untuk melanjutkan.');
		// }
		
		return true;
	});

	function ddmmyyyy(datedefault){
	    var dd = datedefault.getDate();
	    var mm = datedefault.getMonth()+1;

	    var yyyy = datedefault.getFullYear();
	    if(dd<10){
	        dd='0'+dd
	    } 
	    if(mm<10){
	        mm='0'+mm
	    } 
	    var dateformatted = dd+'-'+mm+'-'+yyyy;
	    return dateformatted;
	}


	function days_between(date1, date2, tipe) {

	    // The number of milliseconds in one day
	    var ONE_DAY = 1000 * 60 * 60 * 24;

	    // Convert both dates to milliseconds
	    var date1_ms = date1.getTime();
	    var date2_ms = date2.getTime();

	    switch (tipe) {
			case 'masa':
				    if (date1_ms>date2_ms){
				    	alert("Invalid Input Masa Pajak");
				    	document.getElementById('masasd').value ='';
				    }else{
				    // Calculate the difference in milliseconds
				    var difference_ms = Math.abs(date1_ms - date2_ms);
				    // Convert back to days and return
				    //return Math.round(difference_ms/ONE_DAY)
					var r_lama_day = Math.round(difference_ms/ONE_DAY);
					document.getElementById('r_lama_day').value = r_lama_day ;
						if($('#masa_id').val()==null){
						document.getElementById('r_lama').value = r_lama_day;
						}else{
						var ms = $('#masa_id').val();
							if (ms==0){ //tahun
								var valmasa = ($('#r_lama_day').val())/365;
							}
							else if (ms==1){ //bulan
								var valmasa = ($('#r_lama_day').val())/30;
							}
							else if (ms==2){ //minggu
								var valmasa = ($('#r_lama_day').val())/7;
							}
							document.getElementById('r_lama').value = Math.round(valmasa);
						}
					}
			break;
			case 'denda':
				    	var difference_ms = date1_ms - date2_ms;
				    	if (difference_ms>0){		//mencegah denda muncul
						var valtelat = 0;
				    	}else{
				    	date1 = ddmmyyyy(date1);
				    	date2 = ddmmyyyy(date2);
						$.ajax({
							url: "<?php echo active_module_url()?>sptpd/get_jdendarek/"+date1+"/"+date2,
							async: false,
							success: function (j) {
								var data = $.parseJSON(j);
								valtelat = data['bulan_telat'];
							},
							error: function (xhr, desc, er) {
								alert(er);
							}
						});																									
						}													
						//$('#denda').autoNumeric('set', denda);
						$('#denda').autoNumeric('set', 0); //jangan muncul
						var pad_bunga = <?=pad_bunga()?> ;
						var pesantelat = 'Terlambat : ' + valtelat+' Bulan ( ' + pad_bunga*valtelat + ' % )';
						$('#pesantelat').html(pesantelat);
			 break;
			case 'denda.old':
				    	var difference_ms = date1_ms - date2_ms;
				    	if (difference_ms>0){		//mencegah denda muncul
						var r_hari_telat = 0;
				    	}else{
				    	var difference_ms = Math.abs(date1_ms - date2_ms);
						var r_hari_telat = Math.round(difference_ms/ONE_DAY);
						}
						var valtelat = Math.round(r_hari_telat/30);
						//Dengan kondisi jika (Bulan Catat - Bulan Jatuh Tempo) > 24 maka 24 else (Bulan Catat - Bulan Jatuh Tempo) 									
						if (valtelat>24){
							valtelat = 24;
						}
						//alert("TELAT "+valtelat+" BULAN");
						var dasar = parseFloat($('#dasar').autoNumeric('get'));
						var tarif = parseFloat($('#tarif').autoNumeric('get'));
						var denda = parseFloat($('#denda').autoNumeric('get'));
						var pad_bunga = <?=pad_bunga()?> ;
						var denda = valtelat*(pad_bunga/100) * (dasar * tarif);																										
																						
						//$('#denda').autoNumeric('set', denda);
						$('#denda').autoNumeric('set', 0); //jangan muncul
						var pesantelat = 'Terlambat : ' + valtelat+' Bulan ( ' + pad_bunga*valtelat + ' % )';
						$('#pesantelat').html(pesantelat);
			 break;
		}
	}

	function set_jatuh_tempo() {
		var masadari = masadari_dtp.date;
		//var masasd = masasd_dtp.date;
		var getdr = new Date(masadari.getTime());
		//var getsd = new Date(masasd.getTime());

		/*
		var jm = $('#jenis_masa').val();
		if (jm == 2){ //mingguan itu bukan masadari - 1
			var jtp = getsd.setDate(masasd.getDate());
		}
		else{
			var jtp = getdr.setDate(masadari.getDate() - 1);
		}
		*/

		var jtp = getdr.setDate(masadari.getDate() - 1);
		jatuhtempotgl_dtp.setValue(jtp);
		jatuhtempotgl_dtp.update();
	}

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

	function hitung_denda() {
		var terimatgl = terimatgl_dtp.date;
		var jatuhtempotgl = jatuhtempotgl_dtp.date;
 		days_between(jatuhtempotgl, terimatgl, 'denda');
	}

	function hitung_pajak() {
		var njop = parseFloat($('#r_njop').autoNumeric('get'));
		var luas = parseFloat($('#r_luas').autoNumeric('get'));
		var dasar = parseFloat($('#dasar').autoNumeric('get'));
		var tarif = parseFloat($('#tarif').autoNumeric('get'));
		var denda = parseFloat($('#denda').autoNumeric('get'));
		

		var kenaikan = 0;
		var kompensasi = 0;
		var tk = parseInt($('#r_tarifid').val());
		switch (tk) {
			case 2:
				tarif_lainnya = (dasar*tarif)* 0.25 ;													
				pajak_terhutang = (dasar*tarif)+denda+tarif_lainnya;	
				break;
			case 3:
				tarif_lainnya = (dasar*tarif)*0.25*(-1);													
				pajak_terhutang = (dasar*tarif)+denda+tarif_lainnya;		
				break;
			case 4:
				tarif_lainnya1 = (dasar*tarif)* 0.25;													
				pajak_terhutang = (dasar*tarif)+tarif_lainnya1;													
				tarif_lainnya2 = pajak_terhutang*0.25*(-1);														
				pajak_terhutang = pajak_terhutang+denda+tarif_lainnya2;
				break;
			default:
				tarif_lainnya = 0	
				pajak_terhutang = (dasar*tarif)+denda+tarif_lainnya;	
		}
        total = Math.round(pajak_terhutang);

		$('#pajak').autoNumeric('set', total);
		$('#r_calculated').autoNumeric('set', total);
        
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

	oTable2 = $('#table_dlg2').dataTable({
		"iDisplayLength": 11,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },

		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					skpd = ''; mID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable2.fnGetData( this );
					mID = data[0];
					skpd = data[1];
					oTable2.$('tr.row_selected').removeClass('row_selected');
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
		"sAjaxSource": "<?=active_module_url('subjek_pajak/grid_for_skpd/'.pad_reklame_id());?>"
	});

	var tb_array = [
		'<br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);
	
	oTable3 = $('#table_dlg3').dataTable({
		"iDisplayLength": 11,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },

		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					jID = ''; kjID = '';nmjID = ''; nmjID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable3.fnGetData( this );
					jID = data[0];
					kjID = data[1];
					nmkjID = data[2];
					nmjID = data[3];


					oTable3.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			jID = ''; kjID = '';nmjID = ''; nmjID = '';
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
		"sAjaxSource": "<?=active_module_url('subjek_pajak/grid_for_jalan')?>"
	});

	var tb_array = [
		'<br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);


	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url($this->uri->segment(2));?>'+'index/'+'<?=$this->uri->segment(4);?>'
	});
	
	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			get_cu(mID, cuID, pID, nopd);
			$('#customer_usaha_id').trigger('change');
			$('#cuDialog').modal('hide');
		}
	});

	$('#btn_dialog_ok2').click(function() {
		if (skpd == '' || skpd == null)
			alert('Data belum dipilih.');
		else {
			$('#skpd_old').val(skpd);
			$('#cuDialogSkpd').modal('hide');
		}
	});


	$('#btn_dialog_ok3').click(function() {
		if (jID == '' || jID == null)
			alert('Data belum dipilih.');
		else {
			$('#r_jalan').val(nmjID);
			$('#r_jalan_id').val(jID);
			$('#r_jalanklas').val(nmkjID);
			$('#r_jalanklas_id').val(kjID);
			$('#r_tambahan').val(0);
			$('#cuDialogJalan').modal('hide');
			//Get Media
			get_media_reklame();
		}
	});

	$('#r_njop, #dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak, #r_calculated, #r_kontrak, #r_nsr, #r_banyak, #r_total, #r_tambahan').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '9999999999999999.99', mDec: '0'
	});
    
	$('#r_panjang, #r_lebar, #tarif, #r_tinggi, #r_luas').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
	});

	$('#r_lama, #r_banyak, #r_muka').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999',  mDec: '0' 
	});

	$('#r_panjang, #r_lebar, #r_tinggi, #r_muka, #r_banyak').keyup(function() {
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
		set_jatuh_tempo();
		hitung_pajak();
		days_between(masadari_dtp.date, masasd_dtp.date, 'masa');
		hitung_denda();
		$('#r_tinggi').val(1);
	});

	$('#pajak_id').change(function() {
		get_pajak_detail();
		set_masa_sd();
		set_jatuh_tempo();
		hitung_pajak();
		days_between(masadari_dtp.date, masasd_dtp.date, 'masa');
		hitung_denda();
	});
    
	$('#r_nsr_id').change(function() {
		get_nsr_val(this.value);
		hitung_pajak();
	});

	$('#status_baliho').change(function() {
		if ($('#r_media_id').val()==0){
			alert("Media Belum Dipilih");
			$('#status_baliho').val(1);
		}else{
			if (this.value==1){
				document.getElementById('r_tambahan').value = 0;
			}else if (this.value==2){
				var tbh = parseFloat($('#r_nsr').autoNumeric('get')*0.5);
				document.getElementById('r_tambahan').value = convertToRupiah(tbh);
			}
		}

	});

	$('#r_jalan_id').change(function() {
		//get_klas_jalan(this.value);
		$('#r_jalanklas_id').trigger('change');
		days_between(masadari_dtp.date, masasd_dtp.date, 'masa');

	});
    
	$('#r_jalanklas_id').change(function() {
    	//document.getElementById('r_jalanklas').value = this.options[this.selectedIndex].text;
        //get_jalan_val(this.value);
		hitung_pajak();
		days_between(masadari_dtp.date, masasd_dtp.date, 'masa');
	});
    
	$('#r_lokasi_pasang_id').change(function() {
		get_lokasi_pasang_val(this.value);
		hitung_pajak();
	});
    
	$('#r_sudut_pandang_id').change(function() {
		get_sudut_pandang_val(this.value);
		hitung_pajak();
	});
    
    $('#r_media_id').change(function() {
    	if($('#r_jalan_id').val()==''){
    		alert("Jalan Belum Dipilh!");
    		$('#r_media_id').val(0);
    	}else{
	    	document.getElementById('r_media').value = this.options[this.selectedIndex].text;
	    	var kelas_jalan_id = $('#r_jalanklas_id').val();
	    	var media = this.options[this.selectedIndex].text; 
			var media_id = $('#r_media_id').val();
			get_nsr(kelas_jalan_id, media_id);
			set_jatuh_tempo();
			hitung_pajak();
		}
	});

	$('#btn_cek_ijin').click(function() {
    	var ijin_no = $('#ijin_no').val();

		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/cek_ijin/"+ijin_no,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);
				var ijin = data['ijin_no'];
				if(ijin>0){
					alert("No. Ijin Sudah Ada!");
					document.getElementById('ijin_no').value ='';
				}else{
					alert("No. Ijin "+ijin_no+" Belum Ada.");
					document.getElementById('ijin_no').value = ijin_no;
				}
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
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
		/* set_masa_sd();*/
		set_jatuh_tempo(); 
		$('#pajak_id').trigger('change');
		masadari_dtp.hide();
		days_between(masadari_dtp.date, masasd_dtp.date, 'masa');
		hitung_denda();
	}).data('datepicker');

	var masasd_dtp = $('#masasd').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		masasd_dtp.hide();
		days_between(masadari_dtp.date, masasd_dtp.date, 'masa');
	}).data('datepicker');

	var jatuhtempotgl_dtp = $('#jatuhtempotgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		jatuhtempotgl_dtp.hide();
	}).data('datepicker');

	var tglijin_dtp = $('#tgl_ijin').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tglijin_dtp.hide();
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

	/* init page */
    $('#r_nsr_id').trigger('change');
    $('#r_jalan_id').trigger('change');
    $('#r_lokasi_pasang_id').trigger('change');
    $('#r_sudut_pandang_id').trigger('change');
    
   // $('#r_jalan_id').combobox({bsVersion: '2'});
    //$('#r_jalan_id').trigger('change')
    
	var c_id  = parseInt(<?=$dt['customer_id']?>);
	var cu_id = parseInt(<?=$dt['customer_usaha_id']?>);
	var def_pajak_id = parseInt(<?=$this->uri->segment(7);?>);

	if (!isNaN(c_id)) {
		get_cu(c_id, cu_id, def_pajak_id);
		hitung_pajak();
	}
});

$(document).keypress(function(event){
	if (event.which == '13') {
		event.preventDefault();
	}
});

</script>


<?
	$this->session->set_userdata("mode",$this->uri->segment(3));
	$this->session->set_userdata("usaha_id",$this->uri->segment(4));
?>

<div class="content">
	<!-- Modal -->
<div id="cuDialog" class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Wajib Pajak - Usaha : REKLAME</h4>
				</div>
				<div class="modal-body">
					<table class="table" id="table_dlg1">
						<thead>
							<tr>
								<th>index</th>
								<th>NOPD</th>
								<th>Nama Usaha</th>								
								<th>NPWPD</th>
								<th>Wajib Pajak</th>
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

	<div id="cuDialogSkpd" class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Wajib Pajak - Usaha : REKLAME</h4>
				</div>
				<div class="modal-body">
					<table class="table" id="table_dlg2">
						<thead>
							<tr>
								<th>index</th>
								<th>No. SKPD</th>
								<th>Tanggal</th>								
								<th>Wajib Pajak</th>
								<th>Objek Pajak</th>
								<th>Pajak</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<p>&nbsp;</p>	
				<p>&nbsp;</p>				
				<div class="modal-footer">
					<button class="btn btn-primary" id="btn_dialog_ok2">OK!</button>
					<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Batal</button>
				</div>
			</div>
		</div>
	</div>

		<div id="cuDialogJalan" class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">PILIH JALAN</h4>
				</div>
				<div class="modal-body">
					<table class="table" id="table_dlg3">
						<thead>
							<tr>
								<th>Jalan ID</th>
								<th>Kelas Jalan ID</th>							
								<th>Kelas Jalan</th>
								<th>Nama Jalan</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<p>&nbsp;</p>	
				<p>&nbsp;</p>				
				<div class="modal-footer">
					<button class="btn btn-primary" id="btn_dialog_ok3">OK!</button>
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
			<?=msg_block();?>
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
            
			<input class="form-control" type="hidden" name="r_njop" id="r_njop" value="<?=$dt['r_njop']?>" placeholder="r_njop" />
			<input class="form-control" type="hidden" name="r_jalanklas_val" id="r_jalanklas_val" value="<?=$dt['r_jalanklas_val']?>" placeholder="r_jalanklas_val" />
			<input class="form-control" type="hidden" name="r_lokasi_pasang_val" id="r_lokasi_pasang_val" value="<?=$dt['r_lokasi_pasang_val']?>" placeholder="r_lokasi_pasang_val" />
			<input class="form-control" type="hidden" name="r_sudut_pandang_val" id="r_sudut_pandang_val" value="<?=$dt['r_sudut_pandang_val']?>" placeholder="r_sudut_pandang_val" />
			<input class="form-control" type="hidden" name="multiple" id="multiple" value="<?=$dt['multiple']?>" readonly />

			<div class="row col-sm-12">
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="sptno">No. </label>
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
                        <label class="control-label col-sm-3"  for="type_id">Jenis Dok.</label>
                        <div class="col-sm-6">
                            <?=$select_sptpd_type;?>
                        </div>
                    </div>    
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-5"  for="terimatgl">Tgl.Ijin</label>
                        <div class="col-sm-5">
                            <input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" />
                        </div>
                    </div> 
					<div class="form-group col-sm-6">
					<label class="control-label col-sm-3"  for="r_lokasi">Lokasi</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" name="r_lokasi" id="r_lokasi" value="<?=$dt['r_lokasi']?>"  />
						</div>
					</div>	
					<div class="form-group col-sm-6">
					<label class="control-label col-sm-5" for="skpd_old" >SKPD Sebelumnya</label>
					<div class="col-sm-5">
						<input class="form-control" type="text" name="skpd_old" id="skpd_old" value="<?=$dt['skpd_old']?>"/>
					</div>
					<div class="form-group col-sm-1">
						<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#cuDialogSkpd">Cari</button>
					</div>
					</div>
					<div class="form-group col-sm-6">
						<label class="control-label col-sm-3" for="cara_bayar">Cara Bayar</label>
						<div class="col-sm-6">
							<select class="form-control" name="cara_bayar" id="cara_bayar">
								<?if ($dt['cara_bayar']==1) {?>
								<option value=1 selected> Transfer </option>
								<option value=2> Teller / ATM </option>	
								<?}else if ($dt['cara_bayar']==2){?>
								<option value=2 selected> Teller / ATM </option>	
								<option value=1 > Transfer </option>
								<?}else{?>
								<option value=2> Teller / ATM </option>					
								<option value=1 > Transfer </option>
								<?}?>
							</select>
						</div>
						<div class="col-sm-12">
                    	</div>
					</div>
					<div class="col-sm-12">
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="ijin_no">No Ijin</label>
                        <div class="col-sm-7">
	                        <input style="width:15%" class="form-control2" type="text" value="<?=$this->session->userdata('pad_surat_no');?>"/> /
	                        <input style="width:20%" class="form-control2" type="text" name="ijin_no" id="ijin_no" value="<?=$dt['ijin_no']?>" placeholder="No. Ijin" autocomplete="off" required/> -
	                        <input style="width:30%" class="form-control2" type="text" value="<?=$this->session->userdata('pad_ijin_kd');?>"/> / 
	                        <input style="width:20%" class="form-control2" type="text" value="<?=pad_tahun_anggaran();?>"/>
                        </div>
                        <div class="col-sm-1">
                        <button type="button" class="btn btn-danger btn-xs"id="btn_cek_ijin">Cek Ijin</button >
                        </div>
                    </div> 
                    <div class="form-group col-sm-6">
                        <label class="control-label col-sm-5"  for="r_judul">Naskah Reklame</label>
						<div class="col-sm-7">
							<input class="form-control" type="text" name="r_judul" id="r_judul" value="<?=$dt['r_judul'];?>"/>
						</div>
						<label class="control-label col-sm-5"  for="notes">Keterangan</label>
						<div class="col-sm-7">
							<input class="form-control" type="text" name="notes" id="notes" value="<?=$dt['notes'];?>"/>
						</div>
                    </div> 

                    <div class="col-sm-12">
					</div> 

                    <div class="form-group col-sm-6">
                        <div class="form-group col-sm-12">
						</div>
                        <label class="control-label col-sm-3"  for="nopd">NOPD</label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="nopd" id="nopd" value="<?=$dt['nopd']?>" placeholder="NOPD" autocomplete="off" />
                        </div>
                        <div class="form-group col-sm-3">
						<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#cuDialog">Cari</button>
						</div>
						<label class="control-label col-sm-3"  for="npwpd">Wajib Pajak</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" autocomplete="off"  readonly />
							<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" placeholder="Nama Wajib Pajak" autocomplete="off" readonly/>
						</div>
						<div class="form-group col-sm-12">
						</div>
						<label class="control-label col-sm-3 hide"  for="pnama">Pemilik/Alamat</label>
						<div class="col-sm-9 hide">
							<input class="form-control" type="text" name="wpnama" id="wpnama" value="" autocomplete="off" readonly />
							<input class="form-control" type="text" name="wpalamat" id="wpalamat" value="" autocomplete="off" readonly />
						</div>
						<div class="form-group col-sm-12">
						</div>
						<label class="control-label col-sm-3"  for="no_telp">No. Telp</label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="no_telp" id="no_telp" value="<?=$dt['no_telp']?>"  />
						</div>
					</div>	
			</div>		
			<div class="row col-sm-12">
					<div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="usaha_id">Jenis Usaha</label>
                        <div class="col-sm-9">
                            <?=$select_usaha;?>
                        </div>
                    </div>
 			</div>
			<div class="row col-sm-12">
					<div class="form-group col-sm-6">
						<label class="control-label col-sm-3"  for="pajak_id">Jenis Pajak</label>
						<div class="col-sm-9">
							<?=$select_pajak;?>
						</div>
					</div>
					<div class="col-sm-12">
					</div>
					<div class="form-group col-sm-6">
						<label class="control-label col-sm-3"  for="tarif">Tarif</label>
						<div class="col-sm-4">
							<input class="form-control" type="hidden" style="text-align:right;" name="tarif" id="tarif" value="<?=$dt['tarif']?>" required readonly/>
							<input class="form-control" type="text" style="text-align:right;" name="tarif_view" id="tarif_view" value="<?=$dt['tarif']*100?> %" required readonly/>
						</div>
					</div>	
					<div class="col-sm-12">
					</div>
					<div class="form-group col-sm-6">
                        <label class="control-label col-sm-3"  for="r_sudut_pandang_id">Sudut Pandang</label>
                        <div class="col-sm-6">
                            <?=$select_sudut_pandang;?>
                        </div>
                    </div>
					<div class="col-sm-12">
					</div>
					<div class="form-group col-sm-6">
					<label class="control-label col-sm-3"  for="jenis_masa">Jenis Masa</label>
                    <div class="col-sm-3">
                    	<input readonly class="form-control" type="text" style="text-align:right;" name="jenis_masa_view" id="jenis_masa_view"  />
                        <input class="form-control hidden" type="text" name="jenis_masa" value="<?=$dt['jenis_masa']?>" id="jenis_masa" readonly/>
                    </div>
                    <div class="col-sm-12">
					</div>
                    <label class="control-label col-sm-3"  for="masadari">Masa Berlaku</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="masadari" id="masadari" value="<?=$dt['masadari']?>" placeholder="dd-mm-yyyy" />
                    </div>
                    <label class="control-label col-sm-3"  for="masasd">s / d </label>
                    <div class="col-sm-3">                   
                        <input class="form-control" type="text" name="masasd" id="masasd" value="<?=$dt['masasd']?>" placeholder="dd-mm-yyyy"/>
                    </div> 
                    <div class="col-sm-12">
                    <label class="control-label col-sm-3 hide"  for="masapajak_bulan">Masa Pajak</label>
                    <div class="col-sm-3">
                        <input class="form-control hide" type="text" name="masapajak_bulan" id="masapajak_bulan" value="<?$masapajak_bulan = substr($dt['masadari'], -7); echo $masapajak_bulan;?>" placeholder="mm-yyyy"/>
                    </div>
         			</div>
                    <label class="control-label col-sm-3"  for="jatuhtempotgl">Jatuh Tempo</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="jatuhtempotgl" id="jatuhtempotgl" value="<?=$dt['jatuhtempotgl']?>" placeholder="dd-mm-yyyy" required/>
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
				<li class="active"><a href="#media" data-toggle="tab"><strong>Media & Perhitungan</strong></a></li>
			</ul>
			<div class="tab-content">
			<div class="tab-pane fade in active" id="media">
				<br>
				<div class="box box-primary box-solid">
					<div class="box-body">
					<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-6">
						<b class="pull-left"><u> JALAN & MEDIA REKLAME</u></b>
						<p>&nbsp;</p>
						</div>
						<div class="col-sm-12">
                  		</div>
                        <label class="control-label col-sm-6"  for="r_jalan_id"> Jalan</label>
                        <div class="col-sm-5">
						<input type="hidden" name="r_jalan_id" id="r_jalan_id"  readonly/>
						<input type="hidden" name="r_media_id_temp" id="r_media_id_temp"  readonly/>

						<input class="form-control" type="text" name="r_jalan" id="r_jalan"  readonly/>
                            
                        <?//$select_jalan;?>
                        </div>
                        <div class="form-group col-sm-1">
							<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#cuDialogJalan">Cari</button>
						</div>
                        <label class="control-label col-sm-6"  for="r_jalanklas_id">Kelas Jalan</label>
						<input class="form-control" type="hidden" name="r_jalanklas_id" id="r_jalanklas_id"  readonly/>
                        <div class="col-sm-6">
						<input class="form-control" type="text" name="r_jalanklas" id="r_jalanklas" readonly />
                            <?//$select_jalan_kelas;?>
                        </div>
                        <label class="control-label col-sm-6"  for="r_media">Media</label>
						<input type="hidden" name="r_media" id="r_media"/>
                        <div class="col-sm-6">
                            <?=$select_media_reklame;?>
                        </div>
                       <label class="control-label col-sm-6"  for="status_baliho">Posisi Baliho</label>
                        <div class="col-sm-6">
                           <select id="status_baliho" class="form-control" name="status_baliho">
                           	<option value=1>Outdoor</option>
                           	<option value=2>Indoor</option>
                           </select>
                        </div>
                        <label class="control-label col-sm-6"  for="r_tambahan">Tambahan</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_tambahan" id="r_tambahan" readonly />
						</div>
                    </div>
                    <div class="col-sm-12">
                    </div>

					<div class="col-sm-6">
						<div class="col-sm-6">
						<b class="pull-left"><u> UKURAN </u></b>
						<p>&nbsp;</p>
						</div>
						<div class="col-sm-12">
                  		</div>
						<label class="control-label col-sm-6"  for="r_panjang">Panjang</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_panjang" id="r_panjang" />
						</div>
						<label class="control-label col-sm-6"  for="r_lebar">Lebar</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_lebar" id="r_lebar" />
						</div>
						<label class="control-label col-sm-6 hide"  for="r_tinggi">Tinggi</label>
						<div class="col-sm-6 hide">
							<input class="form-control" type="text" style="text-align:right;" name="r_tinggi" id="r_tinggi" value = "1"/>
						</div>
						<label class="control-label col-sm-6"  for="r_luas">P x L</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_luas" id="r_luas" readonly />
						</div>
						<p>&nbsp;</p>
						<p>&nbsp;</p>

						<div class="col-sm-6">
						<button type="button" class="btn btn-success" id="btn_dth_new">Tambahkan ke Daftar</button>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">
						<b class="pull-left"><u> JUMLAH & MASA </u></b>
						<p>&nbsp;</p>
						</div>
						<div class="col-sm-12">
                  		</div>	
						<label class="control-label col-sm-6"  for="r_muka">Jumlah Sisi</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_muka" id="r_muka"  />
						</div>
						<label class="control-label col-sm-6"  for="r_banyak">Banyaknya</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_banyak" id="r_banyak" />
						</div>
						<label class="control-label col-sm-6"  for="r_lama">Lama Waktu</label>
						<div class="col-sm-3">
							<input type="hidden"name="masa_id" id="masa_id" />
							<input type="hidden"name="r_lama_day" id="r_lama_day" />

							<input class="form-control" type="text" style="text-align:right;" name="r_lama" id="r_lama" />
						</div>
						<div class="col-sm-3">
							<label id="masatext">Hari</label>
						</div>
						<p>&nbsp;</p>

						<label class="control-label col-sm-6"  for="alamat_reklame">Tempat Reklame</label>
						<div class="col-sm-6">
						<textarea class="form-control" type="text" name="alamat_reklame" id="alamat_reklame" rows="3" cols="50">
							
							</textarea>
						</div>
						<p>&nbsp;</p>
						<label class="control-label col-sm-6"  for="r_nsr">Nilai Sewa Reklame</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="r_nsr" id="r_nsr" readonly />
						</div>
					</div>

					<div class="col-sm-12">
						<table class="table" id="tableKD">
							<thead>
								<tr>
									<th>Media ID</th>
									<th>Media</th>

									<th>Jalan Kelas ID</th>
									<th>Kelas Jalan</th>
									<th>Panjang</th>
									<th>Lebar</th>
									<th>Tinggi</th>
									<th>P x L</th>
									<th>Sisi</th>
									<th>Banyak</th>
									<th>Lama</th>
									<th>Nilai Sewa</th>
									<th>Tambahan</th>
									<th>Jumlah</th>
									<th>alamat</th>
									<th>Batal</th>
									<th>Batal</th>
									<th>Batal</th>
									<th>Batal</th>


								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div class="col-sm-7">
					</div>
					<div class="col-sm-2">
					<b class="pull-right">TOTAL :</b>
					</div>
					<div class="col-sm-3">
					<input class="form-control" type="text" style="text-align:right;" name="r_total" id="r_total" value="<?=$dt['dasar']?>" readonly />
					</div>

					</div>
					</div>
					</div>

								<div class="box box-primary box-solid">
				<div class="box-body">
				<div class="row">


			<div class="form-group col-sm-5">
			<label class="control-label col-sm-4"  for="dasar">DPP</label>
				<div class="col-sm-6">
					<input class="form-control" type="text" style="text-align:right;" name="dasar" id="dasar" value="<?=$dt['dasar']?>" />
				</div>
			<label class="control-label col-sm-4"  for="r_tarifid">Tarif Lainnya</label>
				<div class="col-sm-6">
				<?=$select_tarif;?>
				</div>
			</div>
			<div class="form-group col-sm-5">
			<label class="control-label col-sm-12" id="pesantelat"></label>
				<label class="control-label col-sm-6"  for="denda">Denda</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="denda" id="denda" value="<?=$dt['denda']?>" readonly/>
				</div>
				<label class="control-label col-sm-6"  for="pajak"><strong>Pajak Terhutang</strong></label>
				<div class="col-sm-6">
					<input class="form-control" type="text" style="text-align:right;" name="pajak" id="pajak" readonly />
				</div>
				<label class="control-label col-sm-6 hide"  for="r_calculated">Calculated</label>
				<div class="col-sm-6">
					<input class="form-control hide" type="text" style="text-align:right;" name="r_calculated" id="r_calculated" value="<?=$dt['r_calculated']?>" readonly />
				</div>
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
					</div-->
					
				</div>
			</div>
			
		<?php echo form_close();?>
	</div>
</div>
<? $this->load->view('_foot'); ?>