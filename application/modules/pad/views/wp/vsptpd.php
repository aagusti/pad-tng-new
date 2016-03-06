<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/wp/_navbar'); ?>

<style>
.nav-tabs > .active > a, .nav-pills > .active > a:hover {
    color: blue;
}
.table.dataTable {
	margin-bottom: 8px;
	font-size: 10px;
}

input {
	height: 14px !important;
	border-radius: 2px 2px 2px 2px !important;
	margin-bottom: 2px !important;
}

select {
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

.dataTables_filter {
	width: 280px;
}
.dataTables_processing {
    top: 50%;
    border: 0;
	background: none;
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

function reload_grid() {
	var type_id  = $('#type_id').val();
	var usaha_id = $('#usaha_id').val();
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+type_id+"/"+usaha_id);
}


var mID;
var uID;
var tID;
var oTable;

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
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "50px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "75px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "80px", "sClass": "" },
			
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "66px", "sClass": "" },
			{ "aTargets": [6], "bSearchable": true,  "bVisible": true,  "sWidth": "66px", "sClass": "" },
			{ "aTargets": [7], "bSearchable": true,  "bVisible": true,  "sWidth": "77px", "sClass": "right" },
			{ "aTargets": [8], "bSearchable": true,  "bVisible": true,  "sWidth": "77px", "sClass": "right" },
			{ "aTargets": [9], "bSearchable": true,  "bVisible": true,  "sWidth": "60px", "sClass": "center" },
			{ "aTargets": [10], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [11], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					uID = data[10];
					tID = data[11];
					
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
		'<div class="btn-group pull-left">',
		'	<button id="add_self" class="btn btn-primary">Tambah</span></button>',
		'	<button id="btn_edit" class="btn">Edit</button>',
		'	<button id="btn_delete" class="btn">Hapus</button>',
		'</div>',
		<? if ($this->uri->segment(2) != 'sptpd') : ?>
		'<div class="btn-group pull-left">',
		'	<button id="btn_proses" data-loading-text="Proses <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-success">Proses</button>',
		'	<button id="btn_recall" data-loading-text="Recall <img border=\'0\' src=\'<?=base_url('assets/pad/img/ajax-loader-fb-white.gif')?>\' />" class="btn btn-warning">Recall</button>',
		'</div>',
		<? endif; ?>
		'<div class="btn-group pull-left">',
		'<?=$select_sptpd_type;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_usaha;?>',
		'</div>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#type_id, #usaha_id').change(function() {
		reload_grid();
	});

	$('#btn_tambah1').click(function() {
		window.location = '<? echo active_module_url($controller); ?>add/';
	});

	$('#add_self').click(function() {
		window.location = '<? echo active_module_url($controller); ?>add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<? echo active_module_url($controller); ?>edit/'+uID+'/'+tID+'/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<? echo active_module_url($controller); ?>delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
	
	<? if ($this->uri->segment(2) != 'sptpd') : ?>
	$('#btn_proses').click(function() {
		if(mID) {
			var anSelected = fnGetSelected( oTable );
			if ( anSelected.length !== 0 ) {
				/* oTable.fnDeleteRow( anSelected[0] ); */
				
				var data = oTable.fnGetData( anSelected[0] );
				if (data[1] != null) alert('Data ini sudah ditetapkan');
				else {
					var proses = confirm('Proses data ini?');
					if(proses == true) {
						$(this).button('loading');
						$.ajax({
							url: '<? echo active_module_url($controller); ?>proses_skpd/'+uID+'/'+mID,
							async: false,
							success: function (data) {
								if (data != 'ok') 
									alert('Proses penetapan gagal');
								else reload_grid();
							},
							error: function (xhr, desc, er) {
								alert(er);
							}
						});
						$(this).button('reset');
					}
				}
			}		
		}else{
			alert('Silahkan pilih data yang akan diproses');
		}
	});
	
	$('#btn_recall').click(function() {
		if(mID) {
			var anSelected = fnGetSelected( oTable );
			if ( anSelected.length !== 0 ) {
				/* oTable.fnDeleteRow( anSelected[0] ); */
				
				var data = oTable.fnGetData( anSelected[0] );
				if (data[1] != null) {
					var proses = confirm('Recall data ini?');
					if(proses == true) {
						$(this).button('loading');
						$.ajax({
							url: '<? echo active_module_url($controller); ?>recall_skpd/'+uID+'/'+mID,
							async: false,
							success: function (data) {
								if (data != 'ok') 
									alert('Proses recall gagal');
								else reload_grid();
							},
							error: function (xhr, desc, er) {
								alert(er);
							}
						});
						$(this).button('reset');
					}
				}
				else alert('Data ini belum ditetapkan - tidak dapat melakukan recall');
			}
		}else{
			alert('Silahkan pilih data yang akan direcall');
		}
	});
	<? endif; ?>
});
</script>

<div class="content">  
	<div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong><? echo $this->uri->segment(2) == 'sptpd' ? 'PENDATAAN - SPTPD' : 'PENETAPAN - SK'; ?></strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th><? echo $this->uri->segment(2) == 'sptpd' ? 'SPTPD' : 'SKPD'; ?></th>
					<th>Tanggal</th>
					<th>NOPD</th>
					<th>Subjek Pajak</th>
					<th>Masa Dari</th>
					<th>Masa s/d</th>
					<th>Omset</th>
					<th>Pajak</th>
					<th>Bayar</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

	</div>
</div>
<? $this->load->view('_foot'); ?>