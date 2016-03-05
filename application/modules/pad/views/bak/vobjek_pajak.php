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
.table > tbody > tr > td{
	padding: 3px;
}
</style>

<script>
var mID;
var cID;

var oTable;
var xRow;

function reload_grid() {
	var usaha_id  = $('#usaha_id').val();
	var status_id  = $('#customer_status_id').val();
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+usaha_id+"/"+status_id);
}

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
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "180px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "180px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [6], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [7], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';  cID = ''; 
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					cID = data[8];

					
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
		"sAjaxSource": "<?=active_module_url('objek_pajak/grid');?>"
	}); 
    
    $('.dataTables_filter input').unbind();
    $('.dataTables_filter input').bind('keyup', function(e) {
        if(e.keyCode == 13) {
            oTable.fnFilter(this.value);   
        }
    });
	
	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btn_tambah" class="btn pull-left hide" type="button">Tambah</button>',
		'	<button id="btn_edit" class="btn pull-left" type="button">Edit</button>',
		'	<button id="btn_delete" class="btn pull-left" type="button">Hapus</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_usaha;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_status;?>',
		'</div></br>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);

	$('#btn_tambah').click(function() {
		window.location = "<?=active_module_url('objek_pajak/add');?>";
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>subjek_pajak/edit/'+cID+'/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>objek_pajak/delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
    
	$('#usaha_id, #customer_status_id').change(function() {
		reload_grid();
	});
});
</script>
 <div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>PENDAFTARAN - OBJEK PAJAK</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>NOPD</th>
					<th>Jenis Usaha</th>
					<th>NPWPD</th>
					<th>Wajib Pajak</th>
					<th>Kecamatan</th>
					<th>Kelurahan</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<? $this->load->view('_foot'); ?>
