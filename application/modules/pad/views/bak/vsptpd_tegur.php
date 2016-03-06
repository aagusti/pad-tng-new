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
.ui-state-default,.ui-widget-content .ui-state-default,.ui-widget-header .ui-state-default {
	font-weight:bold;
}
</style>

<script>
var mID;
var  usID;
var oTable;
var xRow;

function switch_btn() {
    var proses_id  = $('#proses_id').val();
    if (proses_id == 2) {
        $('#btn_proses').hide();
        $('#btn_cetak').show();
    } else {
        $('#btn_proses').show();
        $('#btn_cetak').hide();
    }
}

function reload_grid() {
	var usaha_id  = $('#usaha_id').val();
	var proses_id = $('#proses_id').val();
    
    <? if ($this->uri->segment(2) != 'sptpd_tegur') : ?>
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+usaha_id+"/"+proses_id);
    <? else : ?>
	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+usaha_id);
    <? endif; ?>
}

function show_rpt(rpt){
    var usaha_id = $('#usaha_id').val();
	var rptparams = {
        rpt: rpt,
        uid: usaha_id,
        cuid: mID,
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
			{ "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "200px", "sClass": "" },
			{ "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "200px", "sClass": "" },
			{ "aTargets": [4], "bSearchable": true,  "bVisible": true,  "sWidth": "150px", "sClass": "" },
			{ "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "200px", "sClass": "" },
			{ "aTargets": [6], "bSearchable": true,  "bVisible": true,  "sWidth": "90px", "sClass": "" },
			{ "aTargets": [7], "bSearchable": true,  "bVisible": true,  "sWidth": "100px", "sClass": "right" },
		],
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(nRow).on("click", function (event) {
				if ($(this).hasClass('row_selected')) {
					mID = '';  usID = '';
					$(this).removeClass('row_selected');
				} else {
					var data = oTable.fnGetData( this );
					mID = data[0]; usID = data[11];
					
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
		"sAjaxSource": "<?=active_module_url();?>sptpd_tegur/grid"
	}); 
    
    $('.dataTables_filter input').unbind();
    $('.dataTables_filter input').bind('keyup', function(e) {
        if(e.keyCode == 13) {
            oTable.fnFilter(this.value);   
        }
    });
	
	var tb_array = [
		<? if ($this->uri->segment(2) == 'sptpd_tegur') : ?>
		'<div class="btn-group pull-left">',
		'	<button id="btnshow_rpt" class="btn btn-primary hidden" data-rpt="dat_surat_pemberitahuan">Cetak Pemberitahuan</button>',
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="dat_surat_teguran">Cetak Teguran</button>',
		'	<button id="btnshow_amplop_rpt" class="btn btn-info" data-rpt="dat_amplop_teguran">Cetak Amplop</button>',
		'</div>',
        <? else : ?>
		'<div class="btn-group pull-left">',
		'	<button id="btn_proses" class="btn btn-success" >Proses SKPD-J</button>',
		'	<button id="btn_cetak" class="btn btn-primary hide" data-rpt="skpdj_30">Cetak SKPD-J</button>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<?=$select_proses_id;?>',
		'</div>',
		<? endif; ?>
		'<div class="btn-group pull-left">',
		'<?=$select_usaha;?>',
		'</div></br>',
	];
	var tb = tb_array.join(' ');	
	$("div.toolbar").html(tb);
    
	$('#btn_proses').click(function() {
		if(mID) {
            var proses = confirm('Proses data ini ?');
            if(proses == true) {
                var usaha_id = $('#usaha_id').val();
                $(this).button('loading');
                setTimeout(function() {
                    $.ajax({
                        url: '<? echo active_module_url($controller); ?>proses_skpdj/'+usaha_id+'/'+mID,
                        async: false,
                        success: function (data) {
                            if (data != 'ok') 
                                alert('Proses penetapan SKPD Jabatan GAGAL');
                            else {
                                reload_grid();
                                alert('Selesai');
                            }
                        },
                        error: function (xhr, desc, er) {
                            alert(er);
                        }
                    })
                }, 0);
                $(this).button('reset');
            }
		} else {
            var proses = confirm('Proses semua data ?');
            if(proses == true) {
                var usaha_id = $('#usaha_id').val();
                $(this).button('loading');
                setTimeout(function() {
                    $.ajax({
                        url: '<? echo active_module_url($controller); ?>proses_skpdj/'+usaha_id,
                        async: false,
                        success: function (data) {
                            if (data != 'ok') 
                                alert('Proses penetapan SKPD Jabatan GAGAL');
                            else {
                                reload_grid();
                                alert('Selesai');
                            }
                        },
                        error: function (xhr, desc, er) {
                            alert(er);
                        }
                    })
                }, 0);
                $(this).button('reset');
                
            }
		}
	});
    
	$("[id=btnshow_rpt]").click(function(){
        if (usID == 7){
            var rpt = "dat_surat_teguran_at";
        	}else{
            var rpt = $(this).data('rpt');
        	}
        show_rpt(rpt);
	});

	$("[id=btnshow_amplop_rpt]").click(function(){
        var rpt = $(this).data('rpt');
        show_rpt(rpt);
	});
    
	$("#btn_cetak").click(function(){
		if(mID) {
        	if (usID == 7){
            var rpt = "dat_surat_teguran_at";
        	}else{
            var rpt = $(this).data('rpt');
        	}

            show_rpt(rpt);
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});

	$('#proses_id, #usaha_id').change(function() {
        switch_btn();
		reload_grid();
	});
});
</script>
 <div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong><?=$judul;?></strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>NPWPD</th>
					<th>Wajib Pajak</th>
					<th>Nama OP</th>
					<th>Pemilik/Pengelola</th>
					<th>Alamat</th>
					<th>Jns.Usaha</th>
					<th>Max.Omset</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<? $this->load->view('_foot'); ?>