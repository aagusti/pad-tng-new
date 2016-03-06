<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
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

		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = ''; levelID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					jenis_pajak = data[5];
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			mID = ''; levelID = '';
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
		"sAjaxSource": "<?=active_module_url('pajak/grid_reklame');?>"
	});
	var tb_array = [
		'</br>'
	];

	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>reklame_media';
	});
	
	$('#nilai').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99', mDec: '0'
	});

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			//document.getElementById('jenis_pajak_id').value = 33;
			document.getElementById('jenis_pajak_id').value = mID;
			document.getElementById('jenis_pajak').value    = jenis_pajak;
			$('#cuDialog').modal('hide');
		}
	});
});

$(document).keypress(function(event){
	if (event.which == '13') {
		event.preventDefault();
	}
});
</script>

<div class="content">
    <div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>MEDIA REKLAME</strong></a>
			</li>
		</ul>
		
		<? 
		echo msg_block();
		if(validation_errors()){
			echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
			echo validation_errors('<small>','</small>');
			echo '</blockquote>';
		} 
		?>
				<!-- Modal -->
		<div id="cuDialog" class="modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">JENIS PAJAK</h4>
					</div>
					<div class="modal-body">
						<table class="table" id="table_dlg1">
							<thead>
								<tr>
									<th>index</th>
									<th>Usaha</th>
									<th>Rek. Kode</th>
									<th>Sub. Kode</th>
									<th>Rekening</th>
									<th>Pajak</th>
									<th>TMT</th>
									<th>Aktif</th>
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
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<input type="hidden" name="jenis_pajak_id" id="jenis_pajak_id" value="<?=$dt['jenis_pajak_id']?>"/>

			<div class="form-group">
				<label class="control-label col-sm-1" for="jenis_pajak">Jenis Pajak</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="jenis_pajak" id="jenis_pajak" value="<?=$dt['jenis_pajak']?>" required readonly />
				</div>
				<div class="col-sm-1">	
					<button type="button" class="btn btn-success" data-toggle="modal" data-target="#cuDialog">Cari</button>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="media">Media</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="media" id="media" value="<?=$dt['media']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="singkatan">Singkatan</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="singkatan" id="singkatan" maxlength="15" value="<?=$dt['singkatan']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="masa">Masa</label>
				<div class="col-sm-4">
					<?=$select_masa;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="keterangan">Keterangan</label>
				<div class="col-sm-4">
					<?=$select_keterangan;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="enabled">Aktif</label>
				<div class="col-sm-4">
					<input type="checkbox" name="enabled" <?=$dt['enabled']?>>
				</div>
			</div>
			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>