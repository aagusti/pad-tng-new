<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.form-control {
  height: 24px;
  padding: 0px 12px;
  font-size: 12px;
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
var global_pajak_id;
var global_cu_id;
var is_edit;
var global_end_loop;

	function hitung_denda(bulantahun){
		var d = new Date();
		var bulan_sekarang = d.getMonth();
		var tahun_sekarang = d.getFullYear();
		var year = parseInt(bulantahun.substr(3,6));
		var month = parseInt(bulantahun.substr(0, 2));

		selisih_tahun = tahun_sekarang-year;
		if(selisih_tahun == 0){
		selisih_masa = bulan_sekarang - month;
	    }
	    else if(selisih_tahun == 1){
		selisih_masa = bulan_sekarang + (12-month);
	    }
	    else if(selisih_tahun == 2){
		selisih_masa = bulan_sekarang + (12-month) + 12;
	    }
	    else {
		selisih_masa = bulan_sekarang + (12-month) + 24;
	    }

		var pajak      = parseFloat($('#pajak_calculated').autoNumeric('get'));
		if(selisih_masa>=24){selisih_masa=24;};
		var bunga = <?=pad_bunga()/100;?>;
		denda=pajak*bunga*selisih_masa;
		//document.getElementById('denda').value = denda
		$('#denda').autoNumeric('set', denda);
		$('#pesandenda').html('(Terlambat ' + selisih_masa + ' Bulan)');
		$('#pajak').autoNumeric('set', pajak + denda);
		//hitung_pajak() ;
		//var denda      = parseFloat($('#denda').autoNumeric('get'));
		//total_bayar	= denda + pajak  ;
		//$('#total_bayar').autoNumeric('set', total_bayar);
	}


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
	global_cu_id = cu_id;
}

function set_so(type_id) {
	if (type_id == <?=pad_dok_self_id()?>)
		$('#so').val('S');
	else
		$('#so').val('O');
}


function get_sumur() {
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

	var mode = '<?=$this->uri->segment(3);?>';
	if (mode =='edit'){
	is_edit = parseInt(<?=$this->uri->segment(6);?>);
	}else{
		is_edit = 'x';
	}

	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_sumur/"+global_cu_id+"/"+is_edit,
		async: false,
		success: function (sumur_data) {
			$('#sumur_data').html(sumur_data);
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});

	$('#total_volume').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});

	$('#volume').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});

	var end_loop = document.getElementById('end_loop_sumur').value;
	$('#end_loop_sumur').hide();

	for(var i=1; i<=end_loop; i++){ 
	$('#meteran_akhir'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});
	$('#meteran_awal'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});
	$('#volume'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});
	$('#meteran_akhir'+i).keyup(function() {
		hitung_volume();
	});
	$('#meteran_awal'+i).keyup(function() {
		hitung_volume();
	});
	$('#volume'+i).keyup(function() {
		hitung_volume3();
	});


	if(mode =='edit'){
		global_end_loop = end_loop;
		set_sumur_form();
		hitung_volume();
		hitung_npa();
	}

	var masapajak_bulan = document.getElementById('masapajak_bulan').value;
	document.getElementById('omset_masa_pajak').innerHTML = masapajak_bulan ;
	}
}

function set_sumur_form() {
	for(var i=1; i<=global_end_loop; i++){ 
	$('#meteran_akhir'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});
	$('#meteran_awal'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});
	$('#volume'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.999'
		});
	}
}


function get_pajak_detail() {
	var pajak_id = $('#pajak_id').val();
	var masadari = $('#masadari').val();
	var total_volume = parseFloat($('#volume').autoNumeric('get'));
	if (masadari == '') {
		var sekarang = new Date();
		/*
		$('#masadari').val(sekarang.getDate()+"-"+sekarang.getMonth()+"-"+sekarang.getFullYear());
		masadari = $('#masadari').val();
		*/
		masadari = sekarang.getDate()+"-"+sekarang.getMonth()+"-"+sekarang.getFullYear();
	}

	$.ajax({
		url: "<?php echo active_module_url()?>sptpd/get_pajak_detail/"+global_pajak_id+"/"+masadari+"/"+total_volume,
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);

			var tarif = parseFloat(data['tarif']);
			$('#tarif').autoNumeric('set', tarif);
			
			$('#minimal_omset').val(data['minimal_omset']);
			$('#r_bayarid').val(data['masapajak']);
			$('#r_nsr').val(data['reklame']);
			$('#m_njop').val(data['reklame']);
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
			

			

			//document.getElementById('nopd').value = nopd ;
			document.getElementById('view_tarif').value = (tarif*100)+' %';
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});
}

function hitung_pajak() {
	var dasar      = parseFloat($('#dasar').autoNumeric('get'));
	dasar          = isNaN(dasar) ? 0 : dasar;
	var volume     = parseFloat($('#volume').autoNumeric('get'));
	volume         = isNaN(volume) ? 0 : volume;
	var tarif      = parseFloat($('#tarif').autoNumeric('get'));
	tarif          = isNaN(tarif) ? 0 : tarif;
	
	var pajak = (dasar*tarif);
    pajak = Math.round(pajak);

	if (dasar < minimal_omset)
		$('#pajak').autoNumeric('set', 0);
	else{
		$('#pajak_calculated').autoNumeric('set', pajak);
		<?if ($this->uri->segment(3)!=='edit') { ?>
		$('#pajak').autoNumeric('set', pajak);
		<?}?>
	}
}

function hitung_volume2() {
	var omset=[31];
	var volume = 0;
	for(var i=1; i<=31; i++){
	omset[i] =parseFloat($('#omset'+i).autoNumeric('get'));
	volume = volume + omset[i];
	}
	if (volume > 0 )
		document.getElementById("volume").readOnly = true;
	else
		document.getElementById("volume").readOnly = false;
	//dasar = volume
	$('#volume').autoNumeric('set', volume);
}

function hitung_volume() {
	var end_loop = document.getElementById('end_loop_sumur').value;
	var omset=[end_loop];
	var total_volume = 0;
	var meteran_akhir = [];
	var meteran_awal =[];
	var jml =[];
	for(var i=1; i<=end_loop; i++){
	meteran_akhir[i] = parseFloat($('#meteran_akhir'+i).autoNumeric('get'));
	meteran_awal[i] = parseFloat($('#meteran_awal'+i).autoNumeric('get'));
	jml[i] = meteran_akhir[i]-meteran_awal[i];
	$('#volume'+i).autoNumeric('set', jml[i]);
	volume[i] =parseFloat($('#volume'+i).autoNumeric('get'));
	total_volume = total_volume + volume[i];
	}
	$('#total_volume').autoNumeric('set', total_volume);
	$('#volume').autoNumeric('set', total_volume);
}

function hitung_volume3() {
	var end_loop = document.getElementById('end_loop_sumur').value;
	var omset=[end_loop];
	var total_volume = 0;
	for(var i=1; i<=end_loop; i++){
	$('#volume'+i).autoNumeric('set', parseFloat($('#volume'+i).autoNumeric('get')));
	volume[i] = parseFloat($('#volume'+i).autoNumeric('get'));
	total_volume = total_volume + volume[i];
	}
	$('#total_volume').autoNumeric('set', total_volume);
	$('#volume').autoNumeric('set', total_volume);
}

function hitung_npa(){
		/*cek jika ada selisihnegatif*/
		var end_loop = document.getElementById('end_loop_sumur').value;
		var con = true;
		for(var i=1; i<=end_loop; i++){
			meteran_akhir = parseFloat($('#meteran_akhir'+i).autoNumeric('get'));
			meteran_awal = parseFloat($('#meteran_awal'+i).autoNumeric('get'));
			jml = meteran_akhir-meteran_awal;
			if (jml<0){
			var r = confirm("Ada Meteran Akhir yang Lebih Kecil dari Meteran Awal! Lanjutkan?");
				if (r==true){
					con = true;
				}else{
					con = false;
				}
			}
		}

		/*end check*/
		if (con == true){
		var golongan_id = document.getElementById('golongan_id').value;
		var air_zona_id = document.getElementById('zona_id').value;
		var kelompok_usaha_id = document.getElementById('kelompok_usaha_id').value;
		var vol_max = parseFloat($('#volume').autoNumeric('get'));
		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/hitung_npa/"+golongan_id+"/"+air_zona_id+"/"+kelompok_usaha_id+"/"+vol_max,
			async: false,
			success: function (data) {
				$("#npa_data").html(data);
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		var npa = $("#final_npa").val();
		$('#dasar').autoNumeric('set', npa);
		hitung_pajak();
		hitung_denda(global_bulantahun);
		}
}

//Kelompok Usaha Control
/*
function reload_grid_start() {
	oTable2.fnReloadAjax("<? echo active_module_url('air_kelompok_usaha'); ?>grid_for_hda");
	$('#btn_filter_back').hide();
}

function reload_grid() {
	induk_id  = mID;
	oTable2.fnReloadAjax("<? echo active_module_url('air_kelompok_usaha'); ?>grid_for_hda_filtered/"+induk_id+"/"+level_id);
}

function reload_grid_back() {
	var induk_id  = mID;
	oTable2.fnReloadAjax("<? echo active_module_url('air_kelompok_usaha'); ?>grid_for_hda_filtered/"+induk_id+"/"+level_id);
}
*/

function reload_grid_history() {
	var cust_id = $("#customer_id").val();
	if (cust_id ==""){
		cust_id = 0;
	}
	oTable3.fnReloadAjax("<?=active_module_url('sptpd/grid_air_tanah_history');?>"+cust_id);
}

function switch_btn(level_id) {
    if (level_id == 2) {
		$('#btn_filter_induk').show();
		$('#btn_filter_induk2').hide();
		$('#btn_filter_back').show();
    } else if (level_id == 1) {
		$('#btn_filter_induk2').show();
		$('#btn_filter_induk').hide();
		$('#btn_filter_back').show();
    }
}
<!-- // -->
var mID;
var cuID;
var pID;
var nopd;
var cu_id;
var oTable;
var oTable2;
var oTable3;
var kelompok_usaha;
var global_bulantahun;

$(window).load(function(){ 
		hitung_volume3();
		$('#btn_filter_induk2').hide();
		$('#btn_filter_back').hide();
		$('#hitung_npa').hide();

 });

$(document).ready(function() {

	function get_cu(customer_id, cu_id,def_pajak_id) {
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
				$("#kelompok_usaha_id").val(data['kelompok_usaha_id']);
				$("#kelompok_usaha").val(data['kelompok_usaha']);
				$("#zona_id").val(data['zona_id']);
				$("#zona").val(data['zona']);
				$("#golongan_id").val(data['golongan_id']);
				$("#golongan").val(data['golongan']);
				$("#manfaat_id").val(data['manfaat_id']);
				$("#manfaat").val(data['manfaat']);

				/* $('#usaha_id').html(data['usaha']); //change to */
				$('#customer_usaha_id').html(data['usaha']);
				$("#customer_usaha_id option").not(":selected").attr("disabled", "disabled");
				var nama = document.getElementById('nama').value;
				document.getElementById('display_nama').innerHTML = nama ;

								//SET JTP
				<?if ($this->uri->segment(3)!=='edit') { ?>
				document.getElementById('masapajak_bulan').value = data['masapajak_bulan'];
				var lang = 'en' ;
				str = "01"+"-"+document.getElementById('masapajak_bulan').value ;
				masadari = GetDate(str,lang) ;
				document.getElementById('masadari').value = masadari;
				global_bulantahun = masadari.substr(3, 9);
				<? } ?>


				<?if ($this->uri->segment(3)=='edit') { ?>
				var lang = 'en' ;
				str = "01"+"-"+document.getElementById('masapajak_bulan').value ;
				masadari = GetDate(str,lang) ;
				document.getElementById('masadari').value = masadari;
				global_bulantahun = masadari.substr(3, 9);
				<? } ?>
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		global_pajak_id=def_pajak_id;
		reload_grid_history() ; //tampilkan history air tanah
	}

	function set_jatuh_tempo(bulantahun) {
		//if ($('#masadari').val() == '' || $('#terimatgl').val() == '') return;
		//if ($('#terimatgl').val() == '') return;
		var jt = parseInt($('#jatuhtempo').val());
		var jtp;
		//var masadari = masadari_dtp.date;
		//var tetap_tgl = terimatgl_dtp.date;
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
					if (month==1){
					var msd = 30+"-"+month+"-"+year ;
					jtp = feb+"-"+nextmonth+"-"+year ; 
					}
					if (month==0 || month==3 || month==5 || month==7 || month==8 || month==10 || month==12){
					msd = 31+"-"+month+"-"+year ;
					jtp = 30+"-"+nextmonth+"-"+year ;
						if (month == 7 || month == 12){
						jtp = 31+"-"+nextmonth+"-"+year ;}
					}
					if (month==2 || month==4 || month==6 || month==9 || month==11 ){
					var msd = 30+"-"+month+"-"+year ;
						if (month == 2){
						msd = feb+"-"+month+"-"+year ; }
					jtp = 31+"-"+nextmonth+"-"+year ;
					}
					document.getElementById('jatuhtempotgl').value = jtp ;
					document.getElementById('masasd').value = msd ;
					break;
				/* 30 hari dari tgl terima/tetap 		
				case 1:
					jtp = new Date(tetap_tgl.getFullYear(), tetap_tgl.getMonth(), tetap_tgl.getDate() +30);
					jatuhtempotgl_dtp.setValue(jtp);
					jatuhtempotgl_dtp.update();
					break;
						*/	
				case 1:
					//jtp = new Date(masadari.getFullYear(), masadari.getMonth()+2, 1)-1;
					var year = parseInt(bulantahun.substr(3,6));
					var month = parseInt(bulantahun.substr(0, 2));

					if (year%4==0){var feb=29;}else{var feb=28;}
					
					var nextmonth = month + 1 ;
					if (nextmonth == 13) {nextmonth=1; year=year+1;}
					//jtp = new Date(year, month + 1, 0);
					//jtp = new Date( (new Date(year, month,1))-1 );;
					if (month==1){
					var msd = 30+"-"+month+"-"+year ;
					jtp = feb+"-"+nextmonth+"-"+year ; 
					}
					if (month==0 || month==3 || month==5 || month==7 || month==8 || month==10 || month==12){
					msd = 31+"-"+month+"-"+year ;
					jtp = 30+"-"+nextmonth+"-"+year ;
						if (month == 7 || month == 12){
						jtp = 31+"-"+nextmonth+"-"+year ;}
					}
					if (month==2 || month==4 || month==6 || month==9 || month==11 ){
					var msd = 30+"-"+month+"-"+year ;
						if (month == 2){
						msd = feb+"-"+month+"-"+year ; }
					jtp = 31+"-"+nextmonth+"-"+year ;
					}
					document.getElementById('jatuhtempotgl').value = jtp ;
					document.getElementById('masasd').value = msd ;
					break;
				}
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

	oTable = $('#table_dlg1').dataTable({
		"iDisplayLength": 9,
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
			mID = ''; cuID = '';  pID = ''; nopd = '';
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
		"sAjaxSource": "<?=active_module_url('subjek_pajak/grid_for_spt/'.pad_air_tanah_id());?>"
	});
	var tb_array = [
		'<br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);


	/*-------*/
	var cust_id = $("#customer_id").val();
	if (cust_id ==""){
		cust_id = 0;
	}
	oTable3 = $('#table_history').dataTable({
		"iDisplayLength": 9,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [			
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [4], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "right" },
			{ "aTargets": [5], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "right" },
			{ "aTargets": [6], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "right" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = ''; cuID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable3.fnGetData( this );
					oTable3.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
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
		"sAjaxSource": "<?=active_module_url('sptpd/grid_air_tanah_history');?>"+cust_id
	});

	/*-------*/

	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url($this->uri->segment(2));?>'+'index/'+'<?=$this->uri->segment(4);?>'
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


	$('#btn_ok_omset').click(function() {
	  get_pajak_detail();
	  hitung_pajak();
	});

	$('#btn_dialog_ok_ku').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			document.getElementById('kelompok_usaha_id').value = mID;
			document.getElementById('kelompok_usaha').value = kelompok_usaha;
			$('#kuDialog').modal('hide');
		}
	});

	$('#btn_omset').click(function() {
		$('#btn_save').hide();
		$('#hitung_npa').show();
	});
	$('#hitung_npa').click(function() {
		$('#btn_save').show();
		$('#hitung_npa').hide();
	});


	//Grid Kelompok Usaha
	/*
	oTable2 = $('#table_dlg2').dataTable({
		"iDisplayLength": 9,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar2">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [			
		{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },

		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = ''; kelompok_usaha = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable2.fnGetData( this );
					mID = data[0];
					kelompok_usaha = data[2];
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			mID = ''; kelompok_usaha = '';
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
		"sAjaxSource": "<?=active_module_url('air_kelompok_usaha/grid_for_hda');?>"
	});

	var tb2_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btn_filter_induk" class="btn btn-success pull-left" type="button">Filter Sub Kelompok Usaha</button>',
		'	<button id="btn_filter_induk2" class="btn btn-success pull-left" type="button">Filter Detail Kelompok Usaha</button>',
		'	<button id="btn_filter_back" class="btn pull-left" type="button">Kembali</button>',

		'</div></br>'
	];
	var tb2 = tb2_array.join(' ');	
	$("div.toolbar2").html(tb2);
	*/


	$('#btn_filter_induk').click(function() {
		if(mID) {
				level_id = 1;
				switch_btn(level_id);
		        reload_grid();
			}
		 else{
			alert('Silahkan pilih data yang akan difilter');
		}
	});

	$('#btn_filter_induk2').click(function() {
		if(mID) {
				level_id = 2;
				switch_btn(level_id);
		        reload_grid();
			}
		else{
			alert('Silahkan pilih data yang akan difilter');
		}
	});

	$('#btn_filter_back').click(function() {;
        reload_grid_start();
        $('#btn_filter_induk2').hide();
        $('#btn_filter_induk').show();
	});

	//----------------------

	$('#dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak, #pajak_calculated, #meteran_awal, #meteran_akhir').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '9999999999999.99',  mDec: '0'
	});

	$('#volume').autoNumeric('init', {
	aSep: '.', aDec: ',', vMax: '999999999999.999'
	});


	$('#total_volume').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '9999999999999.999'
	});

	$('#tarif').autoNumeric('init', {aSep: '.', aDec: ',', vMax: '9999999999999.99'});
	
	$('#volume, #dasar, #denda, #bunga, #setoran, #kenaikan, #kompensasi, #lain2, #pajak, #tarif').keyup(function() {
		hitung_pajak();
	});

	for(var i=1; i<=31; i++){ 
	$('#omset'+i).keyup(function() {
		hitung_volume();
	});
	$('#omset'+i).autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '9999999999999.99',  mDec: '0'
		});

	}
	
	$('#customer_usaha_id').change(function() {
		get_pajak(this.value);
		get_pajak_detail();
		get_sumur();
		set_masa_sd();
		//set_jatuh_tempo();
		hitung_pajak();

		//FORCE SET JATUH TEMPO
		var lang = 'en' ;
		str = "01"+"-"+document.getElementById('masapajak_bulan').value ;
		masadari = GetDate(str,lang) ;
		document.getElementById('masadari').value = masadari;
		global_bulantahun = masadari.substr(3, 9);
		set_jatuh_tempo(global_bulantahun);

		var masapajak_bulan = document.getElementById('masapajak_bulan').value;
		document.getElementById('omset_masa_pajak').innerHTML = masapajak_bulan ;
	});
		
	$('#pajak_id').change(function() {
		get_pajak_detail();
		//get_sumur();
		set_masa_sd();
		set_jatuh_tempo();
		hitung_pajak();
	});
			
	$('#nopd').typeahead({
		source: function(query, process){
			$.getJSON('<?=active_module_url('objek_pajak/get_typeahead_nopd/'.$this->uri->segment(4));?>'+query, function(response) {
				var data = new Array;
				for(var i in response) {
					data.push(response[i]['id'] +"#"+ response[i]['nopd'] +"#"+ response[i]['customer_id'] +"#"+ response[i]['usaha_id'] +"#"+ response[i]['customernm'] +"#"+ response[i]['usahanm'] +"#"+ response[i]['def_pajak_id']);
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
	
    // Don't allow direct editing
    $('#terimatgl, #masadari, #masasd, #jatuhtempotgl').on('keypress', function(e) {
        e.preventDefault();
    });
	        //masapajakbulan
    $("#masapajak_bulan").monthpicker();
	$('#masapajak_bulan').change(function() {
		var lang = 'id' ;
		str = "01"+"-"+document.getElementById('masapajak_bulan').value ;
		masadari = GetDate(str,lang) ;

		var sekarang = new Date();
		month_now = parseInt((sekarang.getMonth())+1);
		year_now  = parseInt(sekarang.getFullYear());
		month_picked = parseInt(masadari.substr(3, 4));
		year_picked = parseInt(masadari.substr(6, 9));
		//
			if (month_picked>=month_now){
				 if(year_picked>=year_now){
				alert("Invalid Input");
				document.getElementById('masapajak_bulan').value = '';
				document.getElementById('masadari').value = "1"+"-"+parseInt(month_now-1)+"-"+year_now ;
			}}
			else if (month_picked>=month_now){
				 if(year_picked<year_now){
					document.getElementById('masadari').value = masadari;
					global_bulantahun = masadari.substr(3, 9);
					//get_pajak_detail2(masadari);
					set_jatuh_tempo(global_bulantahun);
					//langsung hitung denda
					//hitung_denda(bulantahun); //lihat sbg presentase
					//view_denda	= 2*selisih_masa;
					//document.getElementById('view_denda').value = view_denda;
					var masapajak_bulan = document.getElementById('masapajak_bulan').value;
					document.getElementById('omset_masa_pajak').innerHTML = masapajak_bulan ;
					hitung_denda(global_bulantahun);
					}
				}
			 if(year_picked>year_now){
				alert("Invalid Input");
				document.getElementById('masapajak_bulan').value = '';
				document.getElementById('masadari').value = "1"+"-"+parseInt(month_now-1)+"-"+year_now ;
			}		
			else{
			document.getElementById('masadari').value = masadari;
			global_bulantahun = masadari.substr(3, 9);
			set_jatuh_tempo(global_bulantahun);
			var masapajak_bulan = document.getElementById('masapajak_bulan').value;
			document.getElementById('omset_masa_pajak').innerHTML = masapajak_bulan ;
			hitung_denda(global_bulantahun);
		}



	});

	function GetDate(str,lang) {
		var arr = str.split('-');
		var month = "";
		if (lang=='id')
		var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'];
		if (lang=='en')
		var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		var i = 0;
		for (i; i < months.length; i++) {
		    if (months[i] == arr[1]) {
		        break;
		    }
	}
	i++;
	if (i >= 10) month = i;
	else month = "0" + i;
	var formatddate = arr[0] + '-' + month + '-' + arr[2];
	return formatddate;
	}

	$('#masapajak_bulan').click(function() {
		var lang = 'id' ;
		str = "01"+"-"+document.getElementById('masapajak_bulan').value ;
		masadari = GetDate(str,lang) ;
		document.getElementById('masadari').value = masadari;
		global_bulantahun = masadari.substr(3, 9);
		//get_pajak_detail2(masadari);
		set_jatuh_tempo(global_bulantahun);
		//langsung hitung denda
		hitung_denda(global_bulantahun); //lihat sbg presentase
		//view_denda	= 2*selisih_masa;
		//document.getElementById('view_denda').value = view_denda;
	});


	$('#hitung_npa').click(function() {
		get_pajak_detail();
		hitung_npa();
		hitung_denda(global_bulantahun);
	});


	/* init page */
	var c_id  = parseInt(<?=$dt['customer_id']?>);
	var cu_id = parseInt(<?=$dt['customer_usaha_id']?>);
	global_cu_id = cu_id;
	var def_pajak_id = parseInt(<?=$this->uri->segment(7);?>);

	if (!isNaN(c_id)) {
		get_cu(c_id, cu_id, def_pajak_id);
		get_sumur();
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
					<h4 class="modal-title">Wajib Pajak - Usaha : AIR TANAH</h4>
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
								<th>Pemilik/Pengelola</th>
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

	<div id="kuDialog" class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Wajib Pajak - Usaha : AIR TANAH</h4>
				</div>
				<div class="modal-body">
				<table class="table" id="table_dlg2">
					<thead>
						<tr>
							<th>index</th>
							<th>Kel. Usaha 1</th>
							<th>Kel. Usaha 2</th>
							<th>Kode</th>
							<th>Kel. Usaha 3</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				</div>
				<p>&nbsp;</p>	
				<p>&nbsp;</p>				
				<div class="modal-footer">
				<button class="btn btn-primary" id="btn_dialog_ok_ku">OK!</button>
				<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Batal</button>
				</div>
			</div>
		</div>
	</div>


	<div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>PENDATAAN AIR TANAH </strong></a>
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
			<div class="box box-primary box-solid">
			<div class="box-header with-border">
			<h3 class="box-title">Rincian Pendataan : <text id="display_nama"></text> </h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
			<?=msg_block();?>
			<input class="form-control" type="hidden" name="id" value="<?=$dt['id']?>" placeholder="spt_id"/>
			<input class="form-control" type="hidden" name="so" id="so" value="<?=$dt['so']?>" placeholder="so"/>
			<input class="form-control" type="hidden" name="customer_id" id="customer_id" value="<?=$dt['customer_id']?>" placeholder="c_id"/>
			<!--input class="form-control" type="hidden" name="customer_usaha_id" id="customer_usaha_id" value="<?=$dt['customer_usaha_id']?>" placeholder="cu_id"/-->
			<input class="form-control" type="hidden" name="r_bayarid" id="r_bayarid" value="<?=$dt['r_bayarid']?>" placeholder="r_bayarid" />
			<input class="form-control" type="hidden" name="minimal_omset" id="minimal_omset" value="<?=$dt['minimal_omset']?>" placeholder="minimal_omset" />
			<input class="form-control" type="hidden" name="r_nsr" id="r_nsr" value="<?=$dt['r_nsr']?>" placeholder="r_nsr" />
			<input class="form-control" type="hidden" name="rekening_id" id="rekening_id" value="<?=$dt['rekening_id']?>" placeholder="rekening_id" />
			<input class="form-control" type="hidden" name="kode" id="kode" value="<?=$dt['kode']?>" placeholder="kode" />
			<input class="form-control" type="hidden" name="jatuhtempo" id="jatuhtempo" value="<?=$dt['jatuhtempo']?>" placeholder="jatuhtempo" />
			<input class="form-control" type="hidden" name="multiple" id="multiple" value="<?=$dt['multiple']?>" readonly />
			
			<div class="form-group">
				<label class="control-label col-sm-2" for="sptno">No. </label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="tahun" id="tahun" value="<?=$dt['tahun']?>" readonly />
				</div>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="sptno" id="sptno" value="<?=$dt['sptno']?>" readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="terimatgl">Tanggal Terima</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" />
				</div>
			</div>
			<div class="form-group">
			<label class="control-label col-sm-2" for="jenis_dokumen">Jenis Dokumen</label>
			<div class="col-sm-4">
				<?=$select_sptpd_type;?>
			</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="cara_bayar">Cara Bayar</label>
				<div class="col-sm-2">
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
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="nopd">NOPD</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="nopd" id="nopd" value="<?=$dt['nopd']?>" placeholder="NOPD" autocomplete="off" />
				</div>
				<div class="col-sm-1">	
					<button type="button" class="btn btn-success" data-toggle="modal" data-target="#cuDialog">Cari</button>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="npwpd">Wajib Pajak</label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" autocomplete="off"  readonly />
					<input class="form-control" type="text" name="nama" id="nama" value="" placeholder="Nama Wajib Pajak" autocomplete="off"  readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="usaha_id">Jenis Usaha</label>
				<div class="col-sm-4">
					<?=$select_usaha;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="pajak_id">Jenis Pajak</label>
				<div class="col-sm-4">
					<?=$select_pajak;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="golongan">Golongan</label>
                <div class="col-sm-2">
                    <input class="form-control hide" type="text" name="golongan_id" id="golongan_id" placeholder="golongan ID" autocomplete="off" />
                    <input class="form-control" type="text" name="golongan" id="golongan" placeholder="Golongan" autocomplete="off" readonly />
                </div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="zona_id">Zona</label>
				<div class="col-sm-4">
					<input class="form-control hide" type="text" name="zona_id" id="zona_id" placeholder="Zona ID" autocomplete="off" />
					<input class="form-control" type="text" name="zona" id="zona" placeholder="Zona" autocomplete="off" readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="kelompok_usaha_id">Kelompok Usaha</label>
				<div class="col-sm-3">
					<input class="form-control hide" type="text" name="kelompok_usaha_id" id="kelompok_usaha_id" placeholder="Kelompok Usaha ID" autocomplete="off" />
					<input class="form-control" type="text" name="kelompok_usaha" id="kelompok_usaha" placeholder="Kelompok Usaha" autocomplete="off" readonly />
				</div>
				<div class="col-sm-1">	
					<button type="button" class="btn btn-success hidden" data-toggle="modal" data-target="#kuDialog">Cari</button>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="manfaat_id">Manfaat</label>
				<div class="col-sm-4">
					<input class="form-control hide" type="text" name="manfaat_id" id="manfaat_id" placeholder="Manfaat ID" autocomplete="off" />
					<input class="form-control" type="text" name="manfaat" id="manfaat" placeholder="Manfaat" autocomplete="off" readonly />
				</div>
			</div>
			<div class="form-group hidden">
				<label class="control-label col-sm-2" for="doc_no">Dokumen No.</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="doc_no" id="doc_no" value="<?=$dt['doc_no']?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2"  for="masapajak_bulan">Masa Pajak</label>
                <div class="col-sm-2">
                   <input class="form-control" type="text" name="masapajak_bulan" id="masapajak_bulan" value="<?=$dt['masapajak_bulan']?>" placeholder="mm-yyyy" required/>
                </div>
				<label class="control-label col-sm-2 hide" for="masadari">Masa Pajak</label>
				<div class="col-sm-2">
					<input class="form-control hide" type="text" name="masadari" id="masadari" value="<?=$dt['masadari']?>" placeholder="dd-mm-yyyy" required/> 
				</div>
				<label class="control-label col-sm-1 hide" for="masasd">s/d</label>
				<div class="col-sm-2">
					<input class="form-control hide" type="text" name="masasd" id="masasd" value="<?=$dt['masasd']?>" placeholder="dd-mm-yyyy"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="jatuhtempotgl">Jatuh Tempo</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="jatuhtempotgl" id="jatuhtempotgl" value="<?=$dt['jatuhtempotgl']?>" placeholder="dd-mm-yyyy" required/>
				</div>
			</div>

			</div>
			</div>

			<div class="tabbable col-sm-12">
			<ul id="myTab" class="nav nav-tabs">
				<li class="active"><a href="#perhitungan" id="hitung_npa2" data-toggle="tab"><strong>Perhitungan</strong></a></li>
				<li ><a href="#history" data-toggle="tab"><strong>History</strong></a></li>
				<li class="hide"><a href="#sumur" data-toggle="tab"><strong>Omset / Volume</strong></a></li>

			</ul>
			<div class="tab-content">
			<div class="tab-pane fade in active" id="perhitungan">	
			<br>
				<div class="box box-primary box-solid">
				<div class="box-body">
				<div class="row">
			 	<div class="row col-sm-5">	
			 		<div class="form-group">
						<label class="control-label col-sm-5" for="volume">Volume (M<sup>3</sup>)</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="volume" id="volume" value="<?=$dt['volume']?>" required readonly/>
						</div>
						<div class="col-sm-1">
							<button type="button" class="btn btn-success" href="#sumur" data-toggle="tab" id="btn_omset">Omset</button>
						</div>
					</div>	
					<div class="form-group">
						<label class="control-label col-sm-5" for="dasar">NPA</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="dasar" id="dasar" value="<?=$dt['dasar']?>" required readonly/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-5" for="tarif">Tarif</label>
						<div class="col-sm-6">
							<input class="form-control" type="hidden" style="text-align:right;" name="tarif" id="tarif" value="<?=$dt['tarif']?>" required readonly/>
							<input class="form-control" type="text" style="text-align:right;" name="view_tarif" id="view_tarif" value="<?=$dt['tarif']*100?>%" required readonly/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-5" for="denda">Denda</label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="denda" id="denda" value="<?=$dt['denda']?>" readonly/>
							<label id="pesandenda"></label>	
						</div>
					</div>
					<div class="form-group">
					<label class="control-label col-sm-5" for="pajak_calculated"><strong>Calculated</strong></label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="pajak_calculated" id="pajak_calculated" readonly />
						</div>
					</div>
					<div class="form-group success">
						<label class="control-label col-sm-5" for="pajak"><strong>Pajak Terhutang</strong></label>
						<div class="col-sm-6">
							<input class="form-control" type="text" style="text-align:right;" name="pajak" id="pajak" readonly />
						</div>
					</div>
				  </div>
				  <div class="row col-sm-2">
				  &nbsp;
				  </div>
				  <div class="row col-sm-5">	
				  		<div id="npa_data">
						</div>
				  </div>		
				  </div>
				  </div>
				  </div>
				</div>
				<div class="tab-pane fade in hide" id="omset">
				<br>
					<div class="box box-primary box-solid">
					<div class="box-body">
					<div class="row">
					<div class="col-sm-6">
						<div style="text-align:center;"  class="col-sm-2" >
							<strong> Tanggal </strong>
						</div>
						<div style="text-align:center;" class="col-sm-4" >
							<strong>Volume</strong>
						</div>
						<div style="text-align:center;"  class="col-sm-6" >
							<strong>Keterangan</strong>
						</div>
						<!-- Omset 31 Hari -->
						<? for ($i=1; $i<=16; $i++) { ?>
						<div style="text-align:center;"class="col-sm-2" >
							<?echo $i; ?>
						</div>

						<div class="row col-sm-4" >
							<div class="form-group">
								<div class="col-sm-12">
								<input class="form-control" type="text" style="text-align:right;" name="omset<?echo $i?>" id="omset<?echo$i?>" value="<?=$dt['omset'.$i]?>"  />
								</div>
							</div>
						</div>		
						<div class="row col-sm-6" >
							<div class="form-group">
								<div class="col-sm-12">
									<input class="form-control" type="text" style="text-align:right;" name="keterangan<?echo $i?>" id="keterangan<?echo $i?>" value="<?=$dt['keterangan'.$i]?>" />
								</div>
							</div>
						</div>	
						<? } ?>	
					</div>

					<div class="col-sm-6">
						<div style="text-align:center;"  class="col-sm-2" >
							<strong> Tanggal </strong>
						</div>
						<div style="text-align:center;" class="col-sm-4" >
							<strong>Volume</strong>
						</div>
						<div style="text-align:center;"  class="col-sm-6" >
							<strong>Keterangan</strong>
						</div>
						<!-- Omset 31 Hari -->
						<? for ($i=17; $i<=31; $i++) { ?>
						<div style="text-align:center;"class="col-sm-2" >
							<?echo $i; ?>
						</div>

						<div class="row col-sm-4" >
							<div class="form-group">
								<div class="col-sm-12">
								<input class="form-control" type="text" style="text-align:right;" name="omset<?echo $i?>" id="omset<?echo$i?>" value="<?=$dt['omset'.$i]?>"  />
								</div>
							</div>
						</div>		
						<div class="row col-sm-6" >
							<div class="form-group">
								<div class="col-sm-12">
									<input class="form-control" type="text" style="text-align:right;" name="keterangan<?echo $i?>" id="keterangan<?echo $i?>" value="<?=$dt['keterangan'.$i]?>" />
								</div>
							</div>
						</div>	
						<? } ?>	
					</div>
				</div>
				</div>
				</div>
				</div>


				<div class="tab-pane fade in " id="sumur">
				<br>
					<div class="box box-primary box-solid">
					<div class="box-body">
					<div class="row">
					<div class="col-sm-12">	
					<label>Omset Bulan : &nbsp;</label><label id="omset_masa_pajak"></label>
					</div>
					<div class="col-sm-12">
						<div style="text-align:center;"  class="col-sm-1" >
							<strong> Sumur </strong>
						</div>
						<div style="text-align:center;" class="col-sm-2" >
							<strong>No. SIPA</strong>
						</div>
						<div style="text-align:center;"  class="col-sm-3" >
							<strong>Meteran Awal</strong>
						</div>
						<div style="text-align:center;"  class="col-sm-3" >
							<strong>Meteran Akhir</strong>
						</div>
						<div style="text-align:center;"  class="col-sm-3" >
							<strong>Volume</strong>
						</div>
						<div id="sumur_data">
						</div>
					</div>
				</div>
				</div>
				</div>
				</div>


				<div class="tab-pane fade in" id="history">
					<div class="box">
					<div class="box-body">
					<div class="row">
					<table class="table" id="table_history">
						<thead>
							<tr>
								<th>index</th>
								<th>Masa Pajak</th>
								<th>Sumur Ke</th>								
								<th>No. Sipa</th>
								<th>Awal</th>
								<th>Akhir</th>
								<th>Volume</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<br>
					<br>
					<br>
				</div>
				</div>
				</div>
				</div>


			<button type="submit" class="btn btn-primary" id="btn_save" >Simpan</button>
			<button type="button" class="btn btn-success" id="hitung_npa" href="#perhitungan" data-toggle="tab">Hitung NPA</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>

			</div>
			</div>
			</div>


		<?php echo form_close();?>
	</div>
</div>
<? $this->load->view('_foot'); ?>