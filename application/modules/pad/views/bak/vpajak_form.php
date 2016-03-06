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

var tarifID;
var oTable;


$(window).load(function(){ 
		$('#gap_form').hide();
 })

$(document).ready(function() {
	oTable = $('#table1').dataTable({
		"iDisplayLength": 9,
		"bJQueryUI": true,
		"bFilter": false,
		"bInfo": false,
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 1, "asc" ]],
		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [0] },
			{ "bSearchable": false, "bVisible": false, "aTargets": [1] }, 
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					tarifID = '';
					$('#btn_p_cancel').trigger('click');
					
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					tarifID = data[0];

					/* map data */
					$('#p_id').val(tarifID);
					get_tarif(tarifID);
					
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			opRow = ''; tarifID = '';
			$('#btn_p_cancel').trigger('click');
		},
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=active_module_url();?>pajak/grid_tarif/<?=$dt['id']?>"
	});
	
	$('#btn_p_back').click(function() {
		window.location = '<?=active_module_url();?>pajak';
	});
	
	var tmt_dtp = $('#tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tmt_dtp.hide();
	}).data('datepicker');
	
	
	<!-- tarif -->
	function reload_grid_tarif() {
		oTable.fnReloadAjax("<?=active_module_url();?>pajak/grid_tarif/<?=$dt['id']?>");
	}

	//Get Data Tarif
	function get_tarif(pajak_id) {
		$('#gap_form').show();
		$('#tarif_processing').show();
		$.ajax({
			url: "<?php echo active_module_url()?>pajak/get_tarif/"+pajak_id,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);

				$('#form_tarif')[0].reset();

				$('#tarif_id').val(data['id']);
				$('#p_tarif').autoNumeric('set', parseFloat(data['tarif']));
				//$('#p_tarif_percent').autoNumeric('set', data['tarif']);
				$('#p_minimal_omset').autoNumeric('set', parseFloat(data['minimal_omset']));
				$('#p_reklame').autoNumeric('set', parseFloat(data['reklame']));

				$('#p_tarif_percent').val(data['tarif']*100);

				p_tmt_dtp.setValue(data['tmt']);
				p_tmt_dtp.update(); 
				if(parseInt(data['enabled'])==1)				
					$('#p_enabled').attr('checked','checked');				
				else
					$('#p_enabled').removeAttr('checked');				
				
				$('#btn_p_new').hide();
				$('#btn_p_delete').show();
				$('#btn_p_submit').show();
				$('#btn_p_cancel').show();
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		setTimeout(function(){
			$('#tarif_processing').hide();
		},500);
	}
	
	$('#gap_form').wrap('<form id="form_tarif" action="<?php echo active_module_url()?>pajak/proces_tarif/" method="post"></form>');
	
	$('#btn_p_submit').click(function() {
		var mode = 'add';
		var pid  = "<?=$dt['id'];?>";
		var id   = $('#tarif_id').val();
		if (id !== '') mode = 'edit';

		$.ajax({
			url: "<?php echo active_module_url()?>pajak/proces_tarif/"+mode,
			type: "post",
			async: false,
			data: $('#form_tarif').serialize()+"&pajak_id="+pid,
			success: function (j) {
				$('#btn_p_new').show();
				$('#btn_p_delete').hide();
				$('#btn_p_submit').hide();
				$('#btn_p_cancel').hide();

				reload_grid_tarif();
				$('#form_tarif')[0].reset();
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		$('#gap_form').hide();
	});

	$('#btn_p_delete').click(function() {
		if(tarifID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				var url  = '<?=active_module_url();?>pajak/delete_tarif/'+tarifID;
				$.get(url, function(data) {
					if (data=='sip') {
						reload_grid_tarif();
						$('#btn_p_cancel').trigger('click');
					} else alert ('Gagal hapus!');
				});
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
	
	$('#btn_p_cancel').click(function() {
		oTable.$('tr.row_selected').removeClass('row_selected');
		$('#form_tarif')[0].reset();
		$('#btn_p_new').show();
		$('#btn_p_delete').hide();
		$('#btn_p_submit').hide();
		$('#btn_p_cancel').hide();
	});

	$('#btn_p_new').click(function() {
		$('#form_tarif')[0].reset();
		$('#tarif_id').val('');
		$('#p_tarif').autoNumeric('set', 0);
		$('#p_minimal_omset').autoNumeric('set', 0);
		$('#p_reklame').autoNumeric('set', 0);
		$('#p_tarif_percent').val(0);


		$('#btn_p_new').hide();
		$('#btn_p_delete').hide();
		$('#btn_p_submit').show();
		$('#btn_p_cancel').show();

		$('#gap_form').show();
		$('#btn_p_save').hide();
		$('#btn_p_back').hide();

	});

	$('#btn_p_cancel').click(function() {

		$('#btn_p_save').show();
		$('#btn_p_back').show();

		$('#gap_form').hide();

	});

	$('#p_tarif_percent').keyup(function() {
	var p_tarif_percent = $('#p_tarif_percent').val();

	//var p_tarif_percent = parseFloat($('#p_tarif_percent').val());
	//document.getElementById('p_tarif').value = p_tarif_percent/100;
	$('#p_tarif').autoNumeric('set', parseFloat(p_tarif_percent/100));
	});

	var p_tmt_dtp = $('#p_tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		p_tmt_dtp.hide();
	}).data('datepicker');
	
	$('#p_reklame, #p_minimal_omset').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
	});
	$('#p_tarif').autoNumeric('init', {aSep: '.', aDec: ',', vMax: '999999999999.99'});
	$('#p_tarif_percent').autoNumeric('init', {vMin: '0', vMax: '100'});

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
				<a href="#"><strong>JENIS PAJAK</strong></a>
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
		<br>
		<div class="box box-primary box-solid">
		<div class="box-body">

		<?php echo form_open($faction, array('id'=>'myform','class'=>'form-horizontal'));?>
			<input class="form-control" type="hidden" name="id" value="<?=$dt['id']?>"/>
			
			<div class="row">
					<div class="form-group">
						<label class="control-label col-sm-2" for="usaha_id">Jenis Usaha</label>
						<div class="col-sm-4">
							<?=$select_usaha;?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="nama">Pajak</label>
						<div class="col-sm-4">
							<input class="form-control" class="input-xlarge" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="rekening_id">Rekening</label>
						<div class="col-sm-4">
							<?=$select_rekening;?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="rekdenda_id">Rek. Denda</label>
						<div class="col-sm-4">
							<?=$select_rekdenda;?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="rekening_kd_sub">Sub Kode Rek.</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" name="rekening_kd_sub" id="rekening_kd_sub" value="<?=$dt['rekening_kd_sub']?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="jatuhtempo">Jatuh Tempo</label>
						<div class="col-sm-4">
							<?=$select_jatuhtempo;?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="masapajak">Masa Pajak</label>
						<div class="col-sm-4">
							<?=$select_masapajak;?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="multiple">Multiple</label>
						<div class="col-sm-4">
							<input type="checkbox" name="multiple" <?=$dt['multiple']?>>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="enabled">Aktif</label>
						<div class="col-sm-4">
							<input type="checkbox" name="enabled" <?=$dt['enabled']?>>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="tmt">TMT</label>
						<div class="col-sm-2">
							<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>" />
						</div>
					</div>
			</div>
			</div>
			</div>
			<? if ($dt['id'] > 0) : ?>
					
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#"><strong>Tarif Pajak<strong></a>
				</li>
			</ul>
			
			<div class="row">
					<div id="gap_form" class="col-sm-6">
					<br>
						<div id="tarif_processing" class="tarif_processing" style="display: none;">
							<img border="0" src="<?=base_url('assets/pad/img/ajax-loader-bert.gif')?>"></img>
						</div>
						<input class="form-control" type="hidden" name="tarif_id" id="tarif_id" />

						<div class="form-group col-sm-12">
							<label class="control-label col-sm-4" for="p_tarif_percent">Tarif (%)</label>
							<div class="col-sm-6">
								<input class="form-control" style="text-align:right;" type="text" name="p_tarif_percent" id="p_tarif_percent" required />
							</div>
						</div>

						<div class="form-group col-sm-12 hide">
							<label class="control-label col-sm-4" for="p_tarif">Tarif (Decimal)</label>
							<div class="col-sm-6">
								<input class="form-control" style="text-align:right;" type="text" name="p_tarif" id="p_tarif" required />
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label class="control-label col-sm-4" for="p_minimal_omset">Min. Omset (Rp.)</label>
							<div class="col-sm-6">
								<input class="form-control" style="text-align:right;" type="text" name="p_minimal_omset" id="p_minimal_omset" />
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label class="control-label col-sm-4" for="p_reklame">NS.Reklame (Rp.)</label>
							<div class="col-sm-6">
								<input class="form-control" style="text-align:right;"  type="text" name="p_reklame" id="p_reklame" />
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label class="control-label col-sm-4" for="p_tmt">TMT</label>
							<div class="col-sm-6">
								<input class="form-control"  style="text-align:right;" type="text" name="p_tmt" id="p_tmt" />
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label class="control-label col-sm-4" for="p_enabled">Aktif</label>
							<div class="col-sm-6">
								<input type="checkbox" name="p_enabled" id="p_enabled">
							</div>
						</div>
						<button type="button" class="btn btn-primary" id="btn_p_submit">Simpan</button>
						<button type="button" class="btn btn-danger" id="btn_p_delete">Hapus</button>
						<button type="button" class="btn " id="btn_p_cancel">Batal</button>
					</div>
					<br>
					<button type="button" class="btn btn-primary" id="btn_p_new">Tambah Tarif Pajak</button>
				<div class="span6">
					<table class="table" id="table1">
						<thead>
							<tr>
								<th>Index</th>
								<th>Tarif</th>
								<th>Tarif (%)</th>
								<th>Min. Omset</th>
								<th>NS.Reklame</th>
								<th>TMT</th>
								<th>Aktif</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<? endif;?>
			
			<hr />
			<button type="submit" id="btn_p_save" class="btn btn-primary">Simpan</button>
			<button type="button" id="btn_p_back" class="btn" id="btn_cancel">Batal / Kembali</button>
		<?php echo form_close();?>
    </div>
</div>
<? $this->load->view('_foot'); ?>