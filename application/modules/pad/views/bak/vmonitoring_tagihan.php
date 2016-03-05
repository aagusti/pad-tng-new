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
	var bulan_id  = $('#bulan_id').val();
	var tahun  = $('#tahun').val();

	oTable.fnReloadAjax("<? echo active_module_url($controller); ?>grid/"+tahun+bulan_id);
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

$(window).load(function(){ 
		reload_grid();
 });

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
			{ "aTargets": [5], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },

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
		"sAjaxSource": "<?=active_module_url();?>monitoring_tagihan/grid"
	}); 
    
    $('.dataTables_filter input').unbind();
    $('.dataTables_filter input').bind('keyup', function(e) {
        if(e.keyCode == 13) {
            oTable.fnFilter(this.value);   
        }
    });
	
	var tb_array = [
		<? if ($this->uri->segment(2) == 'monitoring_tagihan') : ?>
		'<div class="btn-group pull-left">',
		'	<button id="btnshow_rpt" class="btn btn-primary" data-rpt="dat_surat_teguran">Cetak Teguran</button>',
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
		'<?=$select_bulan;?>',
		'</div>',
		'<div class="btn-group pull-left">',
		'<input class="form-control" typle="text" id="tahun" style="width:70px;" value="<?=date('Y')?>" >',
		'</div>',
		'</br>',
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
		if(mID) {
		var proses = confirm('Cetak data ini ?');
            if(proses == true) {
            var rpt = $(this).data('rpt');
            show_rpt(rpt);
            $.ajax({
                url: '<? echo active_module_url($controller); ?>proses_cetak/'+mID,
                async: false,
                success: function (data) {
                    if (data != 'ok') 
                        alert('Proses Teguran Gagal');
                    else {
                        reload_grid();
                        alert('Proses Teguran dan Status Cetak Telah Diupdate');
                    }
                },
                error: function (xhr, desc, er) {
                    alert(er);
                }
            });
            }
        }
	});
    
	$("#btn_cetak").click(function(){
		if(mID) {
		var proses = confirm('Cetak data ini ?');
            if(proses == true) {
            var rpt = $(this).data('rpt');
            show_rpt(rpt);
            }
		}else{
			alert('Silahkan pilih data yang akan dicetak');
		}
	});

	$('#bulan_id').change(function() {
        switch_btn();
		reload_grid();
	});
});
</script>
 <div class="content">  
	 <div class="container-fluid">  
		
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#"><strong>MONITORING TAGIHAN</strong></a>
			</li>
		</ul>
		
		<?=msg_block();?>

		<table class="table" id="table1">
			<thead>
				<tr>
					<th>index</th>
					<th>Teguran No</th>
					<th>Tanggal</th>
					<th>Wajib Pjk</th>
					<th>Objek Pjk</th>
					<th>NOPD</th>
					<th>Jenis Usaha</th>
					<th>Jenis Pajak</th>
					<th class="th6">Masa</th>
					<th class="th7">J.Tempo</th>
					<th class="th8">Pajak</th>
					<th>Cetak Ke</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<? $this->load->view('_foot'); ?>