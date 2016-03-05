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
var sID;
var oTable;
var xRow;

function reload_grid() {
	var usaha_id  = $('#usaha_id').val();
    var terimatgl = $('#terimatgl').val();
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+usaha_id+"/"+terimatgl);
}

function show_rpt(rpt){
	var rptparams = {
        rpt: rpt,
        id: mID,
        sid: sID,
    }
	
	var data = decodeURIComponent($.param(rptparams));
	var url  = '<? echo active_module_url($controller); ?>cetak/pdf/?'+data;
	
	var winparams = 'width='+screen.width+',height='+screen.height+',directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no';
	window.open(url, 'Laporan', winparams);
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
			{ "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "80px", "sClass": "" },
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "240px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "220px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "" },
			{ "aTargets": [6], "bSearchable": false, "bVisible": false},
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';
					sID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0];
					sID = data[6];
					
					oTable.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
				}
			})
		},
		"fnDrawCallback": function( oSettings ) {
			mID = '';
			sID = '';
		},
		"oLanguage": {
			"sProcessing":   "<i class='fa fa-refresh fa-spin' style='font-size: 70px;'></i>",
			"sLengthMenu":   "Tampilkan _MENU_",
			"sZeroRecords":  "Tidak ada data",
			"sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
			"sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
			"sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
			"sInfoPostFix":  "",
			"sSearch":       "Cari : ",
			"sUrl":          "",
			"oPaginate": {
				"sFirst":    "Awal",
				"sPrevious": "Balik",
				"sNext":     "Lanjut",
				"sLast":     "Akhir",
			}
		},
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=active_module_url();?>kartu_data/grid"
	}); 
    
    $('.dataTables_filter input').unbind();
    $('.dataTables_filter input').bind('keyup', function(e) {
        if(e.keyCode == 13) {
            oTable.fnFilter(this.value);   
        }
    });
	
	var tb_array = [
		'<div class="btn-group pull-left">',
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="kartudt">Cetak Kartu Data</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_usaha;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'	<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span><input style="width:100px;" class="form-control" type="text" name="terimatgl" id="terimatgl" value="<?=date('d-m-Y');?>"/></div>',
		'</div></br>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);
    
	$("[id=btnshow_rpt]").click(function(){
		if(mID) {
            var rpt = $(this).data('rpt');
            show_rpt(rpt);
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});
    
	$('#usaha_id').change(function() {
		reload_grid();
	});
    
	var terimatgl_dtp = $('#terimatgl').datepicker({
		format: 'dd-mm-yyyy'
	}).on('changeDate', function(ev) {
		reload_grid();
		terimatgl_dtp.hide();
	}).data('datepicker');
    
});
</script>
 <div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>KARTU DATA *Berdasarkan SSPD yg masuk</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>NPWPD</th>
					<th>Subjek Pajak</th>
					<th>Pemilik/Pengelola</th>
					<th>Alamat</th>
					<th>Jns.Usaha</th>
					<th>sid</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<? $this->load->view('_foot'); ?>