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
var xRow;

$(document).ready(function() {
	oTable = $('#table1').dataTable({
		"sScrollY": "380px",
		"bScrollCollapse": true,
		"bPaginate": false,
		"sDom": '<"toolbar">frtip',

		"aoColumnDefs": [
			{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
		],
		"aoColumns": [
			null,
			{ "sWidth": "6%" },
			null
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if(aData[0]!=xRow) {
					if ($(this).hasClass('row_selected')) {
						$(this).removeClass('row_selected');
					} else {
						oTable.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}

					var data = oTable.fnGetData( this );
					mID = data[0];
				}
				xRow = aData[0];
			})
		},
		"bSort": true,
		"bInfo": true,
		"bProcessing": true,
		"sAjaxSource": "<?=active_module_url();?>urusan/grid"
	});

	$("div.toolbar").html('<div class="btn-group pull-left"><button id="btn_tambah" class="btn pull-left" type="button">Tambah</button><button id="btn_edit" class="btn pull-left" type="button">Edit</button> <button id="btn_delete" class="btn pull-left" type="button">Hapus</button></div>');

	$('#btn_tambah').click(function() {
		window.location = '<?=active_module_url();?>urusan/add/';
	});

	$('#btn_edit').click(function() {
		if(mID) {
			window.location = '<?=active_module_url();?>urusan/edit/'+mID;
		}else{
			alert('Silahkan pilih data yang akan diedit');
		}
	});

	$('#btn_delete').click(function() {
		if(mID) {
			var hapus = confirm('Hapus data ini?');
			if(hapus==true) {
				window.location = '<?=active_module_url();?>urusan/delete/'+mID;
			};
		}else{
			alert('Silahkan pilih data yang akan dihapus');
		}
	});
});
</script>

<div class="content">
    <div class="container-fluid">
		<div class="page-header" style="margin-bottom:8px;">
			<strong>:: Urusan</strong>
		</div>
    </div>

    <div class="container-fluid">
		<?=msg_block();?>
    </div>

    <div class="container-fluid">
		<table class="table" id="table1">
			<thead>
				<tr>
					<th>Index</th>
					<th>Kode</th>
					<th>Uraian</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<? $this->load->view('_foot'); ?>