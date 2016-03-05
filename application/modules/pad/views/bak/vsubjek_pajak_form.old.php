<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
	.nopd_processing {    
		--background-color: #00c;
		position: absolute;
		text-align: center;
		left: 0;
		right:0;
		margin: auto;
		font-size: 50px;
		z-index: 3;
	}

	.form-control {
		height: 24px;
		padding: 0px 5px;
		font-size: 12px;
	}

	.form-group {
		margin-bottom: 5px;
	}

	.form-horizontal 
	.control-label {
		padding-top: 3px;
	}

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

	$(window).load(function(){ 
		$('#gap_form').hide();
		$('#datapendukung').hide();
		$('#loader').hide();
	})

	function show_rpt(rpt){
		var cid = "<?=$dt['id']?>";
		var npwpd = "<?=$dt['npwpd']?>";

		var rptparams = {
			rpt: rpt,
			id: cid,
			npwpd: npwpd,
		}

		var data = decodeURIComponent($.param(rptparams));
		var url  = '<?=active_module_url($this->router->fetch_class());?>cetak/pdf/?'+data;

		var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
		window.open(url, 'Laporan', winparams);
	}

	function reload_grid() {
		oTable.fnReloadAjax("<?=active_module_url();?>subjek_pajak/grid_objek_pajak/<?=$dt['id']?>");
		$('#loader').hide();
	}	

	function get_kelurahan(kec_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>subjek_pajak/get_kelurahan/"+kec_id,
			success: function (j) {
				var data = $.parseJSON(j);
				var select = $('#kelurahan_id');

				select.html("");
				$.each(data, function(i, val){
					select.append($('<option />', { value: val['id'], text: val['nama'] }));
				});
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	}

	function get_op_kelurahan(kec_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>subjek_pajak/get_kelurahan/"+kec_id,
			success: function (j) {
				var data = $.parseJSON(j);
				var select = $('#op_kelurahan_id');

				select.html("");
				$.each(data, function(i, val){
					select.append($('<option />', { value: val['id'], text: val['nama'] }));
				});
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	}

	function get_op_pajak(usaha_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>subjek_pajak/get_op_pajak/"+usaha_id,
			success: function (j) {
				var data = $.parseJSON(j);
				var select = $('#op_pajak_id');

				select.html("");
				$.each(data, function(i, val){
					select.append($('<option />', { value: val['id'], text: val['nama'] }));
				});
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
	}

	function get_op(op_id) {
		$('#loader').show();
		$.ajax({
			url: "<?php echo active_module_url()?>subjek_pajak/get_op/"+op_id,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);

				$('#form_op')[0].reset();

				$('#op_id').val(data['id']);
				$('#op_konterid').val(data['konterid']);
				$('#op_nopd').val(data['nopd']);

				$('#op_tmt').val(data['tmt']);
				$('#op_keterangan').val(data['notes']);
				$('#aktifnotes').val(data['aktifnotes']);


				$('#op_status').html(data['status']);
				$('#op_usaha_id').html(data['usaha']);
				$('#op_kecamatan_id').html(data['kecamatan']);
				$('#op_kelurahan_id').html(data['kelurahan']);

				$('#op_pajak_id').html(data['pajak']);
				$('#op_nama').val(data['opnm']);
				$('#op_alamat').html(data['opalamat']);
				$('#op_latitude').autoNumeric('set', parseFloat(data['latitude']));
				$('#op_longitude').autoNumeric('set', parseFloat(data['longitude']));

				$('#kd_restojmlmeja').val(data['kd_restojmlmeja']);
				$('#kd_restojmlkursi').val(data['kd_restojmlkursi']);
				$('#kd_restojmltamu').val(data['kd_restojmltamu']); 
				$('#kd_filmkursi').val(data['kd_filmkursi']); 
				$('#kd_filmpertunjukan').val(data['kd_filmpertunjukan']); 
				$('#kd_filmtarif').val(data['kd_filmtarif']); 
				$('#kd_bilyarmeja').val(data['kd_bilyarmeja']); 
				$('#kd_bilyartarif').val(data['kd_bilyartarif']); 
				$('#kd_bilyarkegiatan').val(data['kd_bilyarkegiatan']); 
				$('#kd_diskopengunjung').val(data['kd_diskopengunjung']); 
				$('#kd_diskotarif').val(data['kd_diskotarif']); 

				$('#btn_op_new').hide();
				$('#btn_op_delete').show();
				$('#btn_op_submit').show();
				$('#btn_op_cancel').show();
				$('#gap_form').show();
				$('#datapendukung').show();

				var u = data['usaha_id'];
				if(u == 1) {
		            $('#kdhotel').show(); 
		            $('#kdhiburan').hide(); 
		            $('#kdmall').hide(); 
		            $('#kdrestoran').hide(); 
			        } 
		        else if(u == 2) {
		            $('#kdhotel').hide(); 
		            $('#kdhiburan').hide(); 
		            $('#kdmall').hide(); 
		            $('#kdrestoran').show();
		        }
		        else if(u == 3) {
		            $('#kdhotel').hide(); 
		            $('#kdhiburan').show(); 
		            $('#kdmall').hide(); 
		            $('#kdrestoran').hide();
		        } 
		        else if(u == 4) {
		            $('#kdhotel').hide(); 
		            $('#kdhiburan').hide(); 
		            $('#kdmall').show(); 
		            $('#kdrestoran').hide();
		        }        
		        else if(u == 5) {
		            $('#kdhotel').hide(); 
		            $('#kdhiburan').hide(); 
		            $('#kdmall').hide(); 
		            $('#kdrestoran').hide();
		        } 
		       else if(u == 6) {
		            $('#kdhotel').hide(); 
		            $('#kdhiburan').hide(); 
		            $('#kdmall').hide(); 
		            $('#kdrestoran').hide();
		        }
		       else if(u == 7) {
		            $('#kdhotel').hide(); 
		            $('#kdhiburan').hide(); 
		            $('#kdmall').hide(); 
		            $('#kdrestoran').hide();
		        }
		        else {
		            alert(u);
		        } 
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
reload_grid_konter();
setTimeout(function(){
	$('#loader').hide();
},500);
}

function new_op() {
	$('#gap_form').show();
	$('#datapendukung').show();
	$.ajax({
		url: "<?php echo active_module_url()?>subjek_pajak/new_op/",
		async: false,
		success: function (j) {
			var data = $.parseJSON(j);

			$('#form_op')[0].reset();

			$('#op_id').val('');
			$('#op_tmt').val(data['tmt']);
			$('#op_status').html(data['status']);
			$('#op_usaha_id').html(data['usaha']);
			$('#op_kecamatan_id').html(data['kecamatan']);
			$('#op_kelurahan_id').html(data['kelurahan']);

			$('#op_keterangan').html($("#op_usaha_id option:selected").text());

			$('#op_pajak_id').html(data['pajak']);
			$('#op_nama').val('');
			$('#op_alamat').html('');
			$('#op_latitude').autoNumeric('set', 0);
			$('#op_longitude').autoNumeric('set', 0);

			$('#btn_op_new').hide();
			$('#btn_op_delete').hide();
			$('#btn_op_submit').show();
			$('#btn_op_cancel').show();
		},
		error: function (xhr, desc, er) {
			alert(er);
		}
	});

}

function reload_grid_konter() {
	var konterid = document.getElementById('op_konterid').value ;
	oTableKD.fnReloadAjax("<?=active_module_url();?>subjek_pajak/grid_kd/<?=$dt['id']?>/"+konterid);
	$('#btn_save').hide();
	$('#btn_cancel').hide();
}



var opID;
var oTable;
var oTableKD;
var opRow;

$(document).ready(function() {
	oTable = $('#table1').dataTable({
	/*
	"bAutoWidth": false,
	"bPaginate": false,
	"sPaginationType": "full_numbers",
	"sScrollY": "200px",
    "bScrollCollapse": true,
	"bLengthChange": false,
	*/

	"iDisplayLength": 9,
	"bJQueryUI": true,
	"bFilter": false,
	"bInfo": false,
	"sDom": '<"toolbar">frtip',
	"aaSorting": [[ 1, "asc" ]],
	"aoColumnDefs": [
	{ "bSearchable": false, "bVisible": false, "aTargets": [0] },
	{ "bSearchable": true, "bVisible": true, "aTargets": [1] }, 
	],
	"fnRowCallback": function (nRow, aData, iDisplayIndex) {
		$(nRow).on("click", function (event) {
			if ($(this).hasClass('row_selected')) {
				opID = '';
				$('#btn_op_cancel').trigger('click');
				
				$(this).removeClass('row_selected');
			} else {
				var data = oTable.fnGetData( this );
				opID = data[0];

				/* map data */
				$('#op_id').val(opID);
				get_op(opID);
				
				oTable.$('tr.row_selected').removeClass('row_selected');
				$(this).addClass('row_selected');
			}
		})
	},
	"fnDrawCallback": function( oSettings ) {
		opRow = ''; opID = '';
		$('#btn_op_cancel').trigger('click');
	},
	"bProcessing": true,
	"bServerSide": true,
	"sAjaxSource": "<?=active_module_url();?>subjek_pajak/grid_objek_pajak/<?=$dt['id']?>"
});

/* daftar kartu data hotel - etc */
oTableKD = $('#tableKD').dataTable({
	"iDisplayLength": 99,	
	"bJQueryUI": true,
	"bFilter": false,
	"bInfo": false,
	"sDom": '<"toolbar">frtip',
	//"bProcessing": true,
	//"bServerSide": true,
	"sAjaxSource": "<? echo active_module_url('subjek_pajak/grid_kd'); ?>",
});

$('#btn_cancel').click(function() {
	window.location = '<?=active_module_url();?>subjek_pajak';
});

$('#op_latitude, #op_longitude').autoNumeric('init', {
	aSep: '.', aDec: ',', vMax: '9999.999999'
});

var reg_date_dtp = $('#reg_date').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	reg_date_dtp.hide();
}).data('datepicker');

var ijin1tgl_dtp = $('#ijin1tgl').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin1tgl_dtp.hide();
}).data('datepicker');

var ijin2tgl_dtp = $('#ijin2tgl').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin2tgl_dtp.hide();
}).data('datepicker');

var ijin3tgl_dtp = $('#ijin3tgl').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin3tgl_dtp.hide();
}).data('datepicker');

var ijin4tgl_dtp = $('#ijin4tgl').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin4tgl_dtp.hide();
}).data('datepicker');

var ijin1tglakhir_dtp = $('#ijin1tglakhir').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin1tglakhir_dtp.hide();
}).data('datepicker');

var ijin2tglakhir_dtp = $('#ijin2tglakhir').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin2tglakhir_dtp.hide();
}).data('datepicker');

var ijin3tglakhir_dtp = $('#ijin3tglakhir').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin3tglakhir_dtp.hide();
}).data('datepicker');

var ijin4tglakhir_dtp = $('#ijin4tglakhir').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	ijin4tglakhir_dtp.hide();
}).data('datepicker');

var op_tmt_dtp = $('#op_tmt').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	op_tmt_dtp.hide();
}).data('datepicker');

var kukuhtgl_dtp = $('#kukuhtgl').datepicker({
	format: 'dd-mm-yyyy'
}).on('changeDate', function(ev) {
	kukuhtgl_dtp.hide();
}).data('datepicker');


<!-- OP -->
$('#gap_form').wrap('<form id="form_op" action="<?php echo active_module_url()?>subjek_pajak/proces_op/" method="post"></form>');

$('#btn_op_submit').click(function() {
	var opnama = document.getElementById("op_nama").value.trim();
    var opalamat = document.getElementById("op_alamat").value.trim();

    if(opnama=="" || opalamat==""){
    	alert("Nama dan Alamat OP Harus diisi");
    }	
    else {
        	var mode = 'add';
			var cid  = "<?=$dt['id'];?>";
			var id   = $('#op_id').val();
			if (id != '') mode = 'edit';
			var data = JSON.stringify({
				"dtKD": oTableKD.fnGetData()
			});
			$('#loader').show();
			$('<input type="hidden" name="dtKD"/>').val(data).appendTo('#form_op');
			$.ajax({
				url: "<?php echo active_module_url()?>subjek_pajak/proces_op/"+mode,
				type: "post",
				async: false,
				data: $('#form_op').serialize()+"&customer_id="+cid,
				success: function (j) {
					$('#btn_op_new').show();
					$('#btn_op_delete').hide();
					$('#btn_op_submit').hide();
					$('#btn_op_cancel').hide();
					alert("Objek Pajak Berhasil Disimpan!");
					reload_grid();
					$('#form_op')[0].reset();
				},
				error: function (xhr, desc, er) {
					alert(er);
				}
			});
	
    }
});

$('#btn_op_delete').click(function() {
	if(opID) {
		var hapus = confirm('Hapus data ini?');
		if(hapus==true) {
			var url  = '<?=active_module_url();?>subjek_pajak/delete_op/'+opID;
			$.get(url, function(data) {
				if (data=='sip') {
					reload_grid();
					reload_grid_konter();
					$('#btn_op_cancel').trigger('click');
				} else alert ('Gagal hapus!');
			});
		};
	}else{
		alert('Silahkan pilih data yang akan dihapus');
	}
});

$('#btn_op_cancel').click(function() {
	oTable.$('tr.row_selected').removeClass('row_selected');
	$('#gap_form').hide();
	$('#datapendukung').hide();
	$('#form_op')[0].reset();
	$('#btn_op_new').show();
	$('#btn_save').show();
	$('#btn_cancel').show();
	$('#btn_op_delete').hide();
	$('#btn_op_submit').hide();
	$('#btn_op_cancel').hide();
});

$('#btn_op_new').click(function() {
	$('#kdhotel').hide(); 
    $('#kdhiburan').hide(); 
    $('#kdmall').hide(); 
    $('#kdrestoran').hide();
	$('#form_op')[0].reset();
	$('#btn_op_new').hide();
	$('#btn_op_delete').hide();
	$('#btn_save').hide();
	$('#btn_cancel').hide();
	$('#btn_op_submit').show();
	$('#btn_op_cancel').show();
	reload_grid_konter();
	new_op();
});


	$('#btn_p_cancel').click(function() {

		$('#btn_p_save').show();
		$('#btn_p_back').show();

		$('#gap_form').hide();

	});


$('#op_usaha_id').on('change', function() {
       if(this.value == 1) {
            $('#kdhotel').show(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').hide(); 
        } 
        else if(this.value == 2) {
            $('#kdhotel').hide(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').show();
        }
        else if(this.value == 3) {
            $('#kdhotel').hide(); 
            $('#kdhiburan').show(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').hide();
        } 
        else if(this.value == 4) {
            $('#kdhotel').hide(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').show(); 
            $('#kdrestoran').hide();
        }        
        else if(this.value == 5) {
            $('#kdhotel').hide(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').hide();
        } 
       else if(this.value == 6) {
            $('#kdhotel').hide(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').hide();
        }
       else if(this.value == 7) {
            $('#kdhotel').hide(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').hide();
        }
        else {
            $('#kdhotel').hide(); 
            $('#kdhiburan').hide(); 
            $('#kdmall').hide(); 
            $('#kdrestoran').hide();
        } 
});


$('#op_usaha_id').change(function() {
	/* if ($.trim($('#op_keterangan').val()) == '')  */
	if ($.trim($('#op_konterid').val()) == '') 
		$('#op_keterangan').val($("#op_usaha_id option:selected").text());
});
/* end OP */

/* control kartu data hotel */
var i =0;
$('#btn_dth_new').click( function (e) {
	e.preventDefault();

	if ($('#kdh_gol').val()=='' || $('#kdh_kamar').val()=='' || $('#kdh_tarif').val()=='' || $('#kdh_tamu').val()=='') {
		alert ('Harap isi data dengan benar!')
		return;
	};

	var gol   = $('#kdh_gol').val();
	var tarif = parseFloat($('#kdh_tarif').autoNumeric('get'));
	var kamar = parseFloat($('#kdh_kamar').autoNumeric('get'));
	var tamu  = parseFloat($('#kdh_tamu').autoNumeric('get'));

	var aiNew = oTableKD.fnAddData( [ gol, tarif, kamar, tamu,'<a class="delete" href="">Hapus</a>' ] );
	var nRow = oTableKD.fnGetNodes( aiNew[0] );

	$('#kdh_gol').val('');
	$('#kdh_tarif').val(0);
	$('#kdh_kamar').val(0);
	$('#kdh_tamu').val(0);
});

$('#tableKD').on('click', 'a.delete',function (e) {
	e.preventDefault();

	var nRow = $(this).parents('tr')[0];
	oTableKD.fnDeleteRow( nRow );
});
/* end control kartu data hotel */

$("#form_op").submit(function(eventObj){
	// if ($('#msg_confirm').hasClass('hide')==true) {
		// $('#msg_confirm').removeClass('hide');
		// location.hash = '#msg_confirm';
	// } else {
		// if ($('#submit_ok').is(":checked")) {
			var data = JSON.stringify({
				"dtKD": oTableKD.fnGetData()
			});

			$('<input type="hidden" name="dtKD"/>').val(data).appendTo('#form_op');
			// return true;
		// } else
			// alert('Silahkan setujui dokumen ini terlebih dahulu untuk melanjutkan.');
	// }
	
	return true;
});

$('#kd_restojmlmeja, #kd_restojmlkursi, #kd_restojmltamu, #kd_filmkursi, #kd_filmpertunjukan, #kd_filmtarif, #kd_bilyarmeja, #kd_bilyartarif, #kd_bilyarkegiatan, #kd_diskopengunjung, #kd_diskotarif, #kd_waletvolume, #kdh_tarif, #kdh_tamu, #kdh_kamar').autoNumeric('init', {
	aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
});

$("[id=btnshow_rpt]").click(function(){
	var rpt = $(this).data('rpt');
	show_rpt(rpt);
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
				<a href="#"><strong>PENDAFTARAN - WAJIB PAJAK </strong></a>
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
		
		<input type="hidden" name="id" class="form-control" value="<?=$dt['id']?>"/>
		<input type="hidden" class="form-control" name="rp" id="rp" value="<?=$dt['rp']?>" />

		<div class="box box-primary box-solid">
			<div class="box-header with-border">
			<h3 class="box-title">Data Wajib Pajak</u></h4>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<!-- /.COLLAPSE-->
				<div class="row">
					<div class="row col-sm-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="formno">No. Form</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="formno" id="formno" value="<?=$dt['formno']?>" readonly/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="reg_date">Tanggal Daftar</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="reg_date" id="reg_date" value="<?=$dt['reg_date']?>" required/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="pb">Pribadi/Badan</label>
							<div class="col-sm-4">
								<?echo $select_pb;?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="nama">Wajib Pajak</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="nama" id="nama" value="<?=$dt['nama']?>" required/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="alamat">Alamat</label>
							<div class="col-sm-8">
								<textarea class="form-control" name="alamat" rows="1" cols="50" required><?=$dt['alamat']?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kecamatan_id">Kecamatan</label>
							<div class="col-sm-5">
								<?echo $select_kecamatan;?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kelurahan_id">Kelurahan</label>
							<div class="col-sm-5">
								<?echo $select_kelurahan;?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kabupaten">Kabupaten/Kota</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="kabupaten" id="kabupaten" value="<?=$dt['kabupaten']?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kodepos">Kode Pos</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" maxlength="5" name="kodepos" id="kodepos" value="<?=$dt['kodepos']?>" /> 
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="telphone">Telp.</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="telphone" id="telphone" value="<?=$dt['telphone']?>" />
							</div>
						</div>
					</div>
					<div class="row col-sm-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="npwpd">NPWPD</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="npwpd" id="npwpd" value="<?=$dt['npwpd']?>" readonly />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kukuhno">No. Pengukuhan</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="kukuhno" id="kukuhno" value="<?=$dt['kukuhno']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kukuhtgl">Tgl. Pengukuhan</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="kukuhtgl" id="kukuhtgl" value="<?=$dt['kukuhtgl']?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="kukuh_jabat_id">Pejabat</label>
							<div class="col-sm-5">
								<?echo $select_kukuh_jabat;?>
							</div>
						</div>
					</div>	
				</div><!-- /.content -->
			</div><!-- /.box-body -->
		</div><!-- /.box -->

	<div class="tabbable col-sm-12">
		<ul id="myTab" class="nav nav-tabs">
			<? if ($dt['id'] > 0) : ?>
			<li class="active"><a href="#op" data-toggle="tab"><strong>Objek Pajak</strong></a></li>
			<li><a href="#pemilik" data-toggle="tab"><strong>Pemilik</strong></a></li>
		<? else :?>
		<li class="active"><a href="#pemilik" data-toggle="tab"><strong>Pemilik</strong></a></li>
	<? endif;?>
	<li><a href="#pengelola" data-toggle="tab"><strong>Pengelola</strong></a></li>
	<li><a href="#perizinan" data-toggle="tab"><strong>Perizinan</strong></a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane <?echo $dt['id'] > 0 ? '' : 'active'; ?>" id="pemilik">
	<p>&nbsp;</p>
	<div class="box box-primary box-solid">
	<div class="box-body">

		<div class="form-group">
			<label class="control-label col-sm-2" for="wpnama">Nama</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="wpnama" id="wpnama" value="<?=$dt['wpnama']?>" required/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="wpalamat">Alamat</label>
			<div class="col-sm-4">
				<textarea class="form-control" name="wpalamat" rows="1" cols="50" required><?=$dt['wpalamat']?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="wpkecamatan">Kecamatan</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="wpkecamatan" id="wpkecamatan" value="<?=$dt['wpkecamatan']?>" required/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="wpkelurahan">Kelurahan</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="wpkelurahan" id="wpkelurahan" value="<?=$dt['wpkelurahan']?>" required/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="wpkabupaten">Kabupaten/Kota</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="wpkabupaten" id="wpkabupaten" value="<?=$dt['wpkabupaten']?>" required/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="wpkodepos">Kode Pos</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="wpkodepos" id="wpkodepos" maxlength="5" value="<?=$dt['wpkodepos']?>" />  
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="wptelp">Telp</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="wptelp" id="wptelp" value="<?=$dt['wptelp']?>" />
			</div>
		</div>
		</div>
		</div>
	</div>

	<div class="tab-pane " id="pengelola">
	<p>&nbsp;</p>
	<div class="box box-primary box-solid">
	<div class="box-body">

		<div class="form-group">
			<label class="control-label col-sm-2" for="pnama">Nama</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pnama" id="pnama" value="<?=$dt['pnama']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="palamat">Alamat</label>
			<div class="col-sm-4">
				<textarea class="form-control" name="palamat" rows="1" cols="50"><?=$dt['palamat']?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="pkecamatan">Kecamatan</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pkecamatan" id="pkecamatan" value="<?=$dt['pkecamatan']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="pkelurahan">Kelurahan</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pkelurahan" id="pkelurahan" value="<?=$dt['pkelurahan']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="pkabupaten">Kabupaten/Kota</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pkabupaten" id="pkabupaten" value="<?=$dt['pkabupaten']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="pkodepos">Kode Pos</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pkodepos" id="pkodepos" maxlength="5" value="<?=$dt['pkodepos']?>" />
			</div>
		</div>	
		<div class="form-group">
			<label class="control-label col-sm-2" for="ptelp">Telp. </label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="ptelp" id="ptelp" value="<?=$dt['ptelp']?>" />
			</div>
		</div>
	</div>
	</div>
	</div>

	<div class="tab-pane " id="perizinan">
	<p>&nbsp;</p>
	<div class="box box-primary box-solid">
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-4">
						<strong>Perizinan</strong>
						<input class="form-control" type="text" name="ijin1" value="<?=$dt['ijin1']?>" />
					</div>
					<div class="col-sm-3">
						<strong>No. Perizinan</strong>
						<input class="form-control" type="text" name="ijin1no" value="<?=$dt['ijin1no']?>" />
					</div>
					<div class="col-sm-2">
						<strong>Tanggal Berlaku</strong>
						<input class="form-control" type="text" name="ijin1tgl" id="ijin1tgl" value="<?=$dt['ijin1tgl']?>" />
					</div>
					<div class="col-sm-2">
						<strong>Tanggal Berakhir</strong>
						<input class="form-control" type="text" name="ijin1tglakhir" id="ijin1tglakhir" value="<?=$dt['ijin1tglakhir']?>" />
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<input class="form-control" type="text" name="ijin2" value="<?=$dt['ijin2']?>" />
					</div>
					<div class="col-sm-3">
						<input class="form-control" type="text" name="ijin2no" value="<?=$dt['ijin2no']?>" />
					</div>
					<div class="col-sm-2">
						<input class="form-control" type="text" name="ijin2tgl" id="ijin2tgl" value="<?=$dt['ijin2tgl']?>" />
					</div>
					<div class="col-sm-2">
						<input class="form-control" type="text" name="ijin2tglakhir" id="ijin2tglakhir" value="<?=$dt['ijin2tglakhir']?>" />
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<input class="form-control" type="text" name="ijin3" value="<?=$dt['ijin3']?>" />
					</div>
					<div class="col-sm-3">
						<input class="form-control" type="text" name="ijin3no" value="<?=$dt['ijin3no']?>" />
					</div>
					<div class="col-sm-2">
						<input class="form-control" type="text" name="ijin3tgl" id="ijin3tgl" value="<?=$dt['ijin3tgl']?>" />
					</div>
					<div class="col-sm-2">
						<input class="form-control" type="text" name="ijin3tglakhir" id="ijin3tglakhir" value="<?=$dt['ijin3tglakhir']?>" />
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<input class="form-control" type="text" name="ijin4" value="<?=$dt['ijin4']?>" />
					</div>
					<div class="col-sm-3">
						<input class="form-control" type="text" name="ijin4no" value="<?=$dt['ijin4no']?>" />
					</div>
					<div class="col-sm-2">
						<input class="form-control" type="text" name="ijin4tgl" id="ijin4tgl" value="<?=$dt['ijin4tgl']?>" />
					</div>
					<div class="col-sm-2">
						<input class="form-control" type="text" name="ijin4tglakhir" id="ijin4tglakhir" value="<?=$dt['ijin4tglakhir']?>" />
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	</div>

	<? if ($dt['id'] > 0) : ?>
	<div class="tab-pane <?echo $dt['id'] > 0 ? 'active' : ''; ?>" id="op"><br>

	<div class="box box-primary box-solid">
			<div class="box-header with-border">
			<h3 class="box-title">Data Objek Pajak</u></h4>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<!-- /.COLLAPSE-->
			<div class="row">
     <div id="gap_form">			
		<div class="col-sm-6">
				<input type="hidden" name="op_id" id="op_id" />

				<div class="form-group hide">
					<label class="control-label col-sm-4" for="op_konterid">Nomor</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="op_konterid" id="op_konterid" readonly />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_nopd">NOPD</label>
					<div class="col-sm-6">
						<input class="form-control" type="text" name="op_nopd" id="op_nopd" readonly />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_nama">Nama Konter</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="op_nama" id="op_nama" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_usaha_id">Jenis Usaha</label>
					<div class="col-sm-5">
						<?echo $select_op_usaha;?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_pajak_id">Jenis Pajak</label>
					<div class="col-sm-5">
						<?echo $select_op_pajak;?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_kecamatan_id">Kecamatan</label>
					<div class="col-sm-5">
						<?echo $select_op_kecamatan;?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_kelurahan_id">Kelurahan</label>
					<div class="col-sm-5">
						<?echo $select_op_kelurahan;?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_alamat">Alamat</label>
					<div class="col-sm-7">
						<textarea class="form-control" name="op_alamat" id="op_alamat" rows="1" cols="50"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_keterangan">Keterangan</label>
					<div class="col-sm-5">
						<textarea class="form-control" name="op_keterangan" id="op_keterangan" rows="1" cols="50"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_latitude">Kordinat Peta</label>
					<div class="col-sm-3">
						<input class="form-control" type="text" name="op_latitude" id="op_latitude" />, 
					</div>	
					<div class="col-sm-3">
						<input class="form-control" type="text" name="op_longitude" id="op_longitude" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_status">Status</label>
					<div class="col-sm-5">
						<?echo $select_op_status;?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="aktifnotes">Catatan</label>
					<div class="col-sm-5">
						<textarea class="form-control" type="text" name="aktifnotes" id="aktifnotes" rows="1" cols="50" ></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="op_status_tmt">Status TMT</label>
					<div class="col-sm-5">
						<input class="form-control" type="text" name="op_tmt" id="op_tmt" required />
					</div>
				</div>
		</div>
		<div class="col-sm-6" id="datapendukung">
		<!-- data pendukung --> 
			<!-- restoran -->
			<div class="" id="kdrestoran">
			<div class="row"> 
				<div class="col-sm-12">
				<h4><u>DATA RESTORAN</u></h4>
				<div class="form-group">
					<label class="control-label col-sm-4">Jumlah Meja</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="kd_restojmlmeja" id="kd_restojmlmeja" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Jumlah Kursi</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="kd_restojmlkursi" id="kd_restojmlkursi" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Jumlah Tamu</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" style="text-align:right;" name="kd_restojmltamu" id="kd_restojmltamu" />
					</div>
				</div>
			</div>
			</div>
		</div>	
			<!-- hiburan -->
			<div class="" id="kdhiburan">
				<div class="row"> 
					<div class="col-sm-12">
					<h4><u>DATA HIBURAN</u></h4>
						<div class="form-group">
							<label class="control-label col-sm-8" style="color:#3C8DBC"><u>Pertunjukan</u></label>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Jumlah Kursi</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_filmkursi" id="kd_filmkursi" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Jumlah Pertunjukan</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_filmpertunjukan" id="kd_filmpertunjukan" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Tarif Masuk</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_filmtarif" id="kd_filmtarif" />
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label class="control-label col-sm-8" style="color:#3C8DBC"><u>Bilyard </u></label>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Jumlah Meja</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_bilyarmeja" id="kd_bilyarmeja" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Tarif</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_bilyartarif" id="kd_bilyartarif" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Rata-rata Kegiatan</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_bilyarkegiatan" id="kd_bilyarkegiatan" />
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label class="control-label col-sm-8" style="color:#3C8DBC"><u>Diskotik, Club, dan Pertandingan </u></label>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Pengunjung</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_diskopengunjung" id="kd_diskopengunjung"  />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Tarif</label>
							<div class="col-sm-4">
								<input class="form-control" type="text" style="text-align:right;" name="kd_diskotarif" id="kd_diskotarif" />
							</div>
						</div>
					</div>
				</div>
			</div> <!-- end Hiburan -->

						<!-- Mall -->
			<div class="" id="kdmall">
			<h4><u>DATA MALL</u></h4>
				<div class="form-group">
					<label class="control-label col-sm-4">Mall</label>
					<div class="col-sm-4">
						<?echo $select_op_mall;?>
					</div>
				</div>
			</div>

			<div class="" id="kdhotel">
				<div class="row">
					<div class="col-sm-12">
					<h4><u>Data Hotel, Panti Pijat, Mandi Uap, Karaoke</u></h4>
						<div id="nopd_processing" class="nopd_processing" style="display: none;">
							<img border="0" src="<?=base_url('assets/pad/img/ajax-loader-bert.gif')?>"></img>
						</div>
						<div id="gap_form">
							<div class="form-group">
								<label class="control-label col-sm-4">Golongan/Kelas</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" name="kdh_gol" id="kdh_gol" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-4">Tarif</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" style="text-align:right;" name="kdh_tarif" id="kdh_tarif" value=0 />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-4">Jumlah Kamar</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" style="text-align:right;" name="kdh_kamar" id="kdh_kamar" value=0 />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-4">Tamu per Hari</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" style="text-align:right;" name="kdh_tamu" id="kdh_tamu" value=0 />
								</div>
							</div>
							<p>&nbsp;</p>
							<button type="button" class="btn btn-success" id="btn_dth_new">Tambahkan ke Daftar</button>
						</div>
					</div>
					<div class="col-sm-12">
						<table class="table" id="tableKD">
							<thead>
								<tr>
									<th>Golongan/Kelas</th>
									<th>Tarif</th>
									<th>Kamar</th>
									<th>Tamu</th>
									<th>Batal</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

				</div>
			</div>
            
		</div>  <!-- end data pendukung -->

		</div>
	</div>




	 	
	</div>
  </div>	
</div>

<p>&nbsp;</p>
			<button type="button" class="btn btn-success" id="btn_op_new">Tambah Objek Pajak</button>
			<button type="button" class="btn btn-primary" id="btn_op_submit">Simpan Objek Pajak</button>
			<button type="button" class="btn btn-danger" id="btn_op_delete">Hapus</button>
			<button type="button" class="btn" id="btn_op_cancel">Batal</button>
			<? if ($dt['id'] > 0) : ?>
			<button type="button" class="btn btn-primary pull-right" id="btnshow_rpt" data-rpt="wpkartu" >Cetak Kartu</button>  
			<button type="button" class="btn btn-primary pull-right" id="btnshow_rpt" data-rpt="wpkukuh" >Cetak Pengukuhan</button>
			<? endif; ?>


			<div class="span4">
				<table class="table" id="table1">

                <div id="loader" class="nopd_processing">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
					<thead>
						<tr>
							<th>Index</th>
							<th>No.</th>
							<th>Keterangan</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<? endif;?>

		<div class="content">
		<p>&nbsp;</p>	
		<button type="submit" class="btn btn-primary" id="btn_save">Simpan</button>
		<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
		</div>	
</div>

</div>



		<!--
		<div class="form-group">
			<label class="control-label col-sm-2" for="rp">Pajak/Retribusi</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="rp" id="rp" value="<?=$dt['rp']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="parent">parent</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="parent" id="parent" value="<?=$dt['parent']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="customer_status_id">customer_status_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="customer_status_id" id="customer_status_id" value="<?=$dt['customer_status_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kirimtgl">kirimtgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kirimtgl" id="kirimtgl" value="<?=$dt['kirimtgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="batastgl">batastgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="batastgl" id="batastgl" value="<?=$dt['batastgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="penerimanm">penerimanm</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="penerimanm" id="penerimanm" value="<?=$dt['penerimanm']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="penerimaalamat">penerimaalamat</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="penerimaalamat" id="penerimaalamat" value="<?=$dt['penerimaalamat']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="penerimatgl">penerimatgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="penerimatgl" id="penerimatgl" value="<?=$dt['penerimatgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kembalitgl">kembalitgl</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kembalitgl" id="kembalitgl" value="<?=$dt['kembalitgl']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kembalioleh">kembalioleh</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kembalioleh" id="kembalioleh" value="<?=$dt['kembalioleh']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kukuhprinted">kukuhprinted</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kukuhprinted" id="kukuhprinted" value="<?=$dt['kukuhprinted']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kartuprinted">kartuprinted</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kartuprinted" id="kartuprinted" value="<?=$dt['kartuprinted']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id">id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="id" id="id" value="<?=$dt['id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="tmt">tmt</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="enabled">enabled</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="enabled" id="enabled" value="<?=$dt['enabled']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="create_date">create_date</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="create_date" id="create_date" value="<?=$dt['create_date']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="create_uid">create_uid</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="create_uid" id="create_uid" value="<?=$dt['create_uid']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="write_date">write_date</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="write_date" id="write_date" value="<?=$dt['write_date']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="write_uid">write_uid</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="write_uid" id="write_uid" value="<?=$dt['write_uid']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kukuhnip">kukuhnip</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kukuhnip" id="kukuhnip" value="<?=$dt['kukuhnip']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="kembalinip">kembalinip</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="kembalinip" id="kembalinip" value="<?=$dt['kembalinip']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="catatnip">catatnip</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="catatnip" id="catatnip" value="<?=$dt['catatnip']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="petugas_jabat_id">petugas_jabat_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="petugas_jabat_id" id="petugas_jabat_id" value="<?=$dt['petugas_jabat_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="pencatat_jabat_id">pencatat_jabat_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="pencatat_jabat_id" id="pencatat_jabat_id" value="<?=$dt['pencatat_jabat_id']?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="reg_kelurahan_id">reg_kelurahan_id</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="reg_kelurahan_id" id="reg_kelurahan_id" value="<?=$dt['reg_kelurahan_id']?>" />
			</div>
		</div>

	-->


	<?php echo form_close();?>
</div>
</div>
<? $this->load->view('_foot'); ?>
