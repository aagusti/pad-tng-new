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

<script>
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
	var rptparams = {
        rpt: rpt,
        id: mID,
    }
	
	var data = decodeURIComponent($.param(rptparams));
	var url  = '<? echo active_module_url($controller); ?>cetak/pdf/?'+data;
	
	var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
	window.open(url, 'Laporan', winparams);
}

function switch_btn() {
    var proses_id  = $('#proses_id').val();
    if (proses_id == 2) {
        $('#btn_proses_stpd').hide();
        $('#btn_batal_stpd').show();

    } else {
        $('#btn_proses_stpd').show();
        $('#btn_batal_stpd').hide();
    }
}

function reload_grid() {
	var proses_id  = $('#proses_id').val();
	var bulan_id  = $('#bulan_id').val();
	var tahun  = $('#tahun').val();
	if (proses_id == 2) {
		/* $('th.th1').html('STPD <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>'); */
		$('th.th6').html('Masa <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
		$('th.th7').html('J.Tempo <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
		// $('th.th8').html('Pajak <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
		// $('th.th9').html('Bunga <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
	} else {
		/* $('th.th1').html('SPTPD <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>'); */
		$('th.th6').html('Masa <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
		$('th.th7').html('J.Tempo <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
		// $('th.th8').html('Dasar <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
		// $('th.th9').html('Pajak <span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>');
	}
	
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+proses_id+'/'+tahun+'/'+bulan_id);
}

function cek_bunga(){
       var prosestgl  = $('#stpdtgl').val();
	    var nobunga = true;
	    var pesan = 'xxx';
	    $.ajax({
	        url: '<? echo active_module_url($controller); ?>cek_bunga/'+mID_spt+'/'+prosestgl,
	        async: false,
	        success: function (j) {
	        	var data = $.parseJSON(j);
	            if (data == 'hmm') {
	                alert('Proses stpd gagal');
	                return;
	            } 
	            if (data != 'nobunga') {
	                nobunga = false;
	                //pesan = data;
	                var jenis_bayar  = $('#jenis_bayar').val();
	                var pajak_stpd = parseFloat(data['pokok']);
					$('#pajak_stpd').autoNumeric('set', pajak_stpd);
	                var denda_stpd = parseFloat(data['bunga']);
	                $('#denda_stpd').autoNumeric('set', denda_stpd);

	                if(jenis_bayar==2){
	                var jumlah_stpd = pajak_stpd + denda_stpd;
		                $('#jumlah_stpd').autoNumeric('set', jumlah_stpd);
		                $('#sisa_stpd').autoNumeric('set', 0);
	            	}else{
	            		$('#jumlah_stpd').autoNumeric('set', pajak_stpd);
						$('#sisa_stpd').autoNumeric('set', denda_stpd);
	            	}
	            	
	                $('#telat_bulan').html(data['terlambat']);
	                telat_bulan = data['terlambat'];
	            }
	        },
	        error: function (xhr, desc, er) {
	            alert(er);
	        }
	    });
        if (nobunga==true){
        	pajak_stpdx = pajak_stpd.replace(/\./g,'');
        	pajak_stpd = parseFloat(pajak_stpdx);
			$('#pajak_stpd').autoNumeric('set', pajak_stpd);
        	$('#denda_stpd').autoNumeric('set', 0);
        	$('#sisa_stpd').autoNumeric('set', 0);
        	$('#jumlah_stpd').autoNumeric('set', pajak_stpd);


            $('#telat_bulan').html('-');
        }
}

var mID;
var uID;
var tID;
var stpdno;
var oTable;
var prosestgl_init;
var jp_stpd, masa_stpd, jatuh_tempo, wp_stpd, no_bayar_stpd, nopd, cara_bayar;

$(window).load(function(){ 
		reload_grid();
		$("#proses_id, #bulan_id, #tahun").attr('class', 'form-control2');
		$('#btn_batal_stpd').hide();
 });
 

$(document).ready(function() {	
	oTable = $('#table1').dataTable({
		"iDisplayLength": 13,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumnDefs": [
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [1], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [4], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [10], "bSearchable": false,  "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [11], "bSearchable": true,  "bVisible": true,  "sWidth": "82px", "sClass": "right" },
			{ "aTargets": [12], "bSearchable": false, "bVisible": true, "sWidth": "95px", "sClass": "right" },
			{ "aTargets": [13], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [14], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					mID_spt = data[1];
					stpdno = data[2];
					uID = data[11];
					tID = data[12];
					
					nopd 	= data[5];
					wp_stpd = data[7];
					jp_stpd = data[8];
					masa_stpd 	= data[9];
					jatuh_tempo = data[10];
					no_bayar_stpd = data[13];

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
		
	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btn_proses_stpd" data-loading-text="Proses STPD <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-success">Proses</button>',
		'	<button id="btn_batal_stpd" data-loading-text="Recall STPD <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-warning">Batal STPD</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="stpd"> <i class="fa fa-print"></i> Cetak</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_proses_id;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_bulan;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<input class="form-control" typle="text" id="tahun" style="width:70px;" value="<?=date('Y')?>" >',
		'</div>',
		'</br>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#proses_id, #bulan_id').change(function() {
        switch_btn();
		reload_grid();
	});


	$('#pajak_stpd, #denda_stpd, #jumlah_stpd').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});


	$('#btn_proses_stpd').click(function() {
		if(mID_spt) {
            $('#stpdDialog').modal('show');
            var prosestgl  = $('#stpdtgl').val();
			var proses_id  = $('#proses_id').val();
			$('#wp_stpd').val(wp_stpd);
			$('#masa_stpd').val(masa_stpd);
			$('#jp_stpd').val(jp_stpd);
			$('#jatuh_tempo').val(jatuh_tempo);
			$('#no_bayar_stpd').html(no_bayar_stpd);
			$('#cara_bayar').html('');

			if(cara_bayar=='TRF'){
			var options =
			[
			  {
			    "text"  : "TELLER / ATM",
			    "value" : 1
			  },
			  {
			    "text"     : "TRANSFER",
			    "value"    : 2,
			    "selected" : true
			  },
			];
			}else{
			var options =
			[
			  {
			    "text"  : "TELLER / ATM",
			    "value" : 1,
			    "selected" : true
			  },
			  {
			    "text"     : "TRANSFER",
			    "value"    : 2,
			  },
			];
			}

			var selectBox = document.getElementById('cara_bayar');

			for(var i = 0, l = options.length; i < l; i++){
			  var option = options[i];
			  selectBox.options.add( new Option(option.text, option.value, option.selected) );
			}
			
			if (proses_id == 2){
				alert('Data ini sudah diproses');
			}
			else {
				cek_bunga();
				}
		}else{
			alert('Silahkan pilih data yang akan diproses');
		}
        return;
	});

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

	$('#btn_proses_stpdx').click(function() {
		if(mID) {
			var proses_id  = $('#proses_id').val();
			if (proses_id == 2)
				alert('Data ini sudah diproses');
			else {
				var proses = confirm('Proses data ini?');
				if(proses == true) {
					$(this).button('loading');
                    setTimeout(function() {
                        $.ajax({
                            url: '<? echo active_module_url($controller); ?>proses_stpd/'+mID,
                            async: false,
                            success: function (data) {
                                if (data != 'ok') 
                                    alert('Proses STPD gagal');
                                else reload_grid();
                            },
                            error: function (xhr, desc, er) {
                                alert(er);
                            }
                        })
                    }, 0);
					$(this).button('reset');
				}
			}
		}else{
			alert('Silahkan pilih data yang akan diproses');
		}
	});


	$('#btn_dialog_ok').click(function() {
        var stpdtgl    = $('#stpdtgl').val();
        var proses_id  = $('#proses_id').val();
        var tp_id      = $('#tp_id').val();
        var ket        = $('#keterangan').val();
        var jenis_bayar = $('#jenis_bayar').val();
        var denda_stpd = parseFloat($('#denda_stpd').autoNumeric('get'));
        var pajak_stpd = parseFloat($('#pajak_stpd').autoNumeric('get'));
        var total_stpd = pajak_stpd+denda_stpd ;
        var petugas_id = $('#petugas_id').val();
        var pejabat_id = $('#pejabat_id').val();
        
        if (proses_id == 2)
            alert('Data ini sudah diproses');
        else {
        pesan = 'Pastikan Data Sudah Benar! Proses Data ini?';
        var proses = confirm(pesan);
			if(proses == true) {
                $(this).button('loading');
                setTimeout(function() {
                        $.ajax({
                            url: '<? echo active_module_url($controller); ?>proses_stpd/'+mID_spt+'/'+petugas_id +'/'+pejabat_id,
                            async: false,
                            success: function (data) {
                                if (data != 'ok') 
                                    alert('Proses STPD gagal !');
                                else {
                                    alert('Proses STPD Berhasil !');
                                	reload_grid();
                                }

                            },
                            error: function (xhr, desc, er) {
                                alert(er);
                            }
                        })
                }, 0);
                $(this).button('reset');
            // }
        }
        else
        $('#stpdDialog').modal('hide');
    }
        $('#stpdDialog').modal('hide');
	});
	
	$('#btn_batal_stpd').click(function() {
		if(mID) {
			var anSelected = fnGetSelected( oTable );
			if ( anSelected.length !== 0 ) {
				var data = oTable.fnGetData( anSelected[0] );
				if (data[1] != null) {
					var proses = confirm('Batalkan STPD data ini? No. STPD: '+stpdno);
					if(proses == true) {
						$(this).button('loading');
                        setTimeout(function() {
                            $.ajax({
                                url: '<? echo active_module_url($controller); ?>batal_stpd/'+mID,
                                async: false,
                                success: function (data) {
                                    if (data != 'ok') 
                                        alert('Proses pembatalan gagal !');
                                    else {
                                        alert('Proses pembatalan berhasil !');
                                    	reload_grid();
                                    }
                                },
                                error: function (xhr, desc, er) {
                                    alert(er);
                                }
                            })
                         }, 0);
						$(this).button('reset');
					}
				}
				else alert('Data ini belum diproses - tidak dapat melakukan pembatalan');
			}
		}else{
			alert('Silahkan pilih data yang akan dibatalkan');
		}
	});
    
	$("[id=btnshow_rpt]").click(function(){
		if(mID) {
			var proses_id  = $('#proses_id').val();
			if (proses_id != 2)
				alert('Data ini belum diproses');
			else {
                var rpt = $(this).data('rpt');
                show_rpt(rpt);
            }
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});

	var stpdtgl_dtp = $('#stpdtgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		cek_bunga();
		stpdtgl_dtp.hide();
	}).data('datepicker');

});

</script>

<style>
    div.datepicker {
        z-index: 999999 !important;
    }
    .well {
		margin-bottom: 0px;
	}
	.form-control2 {
	border-radius: 0px !important;
    display: block;
    width: 100%;
    height: 35px;
    padding: 0px 12px;
    font-size: 14px;
    line-height: 1.42857;
    color: #555;
    background-color: #FFF;
    background-image: none;
    border: 1px solid #CCC;
    }

    .form-control {
    height: 27px;
    padding: 0px 12px;
	}
</style>

	<!-- Modal -->
<div class="modal fade" id="stpdDialog" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="dlgKategoriLabel">PROSES STPD</h4>
            </div>
            <div class="content">
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-sm-3" >Nomor SK/SPT</label>
                    <div class="col-sm-6">
                    <label id="no_bayar_stpd" class="text-muted well well-sm no-shadow"></label>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="stpdtgl">Tanggal SK/SPT</label>
                    <div class="col-sm-3">
                        <div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span>
                        <input class="form-control" type="text" name="stpdtgl" id="stpdtgl" value="<?=date('d-m-Y')?>" required />
                        </div>
                    </div>
                    <label class="control-label col-sm-2" for="jatuh_tempo">Jatuh Tempo</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="jatuh_tempo" id="jatuh_tempo" readonly/>
                    </div>
					<label class="control-label col-sm-3" for="cara_bayar">Cara Bayar</label>
					<div class="col-sm-3">
						<select class="form-control" name="cara_bayar" id="cara_bayar">
						</select>
					</div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="keterangan">Keterangan</label>
                    <div class="col-sm-6">
                        <input class="form-control" type="text" name="keterangan" id="keterangan" value="" />
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="wp_stpd">Wajib Pajak</label>
                    <div class="col-sm-6">
                        <input class="form-control" type="text" name="wp_stpd" id="wp_stpd" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="jp_stpd">Jenis Pajak</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="jp_stpd" id="jp_stpd"  readonly/>
                    </div>
                    <label class="control-label col-sm-2" for="masa_stpd">Masa Pajak</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="masa_stpd" id="masa_stpd" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="pajak_stpd">Pajak Terhutang</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="pajak_stpd" id="pajak_stpd" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="denda_stpd">Denda</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="denda_stpd" id="denda_stpd" readonly/>
                    </div>
                    <div class="col-sm-6">
                    <label>( Tunggakan : &nbsp;</label><label id="telat_bulan"></label><label>&nbsp; Bulan )</label>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="petugas_id">Petugas Penetap</label>
                    <div class="col-sm-3">
                        <?=$select_petugas;?>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="pejabat_id">Pejabat Penandatangan</label>
                    <div class="col-sm-3">
                        <?=$select_pejabat;?>
                    </div>
	                <div class="content">
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					</div>
	            </div>
            	<div class="modal-footer">
				<button type="submit" class="btn btn-primary" id="btn_dialog_ok">OK!</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
				</div>
				</div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->

<div class="content">  
	<div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>PENAGIHAN - STPD</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>id spt</th>
					<th>STPD</th>
					<th>Tanggal</th>
					<th>SPT/SK</th>
					<th>Dok.</th>
					<th>NOPD</th>
					<th>Wajib Pajak</th>
					<th>Jenis Pajak</th>
					<th class="th6">Masa</th>
					<th class="th7">J.Tempo</th>
					<th class="th8">Pajak</th>
					<th class="th9">Denda</th>
					<th>No. Bayar</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

	</div>
</div>
<? $this->load->view('_foot'); ?>