<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.dataTables_filter {
    height: 40px;
}
.btn-group, .btn-group-vertical {
    bottom: -10px;
}
.dataTables_wrapper {
    top: 5px;
}
.table > tbody > tr > td{
	padding: 3px;
}

.ui-state-default,.ui-widget-content .ui-state-default,.ui-widget-header .ui-state-default {
	font-weight:bold;
}
</style>

<? if ($add_id == pad_reklame_id()) {
	$add_id_btn = "add_reklame" ;
	}
	elseif ($add_id == pad_air_tanah_id()) {
	$add_id_btn = "add_at" ;
	}
	elseif ($add_id == 'default') {
	$add_id_btn = "btn_hidden" ;
	?>

	<script>
	$(window).load(function(){ 
			reload_grid();
			$('#btn_hidden').hide();
			$('#btn_edit').hide();
			$('#btn_delete').hide();

	 });
	</script>

	<?
	}
	else{
	$add_id_btn = "add_self" ;
	}

?>

<? if ($this->uri->segment(4) == 'default') { ?>
<?}?>

<script>
var mID;
var uID;
var tID;
var cuID;
var oTable;

$(window).load(function(){ 

		var cu = parseInt(<?=$this->uri->segment(4);?>);
		var at = parseInt(<?=pad_air_tanah_id();?>);
		var rk = parseInt(<?=pad_reklame_id();?>);

		if (cu == at ||  cu == rk){
			$('#btn_dialog_validasi').hide();
			$('#validasi_id').hide();
		}
		
		var skpd_type = '<?=$this->uri->segment(3);?>' ;
		if(skpd_type=='kb' || skpd_type=='kbt'){
		$('#btn_dialog_validasi').hide();
		$('#validasi_id').hide();
		}
	
		reload_grid();
		$('#btn_recall').hide();
		$('#btn_dialog_unvalidasi').hide();
 });

$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
	if ( typeof sNewSource != 'undefined' && sNewSource != null ) {
		oSettings.sAjaxSource = sNewSource;
	}

	/* Server-side processing should just call fnDraw */
	if ( oSettings.oFeatures.bServerSide ) {
		this.fnDraw();
		return;
	}

	this.oApi._fnProcessingDisplay( oSettings, true );
	var that = this;
	var iStart = oSettings._iDisplayStart;
	var aData = [];

	this.oApi._fnServerParams( oSettings, aData );

	oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
		/* Clear the old information from the table */
		that.oApi._fnClearTable( oSettings );

		/* Got the data - add it to the table */
		var aData =  (oSettings.sAjaxDataProp !== "") ?
			that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;

		for ( var i=0 ; i<aData.length ; i++ )
		{
			that.oApi._fnAddData( oSettings, aData[i] );
		}

		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

		if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true )
		{
			oSettings._iDisplayStart = iStart;
			that.fnDraw( false );
		}
		else
		{
			that.fnDraw();
		}

		that.oApi._fnProcessingDisplay( oSettings, false );

		/* Callback user function - for event handlers etc */
		if ( typeof fnCallback == 'function' && fnCallback != null )
		{
			fnCallback( oSettings );
		}
	}, oSettings );
};

function show_rpt(rpt){

    if (rpt=='sptpd_sspd'){
    	var rptparams = {
        rpt: rpt,
        no: mID,
    	}
    }else{
    	var rptparams = {
        rpt: rpt,
        id: mID,
    	}
    }
	
	var data = decodeURIComponent($.param(rptparams));
	var url  = '<? echo active_module_url($controller); ?>cetak/pdf/?'+data;
	
	var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
	window.open(url, 'Laporan', winparams);
}

function switch_btn() {
	var validasi_id = $('#validasi_id').val();
	var bayar_id = $('#bayar_id').val();
	if(bayar_id==1){
			$('#btn_dialog_validasi').hide();
	        $('#btn_dialog_unvalidasi').hide();
	}else{
	    if (validasi_id == 2) {
	        $('#btn_dialog_validasi').hide();
	        $('#btn_dialog_unvalidasi').show();
	    } else {
			var cu = parseInt(<?=$this->uri->segment(4);?>);
			var at = parseInt(<?=pad_air_tanah_id();?>);
			var rk = parseInt(<?=pad_reklame_id();?>);

			if (cu == at ||  cu == rk){
				$('#btn_dialog_validasi').hide();
				$('#validasi_id').hide();
			}else{
			$('#btn_dialog_validasi').show();
	        $('#btn_dialog_unvalidasi').hide();
	    	}
	    }
	}

    var proses_id  = $('#proses_id').val();
    if (proses_id == 2) {
        $('#btn_proses').hide();
        $('#btn_recall').show();
    } else {
        $('#btn_proses').show();
        $('#btn_recall').hide();
    }

	var skpd_type = '<?=$this->uri->segment(3);?>' ;
	if(skpd_type=='kb' || skpd_type=='kbt'){
	$('#btn_dialog_validasi').hide();
	$('#validasi_id').hide();
	}
}

function reload_grid() {
	var usaha_id  = $('#usaha_id').val();
	var type_id   = $('#type_id').val();
	var proses_id = $('#proses_id').val();
    var terimatgl = $('#terimatgl').val();
    var terimatgl2 = $('#terimatgl2').val();
    var bayar_id = $('#bayar_id').val();
    var validasi_id = $('#validasi_id').val();

    if(usaha_id==null){
    	usaha_id = 999;
    }

    skpd_type = '<?=$this->uri->segment(3)?>';
    if(skpd_type=='kb' || skpd_type=='kbt'){
		usaha_id = <?=$this->uri->segment(4)?>;
    }
    
    <? if ($this->uri->segment(2) != 'sptpd') : ?>
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+proses_id+"/"+usaha_id+"/"+terimatgl+"/"+terimatgl2+"/"+bayar_id+"/"+validasi_id);
    <? else : ?>
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+type_id+"/"+usaha_id+"/"+terimatgl+"/"+terimatgl2+"/"+bayar_id+"/"+validasi_id);
    <? endif; ?>
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
   return "Rp. " + x1 + x2;
}


$(document).ready(function() {	
	oTable = $('#table1').dataTable({
		"iDisplayLength": 13,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">lfrtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "95px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "80px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "85px", "sClass": "" },
			{ "aTargets": [4], "bSearchable": true,  "bVisible": true,  "sWidth": "80px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "150px", "sClass": "" },
            
			{ "aTargets": [7], "bSearchable": true,  "bVisible": true,  "sWidth": "145px", "sClass": "" },
			{ "aTargets": [8], "bSearchable": true,  "bVisible": true,  "sWidth": "80px", "sClass": "center" },
			{ "aTargets": [9], "bSearchable": true,  "bVisible": true,  "sWidth": "65px", "sClass": "" },
			{ "aTargets": [10], "bSearchable": true, "bVisible": true, "sWidth": "85px", "sClass": "right" },
			{ "aTargets": [11], "bSearchable": true, "bVisible": true, "sWidth": "75px", "sClass": "right" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					uID = data[12];
					tID = data[13];
					pID = data[14];
					cuID = data[15];
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			mID = '';
		},
		"oLanguage": {
			"sProcessing":   "<i class='fa fa-refresh fa-spin' style='font-size: 70px;'></i>",
			"sLengthMenu":   "",
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
		"sAjaxSource": "<? echo active_module_url($controller); ?>grid/"
	}); 
    
    $('.dataTables_filter input').unbind();
    $('.dataTables_filter input').bind('keyup', function(e) {
        if(e.keyCode == 13) {
            oTable.fnFilter(this.value);   
        }
    });
    
	/* Get the rows which are currently selected */
	function fnGetSelected( oTableLocal )
	{
		return oTableLocal.$('tr.row_selected');
	}
	
	 /*
	$(window).resize(function() {
		oTable.fnAdjustColumnSizing();
	});
	*/

	var tb_array = [
		<? if ($this->uri->segment(2) != 'sptpd') : ?>
		'<div class="btn-group pull-left">',
		'	<button id="btn_proses" data-loading-text="Proses <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-success">Proses</button>',
		'	<button id="btn_recall" data-loading-text="Recall <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-warning">Recall</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input style="width:100px;" class="form-control" type="text" name="prosestgl" id="prosestgl" value="<?=date('d-m-Y');?>"/></div>',
		'</div>',
		'<div class="btn-group pull-left">',
		<? if($this->uri->segment(4)==4) {?>
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="reklame_skpd"><i class="fa fa-print"></i> Cetak</button>',
		<? } elseif($this->uri->segment(4)==7) {?>
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="airtanah_skpd"><i class="fa fa-print"></i> Cetak</button>',
		<? }?>
		'	<button id="btnshow_rpt" class="btn btn-primary hide" data-rpt="nota">Cetak Nota</button>',
		'	<button id="btnshow_rpt" class="btn btn-primary hide" data-rpt="reklame_ipr">Cetak IPR</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_proses_id;?>',
		'</div>',
        <? else : ?>
		'<div class="btn-group pull-left">',
		'	<button id="<?echo $add_id_btn?>" class="btn pull-left">Tambah</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<button id="btn_edit" class="btn">Edit</button>',
		'	<button id="btn_delete" class="btn">Hapus</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<button id="btn_rpt_sspd" data-rpt="sptpd_sspd" class="btn btn-info"><i class="fa fa-print"></i> Cetak Nomor Bayar</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<?if ($add_id_btn == "add_self"){?><button id="btnshow_rpt" class="btn btn-primary" data-rpt="sptpd"> <i class="fa fa-print"></i> Cetak</button> <?}?>',
		'</div>',
		'<div class="btn-group pull-left hide">',
		'<?=$select_sptpd_type;?>',
		'</div>',
		<? endif; ?>
		'<div class="btn-group pull-left hide">',
		'<?=$select_usaha;?>',
		'</div>',
		<? if ($this->uri->segment(3) == 'kb' || $this->uri->segment(3) == 'kbt'): ?>
		'<div class="btn-group pull-left">',
		'<?=$select_usaha_sel;?>',
		'</div>',
		<? endif; ?>
		'<div class="col-sm-12"><p></p>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<button id="btn_dialog_validasi" class="btn btn-success">Validasi</button>',
		'<button id="btn_dialog_unvalidasi" class="btn btn-warning">Batalkan Validasi</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<input style="width:100px;" class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=date('01-01-Y');?>"/>',
		'</div>',
		'<div class="btn-group pull-left"><h4> s/d </h4>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input style="width:100px;" class="form-control" type="text" name="terimatgl2" id="terimatgl2" value="<?=date('d-m-Y');?>"/></div>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_bayar;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_validasi;?>',
		'</div><br><br><br>',

	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#proses_id, #type_id, #usaha_id, #bayar_id, #validasi_id').change(function() {
		switch_btn();
		reload_grid();
	});

	$('#usaha_id_sel').change(function() {
		var u_id = this.value ;
		var skpd_type = '<?=$this->uri->segment(3);?>' ;
		window.location = '<? echo active_module_url($controller); ?>'+skpd_type+'/'+u_id ;
	});

	$('#btn_tambah1').click(function() {
		window.location = '<? echo active_module_url($controller); ?>add/';
	});

	$('#add_self').click(function() {
		window.location = '<? echo active_module_url($controller); ?>add/<?=$add_id;?>';
	});

	$('#add_reklame').click(function() {
		window.location = '<? echo active_module_url($controller); ?>add/<?=pad_reklame_id();?>';
	});

	$('#add_at').click(function() {
		window.location = '<? echo active_module_url($controller); ?>add/<?=pad_air_tanah_id();?>';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<? echo active_module_url($controller); ?>edit/'+uID+'/'+tID+'/'+mID+'/'+pID+'/'+cuID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<? echo active_module_url($controller); ?>delete/'+mID+'/<?=$add_id;?>';
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
	

	$("#btn_rpt_sspd").click(function(){
		if(mID) {
            show_rpt($(this).data('rpt'));
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});
	
	$("#btn_rpt_sptpd").click(function(){
		if(mID) {
            show_rpt($(this).data('rpt'));
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});
	
	<? if ($this->uri->segment(2) != 'sptpd') : ?>
	$('#btn_proses').click(function() {
		if(mID) {
			    $.ajax({
                url: '<?php echo active_module_url()?>sptpd/get_validasi/'+uID+'/'+tID+'/'+mID,
                async: false,
                success: function (j) {
                	var data = $.parseJSON(j);
                	$('#nama_pajak').html(data['nama_pajak']);
                	$('#cara_bayar').html(data['cara_bayar']);
                	$('#masa_pajak').html(data['masapajak_bulan']);
                	$('#spt_id').val(data['id']);
                	$('#type_ids').val(data['type_id']);


					if (data['usaha_id']==7){
						var ld = Date.today().clearTime().moveToLastDayOfMonth();
						var lastday = ld.toString("dd-MM-yyyy");
	                	$('#jatuhtempotgl').val(lastday); /*skpd jatuh tempo di akhir bulan penetapan*/
						$('#jatuhtempotgl_view').html(lastday);
						if(data['bulan_telat']!=null){
                	   		 $('#bulan_telat_view').html(data['bulan_telat']+" Bulan");
                		}	
                		else{
							$('#bulan_telat_view').html(" -  Bulan");
                		}
                		$('#content_detail_npa').show();
                	}else if (data['usaha_id']==4){
						$('#masa_pajak').html(data['masapajak_bulan'] + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>[ Tanggal Ijin :  " + data['terimatgl'].split(' ')[0] +", Jatuh Tempo SPT : "+data['jatuhtempotgl'] + " ] </i>");
                		var ijintgl = data['terimatgl'];
            			var mm = ijintgl.split('-')[1];
            			var yyyy = ijintgl.split('-')[0];
            			var dd = data['jatuhtempotgl'].split('-')[0];
						
						var ijindate = new Date(ijintgl.split(' ')[0]);
            			var jtpdate  = new Date(yyyy+'-'+mm+'-'+dd);

            			var jtptgl = dd+'-'+mm+'-'+yyyy;

            			if(ijindate>jtpdate){
            				var m2 = parseInt(mm)+1;
            				if (m2<10){
            					m2 = '0'+m2;
            				}
            				if (m2==13){
            					m2=1; 
            					yyyy = yyyy+1;
            				}
            				var jtptgl = dd+'-'+m2+'-'+yyyy;
            			}
	                	$('#jatuhtempotgl').val(jtptgl); /*skpd jatuh tempo reklame sesuai konfigurasi kesepakatan tgl 14 jan*/
						$('#jatuhtempotgl_view').html(jtptgl);
						if(data['bulan_telat']!=null){
                	   		 $('#bulan_telat_view').html(data['bulan_telat']+" Bulan");
                		}	
                		else{
							$('#bulan_telat_view').html("- Bulan");
                		}
                		$('#content_detail_npa').hide();
                	}

					if (data['usaha_id']==7 || data['usaha_id']==4){
					  //var newdenda = data['dasar']*(data['persen_bunga']/100)*data['bulan_telat'];
					  var newdenda = data['new_denda'];
                	  $('#denda').val(newdenda);
                	  $('#denda_view').html(convertToRupiah(newdenda));
                	}

                	$('#pajak').val(data['pajak']);
                	
                	var pajakval = convertToRupiah(data['pajak']);
                	$('#pajak_view').html(pajakval);

                	//untuk reklame beda tanggal kohir sesuai konfigurasi kesepakatan tgl 14 jan
                	if (data['usaha_id']==4){ 
                		//var dd = data['terimatgl'].split('-')[2];
                		//dd = dd.split(' ')[0];
                		//var prosestgl = dd+'-'+mm+'-'+yyyy;
                		$('#prosestgl_skpd').val(data['terimatgl']);
                	}else{
						$('#prosestgl_skpd').val( $('#prosestgl').val());
                	}
                	$('#dlgKategoriLabel').html('PROSES SKPD');
                	$('#petugas_id').show();
                	$('#pejabat_id').show();
                	$('#petugas_id_lbl').show();
                	$('#penetap_id_lbl').show();
                	$('#petugas_id_lbl2').show();
                	$('#penetap_id_lbl2').show();
                	$('#div_telat').show();
                	$('.content').css('min-height','300px');

                },
                error: function (xhr, desc, er) {
                    alert(er);
                }
            });


			if (uID == 7){
				$.ajax({
					url: "<?php echo active_module_url()?>sptpd/get_detail_npa/"+mID,
					async: false,
					success: function (data) {
						$("#view_detail_npa").html(data);
					},
					error: function (xhr, desc, er) {
						alert(er);
					}
				});
			}


			$('#pesan').html('Anda Yakin ingin memproses data ini ? ');
			$('#mode').val('proses_skpd');

			$('#btn_ok').show();
			$('#btn_batal').hide();
            $('#dlgValidasi').modal('show');
		}else{
			alert('Silahkan pilih data yang akan diproses');
		}
	});
/*
	$('#btn_ok').click(function() {
		var prosestgl  = $('#prosestgl').val();
			$.ajax({
                 url: '<? echo active_module_url($controller); ?>proses_skpd/'+uID+'/'+mID+'/'+prosestgl,
                 async: false,
                 success: function (data) {
                 if (data != 'ok') 
                 alert('Proses penetapan gagal');
                  },
                  error: function (xhr, desc, er) {
                      alert(er);
                }
            });
		});
*/
	
	$('#btn_recall').click(function() {
		if(mID) {
			    $.ajax({
                url: '<?php echo active_module_url()?>sptpd/get_validasi/'+uID+'/'+tID+'/'+mID,
                async: false,
                success: function (j) {
                	var data = $.parseJSON(j);
                	$('#nama_pajak').html(data['nama_pajak']);
                	$('#cara_bayar').html(data['cara_bayar']);
                	$('#masa_pajak').html(data['masapajak_bulan']);
                	$('#spt_id').val(data['id']);
                	$('#jatuhtempotgl').val(data['jatuhtempotgl']);
                	$('#pajak').val(data['pajak']);
                	$('#jatuhtempotgl_view').html(data['jatuhtempotgl']);
                	var pajakval = convertToRupiah(data['pajak']);
                	$('#pajak_view').html(pajakval);
                	$('#cara_bayar').hide();

                	if($('#cara_bayar').val()==2){
                		$('#cara_bayar_view').html('ATM / TELLER');
                	}else if($('#cara_bayar').val()==1){
                		$('#cara_bayar_view').html('TRANSFER');
                	}
                	$('#dlgKategoriLabel').html('RECALL SKPD');
                	$('#petugas_id').hide();
                	$('#pejabat_id').hide();
                	$('#petugas_id_lbl').hide();
                	$('#penetap_id_lbl').hide();
                	$('#petugas_id_lbl2').hide();
                	$('#penetap_id_lbl2').hide();
                	$('#div_telat').hide();
                	$('#content_detail_npa').hide();
                	$('.content').css('min-height','200px');
                },
                error: function (xhr, desc, er) {
                    alert(er);
                }
            });
			$('#pesan').html('Anda Yakin ingin melakukan Recall ? ');
			$('#mode').val('recall');
			$('#btn_ok').hide();
			$('#btn_batal').show();
            $('#dlgValidasi').modal('show');
		}else{
			alert('Silahkan pilih data yang akan dibatalkan');
		}
    });
	<? endif; ?>

	/*validasi non skpd*/
	$('#btn_dialog_validasi').click(function() {
		if(mID) {
			    $.ajax({
                url: '<?php echo active_module_url()?>sptpd/get_validasi/'+uID+'/'+tID+'/'+mID,
                async: false,
                success: function (j) {
                	var data = $.parseJSON(j);
                	$('#nama_pajak').html(data['nama_pajak']);
                	$('#cara_bayar').html(data['cara_bayar']);
                	$('#masa_pajak').html(data['masapajak_bulan']);
                	$('#spt_id').val(data['id']);
                	$('#jatuhtempotgl').val(data['jatuhtempotgl']);
                	$('#pajak').val(data['pajak']);
                	$('#jatuhtempotgl_view').html(data['jatuhtempotgl']);
                	var pajakval = convertToRupiah(data['pajak']);
                	$('#pajak_view').html(pajakval);;
                	$('#dlgKategoriLabel').html('VALIDASI');
                	$('#div_telat').hide();
                	$('#content_detail_npa').hide();

                },
                error: function (xhr, desc, er) {
                    alert(er);
                }
            });

			$('#pesan').html('Anda Yakin ingin memvalidasi data ini ? ');
			$('#mode').val('validate');
			$('#btn_ok').show();
			$('#btn_batal').hide();

            $('#dlgValidasi').modal('show');
		}else{
			alert('Silahkan pilih data yang akan divalidasi');
		}
    });

    $('#btn_dialog_unvalidasi').click(function() {
		if(mID) {
			    $.ajax({
                url: '<?php echo active_module_url()?>sptpd/get_validasi/'+uID+'/'+tID+'/'+mID,
                async: false,
                success: function (j) {
                	var data = $.parseJSON(j);
                	$('#nama_pajak').html(data['nama_pajak']);
                	$('#cara_bayar').html(data['cara_bayar']);
                	$('#masa_pajak').html(data['masapajak_bulan']);
                	$('#spt_id').val(data['id']);
                	$('#jatuhtempotgl').val(data['jatuhtempotgl']);
                	$('#pajak').val(data['pajak']);
                	$('#jatuhtempotgl_view').html(data['jatuhtempotgl']);
                	var pajakval = convertToRupiah(data['pajak']);
                	$('#pajak_view').html(pajakval);
                	$('#cara_bayar').hide();

                	if($('#cara_bayar').val()==2){
                		$('#cara_bayar_view').html('ATM / TELLER');
                	}else if($('#cara_bayar').val()==1){
                		$('#cara_bayar_view').html('TRANSFER');
                	}
                	$('#dlgKategoriLabel').html('CANCEL VALIDASI');
                	$('#div_telat').hide();


                },
                error: function (xhr, desc, er) {
                    alert(er);
                }
            });
			$('#pesan').html('Anda Yakin ingin membatalkan validasi ? ');
			$('#mode').val('cancel');
			$('#btn_ok').hide();
			$('#btn_batal').show();
            $('#dlgValidasi').modal('show');
		}else{
			alert('Silahkan pilih data yang akan dibatalkan');
		}
    });
    
	$("[id=btnshow_rpt]").click(function(){
		if(mID) {
			var anSelected = fnGetSelected( oTable );
			if ( anSelected.length !== 0 ) {
				/* oTable.fnDeleteRow( anSelected[0] ); */	
				var data = oTable.fnGetData( anSelected[0] );
				if (data[1] != null) {
                    var rpt = $(this).data('rpt');
                    show_rpt(rpt);
                } else alert('Data ini belum ditetapkan');
            }
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});
    	
	var terimatgl_dtp = $('#terimatgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		reload_grid();
		terimatgl_dtp.hide();
	}).data('datepicker');

	var terimatgl2_dtp = $('#terimatgl2').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		reload_grid();
		terimatgl2_dtp.hide();
	}).data('datepicker');
    
	var prosestgl_dtp = $('#prosestgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		prosestgl_dtp.hide();
	}).data('datepicker');
});
</script>
<!-- Modal -->
<div class="modal fade" id="dlgValidasi" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="dlgKategoriLabel"></h4>
            </div>
             <form id="form_validasi" action="<?php echo active_module_url()?>sptpd/proces_validasi/" method="post">
            <div class="content">
            <div class="content text-muted well well-sm no-shadow">
			
			<? if ($this->uri->segment(2) == 'skpd') {?>
            <div style="height:300px; width:100%; overflow:auto;">
            <?}?>
            	<input type="hidden" id="spt_id" name="spt_id" />
            	<input type="hidden" id="jatuhtempotgl" name="jatuhtempotgl" />
            	<input type="hidden" id="pajak" name="pajak" />
            	<input type="hidden" id="mode" name="mode" />
            	<input type="hidden" id="type_ids" name="type_ids" />
            	<input type="hidden" id="prosestgl_skpd" name="prosestgl_skpd" />
            	<input type="hidden" id="denda" name="denda" />


                <label id="pesan" class="control-label col-sm-12">
	            </label>

	            <label class="control-label col-sm-3">
	            Jenis Pajak
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <label class="control-label col-sm-8" id="nama_pajak">
	            </label>

	            <label class="control-label col-sm-3">
	            Masa Pajak
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <label class="control-label col-sm-8" id="masa_pajak">
	            </label>

	            <label class="control-label col-sm-3">
	            Jatuh Tempo 
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <label class="control-label col-sm-8" id="jatuhtempotgl_view">
	            </label>

	            <div id="content_detail_npa">
	            <label class="control-label col-sm-3">
	            Perhitungan NPA
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <br><br>
	            <div class="col-sm-8" id="view_detail_npa"></div>
	            <br><br>
	            <br><br>
	            <br><br>
	            </div>

	            <div id="div_telat">
	            <label class="control-label col-sm-3">
	            Terlambat 
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <label class="control-label col-sm-8" id="bulan_telat_view">
	            </label>

	            <label class="control-label col-sm-3">
	            Denda 
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <label class="control-label col-sm-8" id="denda_view">
	            </label>
	            </div>

	            <label class="control-label col-sm-3">
	            Pajak Terhutang
	            </label>
	            <label class="control-label col-sm-1">
	            :
	            </label>
	            <label class="control-label col-sm-8" id="pajak_view">
	            </label>

				<label class="control-label col-sm-3" for="cara_bayar">Cara Bayar </label>
				<label class="control-label col-sm-1">
	            :
	            </label>
				<div class="col-sm-2">
					<select class="form-control" style="width:180px" name="cara_bayar" id="cara_bayar">
					</select>
					<label name="cara_bayar_view" id="cara_bayar_view"></label>
				</div>
				<?if ($this->uri->segment(4)==pad_reklame_id() || $this->uri->segment(4)==pad_air_tanah_id() 
						|| $this->uri->segment(3)=='kb' || $this->uri->segment(3)=='kbt') {?>
				<style>
				.content {
				    min-height: 300px;
				}
				</style>
				<div class="col-sm-12">
                </div>
               <label class="control-label col-sm-3" for="petugas_id" id="petugas_id_lbl">Petugas Penetap</label>
               <label class="control-label col-sm-1" id="petugas_id_lbl2">
	            :
	            </label>
                <div class="col-sm-2">
                    <?=$select_petugas;?>
                </div>
                <div class="col-sm-12">
                </div>
                <label class="control-label col-sm-3" for="penetap_id" id="penetap_id_lbl">Pejabat Penandatangan</label>
                <label class="control-label col-sm-1" id="penetap_id_lbl2">
	            :
	            </label>
                <div class="col-sm-2">
                    <?=$select_pejabat;?>
                </div>
                <?}?>
            </div>
            <? if ($this->uri->segment(2) == 'skpd') {?>
            </div>
            <?}?>

            </div>
            	<div class="modal-footer">
            		<button type="submit" class="btn btn-primary" id="btn_ok">OK!</button>
            		<button type="submit" class="btn btn-danger" id="btn_batal">Batalkan!</button>
	                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
	        </div>
	       </form>
        </div>
    </div>
</div>
<!-- End Modal -->
<div class="content">  
	<div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>
				<? if($this->uri->segment(2) == 'sptpd') echo 'PENDATAAN : ' ;
				   else if ($this->uri->segment(2) == 'skpd' && $this->uri->segment(3) == 'index') echo 'PENETAPAN - SK : ';
				   else if ($this->uri->segment(2) == 'skpd' && $this->uri->segment(3) == 'kb') echo 'PENETAPAN SKPD-KB : '; 
				   else if ($this->uri->segment(2) == 'skpd' && $this->uri->segment(3) == 'kbt') echo 'PENETAPAN SKPD-KBT : '; 
				
					if ($this->uri->segment(4) != 999 && $this->uri->segment(3) != 'kb' && $this->uri->segment(3) != 'kbt')
					{ 
					echo implode(" ",$this->session->userdata('usaha_nama'));
					}else{
						echo "";
					}
				?> 

					</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>
		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th><?  if ($this->uri->segment(4) == 4 )
								echo  'No. Ijin';
							else if ($this->uri->segment(2) == 'sptpd' )
								echo  'SPTPD';
							else 
								echo  'SKPD'; ?></th>
					<th>Tanggal</th>
					<th><? echo $this->uri->segment(2) == 'sptpd' ? 'Dok.' : 'No. Bayar'; ?></th>
					<th>NOPD</th>
					<th>Wajib Pajak</th>
					<th>Jenis Pajak</th>
					<th>Nama OP</th>
					<th>Tarif (%)</th>
					<th>Masa</th>
					<th>Dasar</th>
					<th>Pajak</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table> 
	</div>
</div>

		<?if ($this->session->userdata('rpt_skpd')==1) { ?>
		<script>
		var r = confirm("Data Berhasil Ditetapkan. Cetak SKPD?");
		if (r == true) {
			var mID = <?=$this->session->userdata('id_skpd')?>;
			var s = <?=$this->uri->segment(4);?>;
			if (s == 7){
	        	var rpt = "airtanah_skpd";
	    	}else{
	    		var rpt = "reklame_skpd";
	    	}
	        show_rpt(rpt);
	    	}
	    <? $this->session->set_userdata('rpt_skpd', 0); }?>
		</script>
		
<? $this->load->view('_foot'); ?>
