<? $this->load->view('_head'); ?>
<link href="<?=base_url()?>assets/css/simple-side-bar.css" rel="stylesheet" type="text/css" />
<div id="wrapper" class="">
  <div id="sidebar-wrapper">
    <? $this->load->view(active_module().'/_navbar'); ?>
  </div>
  <div id="page-content-wrapper">
    <div class="content">  
    <!-- content -->
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#"><strong>Jurnal - Ketetapan </strong></a>
        </li>
      </ul>
      <?=msg_block();?>
      <table class="table" id="table1">
        <thead>
          <tr>
            <th>index</th>
            <th>No</th>
            <th>Tanggal</th>
            <th>NOPD</th>
            <th>Wajib Pajak/Objek Pajak</th>
            <th>Rekening</th>
            <th>Nilai</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>

    </div>
  </div>
</div>
<? $this->load->view('_foot'); ?>
<script>
$(document).ready(function() {  
  oTable = $('#table1').dataTable({
    "paginationType": "full_numbers",
    "dom": '<"toolbar">frtip',      
    "processing": true,
    "serverSide": true,    
    "ajaxSource": "<? echo active_module_url($controller); ?>grid/",
    "columnDefs": [
            { "aTargets": [0], "bSearchable": false, "bVisible": false}
            ]
  }
  );
});
  /*
    "iDisplayLength": 13,
    "bJQueryUI": true,
    "bAutoWidth": false,

    "aaSorting": [[ 0, "desc" ]],
      { "aTargets": [1], "bSearchable": true,  "bVisible": true,  "sWidth": "70px", "sClass": "" },
      { "aTargets": [2], "bSearchable": true,  "bVisible": true,  "sWidth": "85px", "sClass": "" },
      { "aTargets": [3], "bSearchable": true,  "bVisible": true,  "sWidth": "75px", "sClass": "" },
      { "aTargets": [4], "bSearchable": true,  "bVisible": true,  "sWidth": "58px", "sClass": "" },
      { "aTargets": [5], "bSearchable": true,  "bVisible": true,  "sWidth": "80px", "sClass": "" },
      { "aTargets": [6], "bSearchable": true,  "bVisible": true,  "sWidth": "", "sClass": "" },
      
      { "aTargets": [8], "bSearchable": true,  "bVisible": true,  "sWidth": "65px", "sClass": "center" },
      { "aTargets": [9], "bSearchable": true,  "bVisible": true,  "sWidth": "86px", "sClass": "right" },
      { "aTargets": [10], "bSearchable": true,  "bVisible": true,  "sWidth": "97px", "sClass": "right" },
      { "aTargets": [11], "bSearchable": true,  "bVisible": true,  "sWidth": "97px", "sClass": "right" },
      { "aTargets": [12], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
      { "aTargets": [13], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
      { "aTargets": [14], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "center" },
      { "aTargets": [15], "bSearchable": false, "bVisible": false, "sWidth": "", "sClass": "" },
      { "aTargets": [16], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "right" },
      { "aTargets": [17], "bSearchable": false, "bVisible": true, "sWidth": "", "sClass": "center" }



    ],
    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
      $(nRow).on("click", function (event) {
        if ($(this).hasClass('row_selected')) {
          mID = '';
          $(this).removeClass('row_selected');
        } else {
          var data = oTable.fnGetData( this );
          mID = data[0];
          nobayar = data[1];
          uID = data[12];
          tID = data[13];

          nopd  = data[5];
          wp_sspd = data[6];
          jp_sspd = data[7];
          masa_sspd   = data[8];
          jatuh_tempo = data[9];
          pajak_sspd = data[11];
          no_bayar_sspd = data[18];
          cara_bayar = data[14];

          
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


  });*/ 
</script>
