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

$(document).ready(function() {	
	oTable = $('#table1').dataTable({
		"iDisplayLength": 13,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sDom": '<"toolbar">frtip',
		"aaSorting": [[ 0, 1, 2, 3, "asc" ]],
		"aoColumnDefs": [			
			{ "aTargets": [0], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "150px", "sClass": "" },
			{ "aTargets": [4], "bSearchable": true,  "bVisible": true,  "sWidth": "", "sClass": "right" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "", "sClass": "right" },


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
		"sAjaxSource": "<?=active_module_url();?>air_hda/grid"
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

	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>air_hda/add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>air_hda/edit/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>air_hda/delete/'+mID;
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
				<a href="#"><strong>Harga Dasar Air (HDA)</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
				<th>index</th>
					<th>Zona</th>
					<th>Golongan</th>
					<th>Kelompok Usaha</th>
					<th>Volume</th>
					<th>HDA</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

	</div>
</div>
<? $this->load->view('_foot'); ?>