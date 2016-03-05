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
</style>

<script>
$(window).load(function(){
	reload_grid();
	$("#proses_id").attr('class', 'form-control2');

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
	var rptparams = {
        rpt: rpt,
        id: mID,
    }
	
	var data = decodeURIComponent($.param(rptparams));
	var url  = '<? echo active_module_url($controller); ?>cetak/pdf/?'+data;
	
	var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
	window.open(url, 'Laporan', winparams);
}

function cek_bunga(){
      
       if (prosestgl_init==1){
       var prosestgl  = $('#prosestgl').val();
   	   } else {
   	   var prosestgl  = $('#sspdtgl').val();
   	   }

	    var nobunga = true;
	    var pesan = 'xxx';
	    $.ajax({
	        url: '<? echo active_module_url($controller); ?>cek_bunga/'+mID+'/'+prosestgl,
	        async: false,
	        success: function (j) {
	        	var data = $.parseJSON(j);
	            if (data == 'hmm') {
	                alert('Proses SSPD gagal');
	                return;
	            } 
	            if (data != 'nobunga') {
	                nobunga = false;
	                //pesan = data;
	                var jenis_bayar  = $('#jenis_bayar').val();
	                var pajak_sspd = parseFloat(data['pokok']);
					$('#pajak_sspd').autoNumeric('set', pajak_sspd);
	                var denda_sspd = parseFloat(data['bunga']);
	                $('#denda_sspd').autoNumeric('set', denda_sspd);

	                if(jenis_bayar==2){
	                var jumlah_sspd = pajak_sspd + denda_sspd;
		                $('#jumlah_sspd').autoNumeric('set', jumlah_sspd);
		                $('#sisa_sspd').autoNumeric('set', 0);
	            	}else{
	            		$('#jumlah_sspd').autoNumeric('set', pajak_sspd);
						$('#sisa_sspd').autoNumeric('set', denda_sspd);
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
        	pajak_sspdx = pajak_sspd.replace(/\./g,'');
        	pajak_sspd = parseFloat(pajak_sspdx);
			$('#pajak_sspd').autoNumeric('set', pajak_sspd);
        	$('#denda_sspd').autoNumeric('set', 0);
        	$('#sisa_sspd').autoNumeric('set', 0);
        	$('#jumlah_sspd').autoNumeric('set', pajak_sspd);


            $('#telat_bulan').html('-');
        }
}

function hitung_bayar(){
		var pajak_sspd = parseFloat($('#pajak_sspd').autoNumeric('get'));
        var denda_sspd = parseFloat($('#denda_sspd').autoNumeric('get'));
        var x = $('#jenis_bayar').val();
		if (x==1){
            var jumlah_sspd = pajak_sspd;
            $('#jumlah_sspd').autoNumeric('set', jumlah_sspd);
            $('#sisa_sspd').autoNumeric('set', denda_sspd);
		}
		else if(x==2){
            var jumlah_sspd = pajak_sspd + denda_sspd;
            $('#jumlah_sspd').autoNumeric('set', jumlah_sspd);
            $('#sisa_sspd').autoNumeric('set', 0);
		}
}

function switch_btn() {
    var proses_id  = $('#proses_id').val();
    var validasi_id  = $('#validasi_id').val();
    if (proses_id == 2) {
        $('#btn_proses_sspd').hide();
        $('#btn_batal_sspd').show();
    } else {
        $('#btn_proses_sspd').show();
        $('#btn_batal_sspd').hide();
    }
    if (validasi_id == 2) {
        $('#btn_validasi').hide();
        $('#btn_batal_validasi').show();
    } else {
        $('#btn_validasi').show();
        $('#btn_batal_validasi').hide();
    }
}
function reload_grid() {
	var proses_id  = $('#proses_id').val();
	var validasi_id = $('#validasi_id').val();
    var terimatgl = $('#terimatgl').val();
    var terimatgl2 = $('#terimatgl2').val();
	
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+proses_id+"/"+validasi_id+"/"+terimatgl+"/"+terimatgl2);
}

var mID;
var uID;
var tID;
var oTable;
var telat_bulan;
var pajak_sspd;
var prosestgl_init;

var jp_sspd, masa_sspd, jatuh_tempo, wp_sspd, no_bayar_sspd, nopd, cara_bayar;
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
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [4], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [6], "bSearchable": true,  "bVisible": true,  "sWidth": "", "sClass": "" },
			
			{ "aTargets": [8], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "center" },
			{ "aTargets": [9], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "right" },

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

					nopd 	= data[5];
					wp_sspd = data[6];
					jp_sspd = data[7];
					masa_sspd 	= data[8];
					jatuh_tempo = data[9];
					pajak_sspd = data[11];
					no_bayar_sspd = data[18];
					cara_bayar = data[14];

					
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
		'	<button id="btn_proses_sspd" data-loading-text="Proses SSPD <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-success">Proses</button>',
		'	<button id="btn_batal_sspd" data-loading-text="Batal SSPD <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-warning hide">Batal SSPD</button>',
		'</div>',
		'<div class="btn-group pull-left" style="padding-right:4px;">',
		'	<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input style="width:100px;" class="form-control2" type="text" name="prosestgl" id="prosestgl" value="<?=date('d-m-Y');?>"/></div>',
		'</div>',
		'<div class="btn-group pull-left">',
		/*'	<button id="btn_validasi" data-loading-text="Validasi <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-success">Validasi</button>',
		'	<button id="btn_batal_validasi" data-loading-text="Batal Validasi <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-warning hide">Batal Validasi</button>',
        */
		'</div>',
		'<div class="btn-group pull-left" style="padding-right:4px;">',
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="sspd">Cetak</button>',
		/*'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="validasi">Cetak Validasi</button>',*/
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_proses_id;?>',
		'</div>',
        /*
		'<div class="btn-group pull-left">',
		'<?=$select_validasi_id;?>',
		'</div>',
        */
		'<div class="btn-group pull-left">',
		'	<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input style="width:100px;" class="form-control2" type="text" name="terimatgl" id="terimatgl" value="<?=date('01-01-Y');?>"/></div>',
		'</div>',
		'<div class="btn-group pull-left"><h4> s/d </h4>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input style="width:100px;" class="form-control2" type="text" name="terimatgl2" id="terimatgl2" value="<?=date('d-m-Y');?>"/></div>',
		'</div><br>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#proses_id, #validasi_id').change(function() {
        if($(this).attr('name')=='proses_id') $("#validasi_id").val('1');
		reload_grid();
        switch_btn();
	});
	
	$('#pajak_sspd, #denda_sspd, #jumlah_sspd, #sisa_sspd').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});


	$('#btn_proses_sspd').click(function() {
		if(mID) {
            $('#sspdDialog').modal('show');
            var prosestgl  = $('#prosestgl').val();
			var proses_id  = $('#proses_id').val();
			$('#wp_sspd').val(wp_sspd);
			$('#masa_sspd').val(masa_sspd);
			$('#jp_sspd').val(jp_sspd);
			$('#jatuh_tempo').val(jatuh_tempo);
			$('#no_bayar_sspd').html(no_bayar_sspd);
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
				var prosestgl_init = 1 ; 
				cek_bunga();
				}
		}else{
			alert('Silahkan pilih data yang akan diproses');
		}
        return;
	});



	$('#jenis_bayar').change(function() {
		hitung_bayar();
	});
	
	$('#btn_dialog_ok').click(function() {
        var sspdtgl    = $('#sspdtgl').val();
        var proses_id  = $('#proses_id').val();
        var tp_id      = $('#tp_id').val();
        var ket        = $('#keterangan').val();
        var jenis_bayar = $('#jenis_bayar').val();
        var denda_sspd = parseFloat($('#denda_sspd').autoNumeric('get'));
        var sisa_sspd = parseFloat($('#sisa_sspd').autoNumeric('get'));
        var pajak_sspd = parseFloat($('#pajak_sspd').autoNumeric('get'));
        var total_sspd = pajak_sspd+denda_sspd ;
        var petugas_id = $('#petugas_id').val();
        var pejabat_id = $('#pejabat_id').val();


        if (proses_id == 2)
            alert('Data ini sudah diproses');
        else {
        pesan = 'Pastikan Data Sudah Benar! Proses Data ini?';
        if ($('#sisa_sspd').val() != 0){
        pesan = 'Proses STPD akan otomatis dibuat karena ada sisa pembayaran. Pastikan Data Sudah Benar! Proses Data ini?';
    	}
        var proses = confirm(pesan);
			if(proses == true) {
                $(this).button('loading');
                setTimeout(function() {
                    $.ajax({
                        url: '<? echo active_module_url($controller); ?>proses_sspd/'+mID+'/'+sspdtgl+'/'+denda_sspd+'/'+total_sspd+'/'+sisa_sspd+'/'+telat_bulan+'/'+jenis_bayar+'/'+petugas_id +'/'+pejabat_id+'/'+ket,
                        async: false,
                        success: function (data) {
                            if (data != 'ok') 
                                alert('Proses SSPD gagal');
                            else {
                            	alert('Proses SSPD Berhasil');
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
        $('#sspdDialog').modal('hide');

    }
        $('#sspdDialog').modal('hide');
	});

	$('#btn_batal_sspd').click(function() {
		if(mID) {
			var anSelected = fnGetSelected( oTable );
			if ( anSelected.length !== 0 ) {
				var data = oTable.fnGetData( anSelected[0] );
				if (data[1] != null) {
					var proses = confirm('Batalkan SSPD data ini?');
					if(proses == true) {
						$(this).button('loading');
                        setTimeout(function() {
                            $.ajax({
                                url: '<? echo active_module_url($controller); ?>batal_sspd/'+mID,
                                async: false,
                                success: function (data) {
                                    if (data != 'ok') 
                                        alert('Proses pembatalan gagal');
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
				else alert('Data ini belum diproses - tidak dapat melakukan pembatalan');
			}
		}else{
			alert('Silahkan pilih data yang akan dibatalkan');
		}
	});
	
	$('#btn_validasi').click(function() {
		if(mID) {
			var proses_id  = $('#proses_id').val();
			if (proses_id ==1) {
				alert('Data ini belum diproses SSPD!');
                return;
            }
			var validasi_id  = $('#validasi_id').val();
			if (validasi_id == 2)
				alert('Data ini sudah divalidasi');
			else {
				var proses = confirm('Validasi data ini?');
				if(proses == true) {
					$(this).button('loading');
                    setTimeout(function() {
                        $.ajax({
                            url: '<? echo active_module_url($controller); ?>proses_validasi/'+mID,
                            async: false,
                            success: function (data) {
                                if (data != 'ok') 
                                    alert('Validasi gagal');
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
			alert('Silahkan pilih data yang akan divalidasi');
		}
	});
	
	$('#btn_batal_validasi').click(function() {
		if(mID) {
			var validasi_id  = $('#validasi_id').val();
			if (validasi_id == 2) {
				var proses = confirm('Batalkan validasi data ini?');
				if(proses == true) {
					$(this).button('loading');
                    setTimeout(function() {
                        $.ajax({
                            url: '<? echo active_module_url($controller); ?>batal_validasi/'+mID,
                            async: false,
                            success: function (data) {
                                if (data != 'ok') 
                                    alert('Pembatalan validasi gagal');
                                else reload_grid();
                            },
                            error: function (xhr, desc, er) {
                                alert(er);
                            }
                        })
                    }, 0);
					$(this).button('reset');
				}
			} else alert('Data ini belum divalidasi');
		}else{
			alert('Silahkan pilih data yang akan dibatalkan');
		}
	});

	$("[id=btnshow_rpt]").click(function(){
		if(mID) {
            if($(this).data('rpt')=='validasi') {
                var validasi_id  = $('#validasi_id').val();
                if (validasi_id == 1) {
                    alert('Data ini belum divalidasi!');
                    return;
                }
            } else {
                var proses_id  = $('#proses_id').val();
                if (proses_id ==1) {
                    alert('Data ini belum diproses SSPD!');
                    return;
                }
            }
            
			var anSelected = fnGetSelected( oTable );
			if ( anSelected.length !== 0 ) {
				/* oTable.fnDeleteRow( anSelected[0] ); */
				
				var data = oTable.fnGetData( anSelected[0] );
				if (data[1] != null) {
                    var rpt = $(this).data('rpt');
                    show_rpt(rpt);
                } else alert('Data ini belum diproses');
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
    
	var sspdtgl_dtp = $('#sspdtgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		var prosestgl_init = 2;
		cek_bunga();
		sspdtgl_dtp.hide();
		hitung_bayar();
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

<style>
    div.datepicker {
        z-index: 999999 !important;
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
<div class="content">  

	<!-- Modal -->
<div class="modal fade" id="sspdDialog" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="dlgKategoriLabel">PROSES</h4>
            </div>
            <div class="content">
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-sm-3" >Nomor Periksa</label>
                    <div class="col-sm-3">
                    <input class="form-control" type="text" name="no_periksa" id="no_periksa"/>
                    </div>
                    <label class="control-label col-sm-3" for="sspdtgl">Tanggal</label>
                    <div class="col-sm-3">
                        <div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input class="form-control" type="text" name="sspdtgl" id="sspdtgl" value="<?=date('d-m-Y')?>" required /></div>
                    </div>
                    <label class="control-label col-sm-3" >Nomor SPT/SK</label>
                    <div class="col-sm-3">
                    <input class="form-control" type="text" name="no_spt" id="no_spt" readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="tgl_spt">Tanggal SPT/SK</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="tgl_spt" id="tgl_spt" readonly/>
                    </div>
                    <label class="control-label col-sm-3" >Nomor Bayar</label>
                    <div class="col-sm-3">
                    <label id="no_bayar_sspd" class="text-muted well well-sm no-shadow"></label>
                    </div>
                    <label class="control-label col-sm-3" for="tgl_byr">Tanggal Bayar</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="tgl_by" id="tgl_byr" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="wp_sspd">Wajib Pajak</label>
                    <div class="col-sm-6">
                        <input class="form-control" type="text" name="wp_sspd" id="wp_sspd" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="jp_sspd">Jenis Usaha</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="jp_sspd" id="jp_sspd"  readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="jp_sspd">Jenis Pajak</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="jp_sspd" id="jp_sspd"  readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="masa_sspd">Masa Pajak</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="masa_sspd" id="masa_sspd" readonly/>
                    </div>

                    <div class="col-sm-12">
                    </div>

                    <label class="control-label col-sm-3" for="dasar">Dasar</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="dasar" id="dasar" readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="dasar_s">Seharusnya</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="dasar_s" id="dasar_s"/>
                    </div>
                    <label class="control-label col-sm-3" for="tarif">Tarif</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="tarif" id="tarif" readonly/>
                    </div>

                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="pajak_sspd">Pajak Terhutang</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="pajak_sspd" id="pajak_sspd" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="denda_sspd">Denda</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="denda_sspd" id="denda_sspd" readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="denda_sspd">Denda</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="denda_sspd" id="denda_sspd" readonly/>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="sisa_sspd">Sisa</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="sisa_sspd" id="sisa_sspd" readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="sisa_sspd">Sisa</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="sisa_sspd" id="sisa_sspd" readonly/>
                    </div>
                    <label class="control-label col-sm-3" for="status_id">Status</label>
                    <div class="col-sm-3">
                        <?=$select_status;?>
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="keterangan">Keterangan</label>
                    <div class="col-sm-6">
                        <input class="form-control" type="text" name="keterangan" id="keterangan" value="" />
                    </div>
                    <div class="col-sm-12">
                    </div>
                    <label class="control-label col-sm-3" for="petugas_id">Ketua Tim</label>
                    <div class="col-sm-3">
                        <?=$select_petugas;?>
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
    

   <div id="cuDialog" class="modal in" style="padding-right:10%;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Wajib Pajak - Usaha : <? echo implode(" ",$this->session->userdata('usaha_nama'))?> </h4>
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

    <!-- content -->
	<div class="container-fluid">  
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>PEMERIKSAAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>


		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>Tahun</th>
					<th>No. Periksa</th>
					<th>Tgl Periksa</th>
					<th>No. LHP</th>
					<th>Keterangan</th>
					<th>Pokok</th>
					<th>Denda</th>
					<th>Bunga</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

	</div>
</div>
<? $this->load->view('_foot'); ?>