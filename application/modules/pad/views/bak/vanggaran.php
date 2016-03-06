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
</style>

<script>
var mID;
var oTable;
var oTable2;

$(document).ready(function() {	
	oTable = $('#table1').dataTable({
		"bInfo": false ,
		"iDisplayLength": 13,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 1, "asc" ]],
		"aoColumnDefs": [			
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
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
		"sAjaxSource": "<?=active_module_url();?>anggaran/grid"
	}); 

	oTable2 = $('#table2').dataTable({
		"bInfo": false ,
		"iDisplayLength": 13,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sDom": '<"toolbar2">frtip',
		"aaSorting": [[ 1, "asc" ]],
		"aoColumnDefs": [			
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
		],
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
		"sAjaxSource": "<?=active_module_url();?>anggaran/grid2"
	}); 

	
	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btn_tambah" class="btn pull-left" type="button">Tambah</button>',
		'	<button id="btn_edit" class="btn pull-left" type="button">Edit</button>',
		'	<button id="btn_delete" class="btn pull-left" type="button">Hapus</button>',
		'</div></br>'
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	var tb_array2 = [
		'<div class="btn-group pull-left">',
		'</div></br>'
	];
	var tb2 = tb_array2.join(' ');	
	$("div.toolbar2").html(tb2);

	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>anggaran/add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>anggaran/edit/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>anggaran/delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
});
</script>
 <div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>ANGGARAN TAHUN <?=pad_tahun_anggaran();?></strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>Kode Rek.</th>
					<th>Tahun</th>
					<th>Status</th>
					<th>Target</th>
					<th>Bulan 1</th>
					<th>Bulan 2</th>
					<th>Bulan 3</th>
					<th>Bulan 4</th>
					<th>Bulan 5</th>
					<th>Bulan 6</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table class="table" id="table2">
			<thead>
				<tr>
					<th>index</th>
					<th>Kode Rek.</th>
					<th>Tahun</th>
					<th>Status</th>
					<th>Target</th>
					<th>Bulan 7</th>
					<th>Bulan 8</th>
					<th>Bulan 9</th>
					<th>Bulan 10</th>
					<th>Bulan 11</th>
					<th>Bulan 12</th>
					<th>Jumlah</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>


	</div>
</div>
<? $this->load->view('_foot'); ?>