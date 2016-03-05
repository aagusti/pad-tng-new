<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
	var mID;
	var mNama;
	var oTable;
	$(document).ready(function() {
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
						mID = ''; mNama = '';
						$(this).removeClass('row_selected');
					} else {
						var data = oTable.fnGetData( this );
						mID = data[1];
						mNama = data[2];

						oTable.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}
				})
			},
			"fnDrawCallback": function( oSettings ) {
				mID = ''; mNama = '';
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
			"sAjaxSource": "<?=active_module_url('user_reg/grid_wp');?>"
		});
		var tb_array = [
		'<br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

$('#btn_dialog_ok').click(function() {
	if (mID == '' || mID == null)
		alert('Data belum dipilih.');
	else {
		$('#userid').val(mID.replace(/\./g, ''));
		$('#nama').val(mNama);
		$('#cuDialog').modal('hide');
	}
});

$('#btn_cancel').click(function() {
	window.location = "<?echo active_module_url('user_reg');?>";
});
});
</script>

<section class="content">
	<!-- Modal -->

	<div id="cuDialog" class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Wajib Pajak</h4>
				</div>
				<div class="modal-body">
					<table class="table" id="table_dlg1">
						<thead>
							<tr>
								<th>index</th>
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
				<button class="btn btn-primary pull-right" id="btn_dialog_ok">OK!</button>
					<button class="btn pull-right" data-dismiss="modal" aria-hidden="true">Batal</button>
				</div>
			</div>
		</div>
	</div>



	<div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>REGISTRASI WAJIB PAJAK</strong></a>
			</li>
		</ul>
		
		<?php
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} ?>
		<p>&nbsp;</p>
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal','enctype'=>'multipart/form-data'));?>
		<input class="form-control" type="hidden" name="id" value="<?=$dt['id']?>"/>
		<div class="form-group">
			<label class="control-label col-sm-2">User ID</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="userid" id="userid" value="<?=$dt['userid']?>" readonly>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2">Nama</label>
			<div class="col-sm-3">
				<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" readonly>
			</div>
			<div class="col-sm-2">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#cuDialog">Cari</button>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2">Password</label>
			<div class="col-sm-3">
				<input class="form-control" type="password" name="passwd" value="<?=$dt['passwd']?>">
			</div>
		</div>
		<div class="form-group <?echo $this->uri->segment(2)=='users2' ? 'hide' : '';?>">
			<label class="control-label col-sm-2">Disabled</label>
			<div class="col-sm-3">
				<input type="checkbox" name="disabled" <?=$dt['disabled']?>>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-3">
				<button type="submit" class="btn btn-primary">Simpan</button>
				<button type="button" class="btn" id="btn_cancel">Batal</button>
			</div>
		</div>
	</form>
</div>
</section>
<? $this->load->view('_foot'); ?>
