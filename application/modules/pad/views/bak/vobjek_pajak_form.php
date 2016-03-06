<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style type="text/css">
.form-control {
  height: 26px;
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

	$(window).load(function(){
	$('#kdhotel').hide(); 
    $('#kdhiburan').hide(); 
    $('#kdmall').hide(); 
    $('#kdrestoran').hide();
	});

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

function get_pajak(usaha_id) {
	$.ajax({
		url: "<?php echo active_module_url()?>subjek_pajak/get_op_pajak/"+usaha_id,
		success: function (j) {
			var data = $.parseJSON(j);
			var select = $('#def_pajak_id');

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


var mID;
var oTable;
var oTableKD;


$(document).ready(function() {

	function get_c_id(customer_id) {
		$.ajax({
			url: "<?php echo active_module_url()?>objek_pajak/get_c_id/"+customer_id,
			async: false,
			success: function (j) {
				var data = $.parseJSON(j);
				$("#npwpd").val(data['npwpd']);
				$("#customer_id").val(data['customer_id']);
				$("#customernm").val(data['customernm']);
			},
			error: function (xhr, desc, er) {
				alert(er);
			}
		});
		reload_grid_konter(customer_id);
	}
	/* daftar kartu data hotel - etc */
oTableKD = $('#tableKD').dataTable({
	"iDisplayLength": 99,	
	"bJQueryUI": true,
	"bFilter": false,
	"bInfo": false,
	"sDom": '<"toolbar">frtip',
	//"bProcessing": true,
	//"bServerSide": true,
	"sAjaxSource": "<? echo active_module_url('objek_pajak/grid_kd/'); ?>",
});

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

function saveKD(){
	// if ($('#msg_confirm').hasClass('hide')==true) {
		// $('#msg_confirm').removeClass('hide');
		// location.hash = '#msg_confirm';
	// } else {
		// if ($('#submit_ok').is(":checked")) {
			var data = JSON.stringify({
				"dtKD": oTableKD.fnGetData()
			});

			$('<input type="hidden" name="dtKD"/>').val(data).appendTo('#myform');
			// return true;
		// } else
			// alert('Silahkan setujui dokumen ini terlebih dahulu untuk melanjutkan.');
	// }
	
	return true;
}

function reload_grid_konter(customer_id) {
	oTableKD.fnReloadAjax("<?=active_module_url();?>objek_pajak/grid_kd/"+customer_id+"/"+"<?=$dt['konterid'];?>");
}

$('#kd_restojmlmeja, #kd_restojmlkursi, #kd_restojmltamu, #kd_filmkursi, #kd_filmpertunjukan, #kd_filmtarif, #kd_bilyarmeja, #kd_bilyartarif, #kd_bilyarkegiatan, #kd_diskopengunjung, #kd_diskotarif, #kd_waletvolume, #kdh_tarif, #kdh_tamu, #kdh_kamar').autoNumeric('init', {
	aSep: '.', aDec: ',', vMax: '999999999999.99',  mDec: '0'
});


	
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

	$('#btn_cancel').click(function() {
		window.location = '<?=active_module_url();?>objek_pajak';
	});

	$('#btn_dialog_ok').click(function() {
		if (mID == '' || mID == null)
			alert('Data belum dipilih.');
		else {
			get_c_id(mID);		
			$('#cuDialog').modal('hide');
		}
	});

	var tmt_dtp = $('#tmt').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		tmt_dtp.hide();
	}).data('datepicker');
	
	$('#usaha_id').change(function() {
		/* if ($.trim($('#notes').val()) == '')  */
		if ($.trim($('#konterid').val()) == '') 
			$('#notes').val($("#usaha_id option:selected").text());
	});
	
	$('#npwpd').typeahead({
		source: function(query, process){
			/* 
			return $.get('<?=active_module_url('objek_pajak/get_typeahead_npwpd');?>', {q: query}, function(response) { 
			*/
			$.getJSON('<?=active_module_url('objek_pajak/get_typeahead_npwpd');?>'+query, function(response) {
				var data = new Array;
				for(var i in response) {
					data.push(response[i]['id'] +"#"+ response[i]['npwpd'] +"#"+ response[i]['nama']);
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
			get_c_id(parts[0]);
			
			return parts[1];
		},
	});
		 
	$('#customernm').typeahead({
		source: function(query, process){
			$.getJSON('<?=active_module_url('objek_pajak/get_typeahead_csname');?>'+query, function(response) {
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
				customernm = parts[2].replace(regex, "<strong>$1</strong>"),
				html = '<div class="typeahead">';
				
			html += '<div class="left">';
			html += '<div>'+customernm+'</div>';
			html += '<div>'+parts[1]+'</div>';
			html += '</div>';
			html += '<div class="clear"></div>';
			html += '</div>';
			return html;
		},
		updater: function(item) {
			var parts = item.split('#');
			get_c_id(parts[0]);			
			return parts[2];
		},
	});
	
	$('#usaha_id').on('change', function() {
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

	
	/* init page */
	$('#latitude, #longitude').autoNumeric('init', {
		aSep: '.', aDec: ',', vMax: '9999.999999'
	});
    
	var cid = parseInt(<?=$dt['customer_id']?>);
	if (!isNaN(cid)) {
		get_c_id(<?=$dt['customer_id']?>);
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
				<a href="#"><strong>PENDAFTARAN - OBJEK PAJAK</strong></a>
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
			<input type="hidden" name="customer_id" id="customer_id" class="form-control" value="<?=$dt['id']?>"/>
			<input type="hidden" name="konterid" id="konterid" class="form-control" value="<?=$dt['konterid'];?>"/>
			<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label col-sm-3" for="nopd">NOPD</label>
				<div class="col-sm-6">
					<input class="form-control" type="text" name="nopd" id="nopd" value="<?=$dt['nopd'];?>" readonly />
				</div>
			</div>
						
			<div class="form-group">
				<label class="control-label col-sm-3" for="npwpd">Subjek Pajak</label>
				<div class="col-sm-8">
					<input class="form-control" type="text" name="npwpd" id="npwpd" value="" placeholder="NPWPD" autocomplete="off" />
					<input class="form-control" type="text" name="customernm" id="customernm" value="" placeholder="Nama Subjek Pajak" autocomplete="off" readonly />
					<button type="button" class="btn hide" data-toggle="modal" data-target="#cuDialog">Cari</button>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="opnm">Nama Konter</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="opnm" id="opnm" value="<?=$dt['opnm'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="usaha_id">Jenis Usaha</label>
				<div class="col-sm-5">
					<?echo $select_usaha;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="def_pajak_id">Jenis Pajak</label>
				<div class="col-sm-5">
					<?echo $select_pajak;?>
				</div>
			</div>
            
			<div class="form-group">
				<label class="control-label col-sm-3" for="kecamatan_id">Kecamatan</label>
				<div class="col-sm-4">
					<?echo $select_kecamatan;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="kelurahan_id">Kelurahan</label>
				<div class="col-sm-4">
					<?echo $select_kelurahan;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="opalamat">Alamat</label>
				<div class="col-sm-8">
					<textarea class="form-control" name="opalamat" id="opalamat" rows="1" cols="50"><?=$dt['opalamat'];?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="notes">Keterangan</label>
				<div class="col-sm-8">
					<textarea class="form-control" name="notes" id="notes" rows="1" cols="50"><?=$dt['notes'];?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="latitude">Kordinat Peta</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" name="latitude" id="latitude" value="<?=$dt['latitude'];?>" /> , 
				</div>
				<div class="col-sm-3">
					<input type="text" class="form-control" name="longitude" id="longitude" value="<?=$dt['longitude'];?>" />				
                </div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="status">Status</label>
				<div class="col-sm-4">
					<?echo $select_status;?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="aktifnotes">Catatan</label>
				<div class="col-sm-8">
					<textarea class="form-control" name="aktifnotes" id="aktifnotes" rows="1" cols="50"><?=$dt['aktifnotes'];?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="tmt">Status TMT</label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="tmt" id="tmt" value="<?=$dt['tmt'];?>" required />
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
							<label class="control-label col-sm-8" style="color:#3C8DBC"><u> Pertandingan dan lainnya </u></label>
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

		<div class="col-sm-12">  
		<p>&nbsp;</p>
			<button type="submit" onclick="saveKD()"class="btn btn-primary">Simpan</button>
			<button type="button" class="btn" id="btn_cancel">Batal / Kembali</button>
			</div>  
		<?php echo form_close();?>
</div>
<? $this->load->view('_foot'); ?>
