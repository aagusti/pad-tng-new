<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>
var mID;
var levelID;
var induk_id;


$(window).load(function(){ 
	$('#btn_filter_induk2').hide();
 });

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
					kelompok_usaha = data[3];
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
		"sAjaxSource": "<?=active_module_url('air_kelompok_usaha/grid_for_hda');?>"
	});

	var tb_array = [
		'<div class="btn-group pull-left">',
		'</div></br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);


	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>air_hda';
	});


	$('#btn_filter_back').click(function() {;
        reload_grid_start();
        $('#btn_filter_induk2').hide();
        $('#btn_filter_induk').show();
	});


	$('#hda, #volume').autoNumeric('init', {aSep: '.', aDec: ',', vMax: '999999999999.99'});
	
	var tmt_dtp = $('#tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tmt_dtp.hide();
	}).data('datepicker');

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			//document.getElementById('jenis_pajak_id').value = 33;
			document.getElementById('kelompok_usaha_id').value = mID;
			document.getElementById('kelompok_usaha').value = kelompok_usaha;
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
				<a href="#"><strong>HARGA DASAR AIR</strong></a>
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
		
		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?><br>
				<!-- Modal -->
		<div id="cuDialog" class="modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">KELOMPOK USAHA</h4>
					</div>
					<div class="modal-body">
					<table class="table" id="table_dlg1">
						<thead>
							<tr>
								<th>index</th>
								<th>Kelompok 1</th>
								<th>Kelompok 2</th>
								<th>Kelompok 3</th>
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

			<br>

			<input type="hidden" name="id" value="<?=$dt['id']?>"/>
			<div class="form-group">
				<label class="control-label col-sm-1" for="manfaatnm">Manfaat</label>
				<div class="col-sm-4">
					<?=$select_air_manfaat;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="golongan">Golongan</label>
				<div class="col-sm-4">
					<select class="form-control" id="golongan" name="golongan">
						<option value=1>ABT</option>
						<option value=2>AP</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="zonanm">Zona</label>
				<div class="col-sm-4">
					<?=$select_air_zona;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="volume">Kelompok Usaha</label>
				<div class="col-sm-4">
					<input class="form-control hide" type="text" name="kelompok_usaha_id" id="kelompok_usaha_id" value="<?=$dt['kelompok_usaha_id']?>"/>
					<input class="form-control" type="text" name="kelompok_usaha" id="kelompok_usaha" required value="<?=$dt['kelompok_usaha']?>" readonly/>
				</div>
				<div class="col-sm-1">	
					<button type="button" class="btn btn-success" data-toggle="modal" data-target="#cuDialog">Cari</button>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="volume">Volume</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="volume" id="volume" value="<?=$dt['volume']?>" required />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-1" for="hda">HDA</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="hda" id="hda" value="<?=$dt['hda']?>" required />
				</div>
			</div>


			<hr />
			<button type="submit" class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>