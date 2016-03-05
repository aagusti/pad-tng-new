<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

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
	oTableLeft.fnReloadAjax("<?=active_module_url();?>penerimaan/grid_pajak_terhutang/<?=$dt['customer_id']?>");
	oTableRight.fnReloadAjax("<?=active_module_url();?>penerimaan/grid_terima_line/<?=$dt['id']?>");
}

<!-- // -->
var mID;
var oTable;

var oIDLeft;
var oTableLeft;

var oIDRight;
var oTableRight;

$(document).ready(function() {
	function get_cu(customer_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>sptpd/get_cu/"+customer_id,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);
				$("#customer_id").val(data['customer_id']);
				$("#npwpd").val(data['npwpd']);
				$("#nama").val(data['customernm']);
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
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
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "60px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "220px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": false, "bVisible": false, "sWidth": "200px", "sClass": "" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					
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
		"sAjaxSource": "<?=active_module_url('objek_pajak/grid_for_nopd');?>"
	});

	oTableLeft = $('#tableLeft').dataTable({
		"iDisplayLength": 9,
		"bJQueryUI": true,
		"bFilter": true,
		"bInfo": false,
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 1, "asc" ]],
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [0] },
			{ "bSearchable": true, "bVisible": true, "aTargets": [3], "sClass": "right" }, 
			{ "bSearchable": true, "bVisible": true, "aTargets": [4], "sClass": "right" }, 
			{ "bSearchable": true, "bVisible": true, "aTargets": [5], "sClass": "right" }, 
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					oIDLeft = '';
					$('#btn_op_cancel').trigger('click');
					
					$(this).removeClass('row_selected');
				} else {
					var data = oTableLeft.fnGetData( this );
					oIDLeft = data[0];
					
					oTableLeft.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			oIDLeft = '';
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
		"sAjaxSource": "<?=active_module_url();?>penerimaan/grid_pajak_terhutang/<?=$dt['customer_id']?>"
	});
	
	oTableRight = $('#tableRight').dataTable({
		"iDisplayLength": 9,
		"bJQueryUI": true,
		"bFilter": true,
		"bInfo": false,
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 1, "asc" ]],
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [0] },
			{ "bSearchable": true, "bVisible": true, "aTargets": [3], "sClass": "right" }, 
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					oIDRight = '';
					$('#btn_op_cancel').trigger('click');
					
					$(this).removeClass('row_selected');
				} else {
					var data = oTableRight.fnGetData( this );
					oIDRight = data[0];
					
					oTableRight.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			oIDRight = '';
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
		"sAjaxSource": "<?=active_module_url();?>penerimaan/grid_terima_line/<?=$dt['id']?>"
	});
	
	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url($this->uri->segment(2));?>';
	});

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			get_cu(mID);			
			$('#cuDialog').modal('hide');
		}
	});

	/* Post */
	$('#btn_to_right').click(function() {
		if (oIDLeft == '' || oIDLeft == null)
			alert('Data belum dipilih.');
		else {
			$.ajax({
				url: '<? echo active_module_url(); ?>penerimaan/post/'+oIDLeft+'/'+'<?=$dt['id']?>',
				async: false,
				success: function (data) {
					$('#jmlterima').autoNumeric('set',data);
					reload_grid();
				},
				error: function (xhr, desc, er) {
					alert(er);
				}
			});
		}
	});
	
	/* Unpost */
	$('#btn_to_left').click(function() {
		if (oIDRight == '' || oIDRight == null)
			alert('Data belum dipilih.');
		else {
			$.ajax({
				url: '<? echo active_module_url(); ?>penerimaan/unpost/'+oIDRight,
				async: false,
				success: function (data) {
					$('#jmlterima').autoNumeric('set',data);
					reload_grid();
				},
				error: function (xhr, desc, er) {
					alert(er);
				}
			});
		}
	});
	
	
	$('#jmlterima').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});
		
	var terimatgl_dtp = $('#terimatgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		terimatgl_dtp.hide();
	}).data('datepicker');
	
	$('#npwpd').typeahead({
		source: function(query, process){
			/* 
			return $.get('<?=active_module_url('subjek_pajak/get_typeahead_npwpd');?>', {q: query}, function(response) { 
			*/
			$.getJSON('<?=active_module_url('subjek_pajak/get_typeahead_npwpd');?>'+query, function(response) {
				var data = new Array;
				for(var i in response) {
					data.push(response[i]['id'] +"#"+ response[i]['npwpd'] +"#"+ response[i]['customernm']);
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
				npwpd = parts[1].replace(regex, "<strong>$1</strong>"),
				html = '<div class="typeahead">';
				
			/* 
			html += '<div class="left"><img src="assets/img/'+parts[2]+'" width="32" height="32" class="img-rounded"></div>' 
			*/
			html += '<div class="left">';
			html += '<div>'+npwpd+'</div>';
			html += '<div>'+parts[2]+'</div>';
			html += '</div>';
			html += '<div class="clear"></div>';
			html += '</div>';
			return html;
		},
		updater: function(item) {
			var parts = item.split('#');
			/* selectedItem = parts[1]; */
			get_cu(parts[0]);
			
			return parts[1];
		},
	});
		 
	$('#nama').typeahead({
		source: function(query, process){
			$.getJSON('<?=active_module_url('subjek_pajak/get_typeahead_csname');?>'+query, function(response) {
				var data = new Array;
				for(var i in response) {
					data.push(response[i]['id'] +"#"+ response[i]['npwpd'] +"#"+ response[i]['customernm']);
				}
				return process(data);
			});
		},
		matcher: function (item) {
			var parts = item.split('#');
			return parts[2].toLowerCase().indexOf(this.query.trim().toLowerCase()) != -1;
		},
		highlighter: function(item) {
			var parts = item.split('#'),        
				regex = new RegExp('(' + this.query + ')', 'i'),
				nama = parts[2].replace(regex, "<strong>$1</strong>"),
				html = '<div class="typeahead">';
				
			html += '<div class="left">';
			html += '<div>'+nama+'</div>';
			html += '<div>'+parts[1]+'</div>';
			html += '</div>';
			html += '<div class="clear"></div>';
			html += '</div>';
			return html;
		},
		updater: function(item) {
			var parts = item.split('#');
			get_cu(parts[0]);			
			return parts[2];
		},
	});
	
	
	/* init page */
	var cid = parseInt(<?=$dt['customer_id']?>);
	if (!isNaN(cid)) {
		get_cu(<?=$dt['customer_id']?>);
	}
	
	var mode = '<?=$this->uri->segment(3);?>';
	if (mode == 'edit') {		
		$('#terimatgl').attr('readonly', 'readonly');
		$('#npwpd').attr('readonly', 'readonly');
		$('#nama').attr('readonly', 'readonly');
		
		$('#jmlterima').attr('readonly', 'readonly');
		$('#notes').attr('readonly', 'readonly');
		
		$('#btn_submit').hide();
		$('#btn_cancel').text('Kembali');
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
			<h3 id="cuDialogLabel">Subjek Pajak</h3>
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
				<a href="#"><strong>PENERIMAAN - POSTING</strong></a>
			</li>
		</ul>

		<?php
		echo msg_block();
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		}
		?>

		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
			<input class="form-control" type="hidden" name="id" value="<?=$dt['id']?>" placeholder="id"/>
			<input class="form-control" type="hidden" name="customer_id" id="customer_id" value="<?=$dt['customer_id']?>" placeholder="customer_id"/>

			<div class="form-group">
				<label class="control-label col-sm-1" for="sptno">No. Terima</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="tahun" id="tahun" value="<?=$dt['tahun']?>" readonly />
					<input class="form-control" type="text" name="sptno" id="sptno" value="<?=$dt['terimano']?>" readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="terimatgl">Tanggal Terima</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=$dt['terimatgl']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="npwpd">Subjek Pajak</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" autocomplete="off" />
					<input class="form-control" type="text" name="nama" id="nama" value="" placeholder="Nama Subjek Pajak" autocomplete="off" />
					<button type="button" class="btn hide" data-toggle="modal" data-target="#cuDialog">Cari</button>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="jmlterima">Jumlah Setoran</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="jmlterima" id="jmlterima" value="<?=$dt['jmlterima']?>" style="text-align:right;" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="notes">Keterangan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="notes" id="notes" value="<?=$dt['notes']?>" />
				</div>
			</div>
			
			<? if ($dt['id'] > 0) : ?>
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#"><strong>Daftar SPTPD/SK - Posting Penerimaan</strong></a>
				</li>
			</ul>
			
			
						<div class="row">
							<div class="span6">
								<table class="table" id="tableLeft">
									<thead>
										<tr>
											<th>Index</th>
											<th>SPTPD</th>
											<th>NOPD</th>
											<th>Pajak</th>
											<th>Bunga</th>
											<th>Total</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<div class="span2" style="width:60px;">
								<div class="row">
									<div class="span2" style="width:60px;">
										<center><button type="button" class="btn" id="btn_to_right"><i class="icon-hand-right"></i></button></center>
									</div>
								</div>
								<hr />
								<div class="row">
									<div class="span2" style="width:60px;">
										<center><button type="button" class="btn" id="btn_to_left"><i class="icon-hand-left"></i></button></center>
									</div>
								</div>
							</div>
							<div class="span5">
								<table class="table" id="tableRight">
									<thead>
										<tr>
											<th>Index</th>
											<th>SPTPD</th>
											<th>NOPD</th>
											<th>Setoran</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
			
			<? endif; ?>
			
			<hr />
			<button type="submit" class="btn btn-primary" id="btn_submit">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
	</div>
</div>
<? $this->load->view('_foot'); ?>