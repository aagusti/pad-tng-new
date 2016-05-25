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
            <th><input type="checkbox"id="select_all" name="select_all"></th>
            <th>No</th>
            <th>Tanggal</th>
            <th>NOPD</th>
            <th>Wajib Pajak/Objek Pajak</th>
            <th>Rekening</th>
            <th>Pokok</th>
            <th>Rekening</th>
            <th>Denda</th>
            <th>Bunga</th>
            <th>Posted</th>
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
  var oTable;
  var rows_selected = [];

  oTable = $('#table1').DataTable({
    paginationType: "full_numbers",
    dom: '<"toolbar">flrtip',
    processing: true,
    serverSide: true,
    ajaxSource: "<? echo active_module_url($controller); ?>grid/",

    order: [[ 1, "asc" ]],
        columns: [
            { data: "id",
                render: function ( data, type, row ) {
                        return '<input type="checkbox" class="editor-active" value="'+data.id+'">';
                    }},
            { data: "nomor_tagihan" },
            { data: "tanggal" },
            { data: "nopd" },
            { data: "customernm" },
            { data: "rekening_pokok" },
            { data: "pokok" },
            { data: "rekening_denda" },
            { data: "denda" },
            { data: "bunga" },
            { data: "posted" }
            ],
      columnDefs: [ {
        orderable: false,
        visible: true,
        className: 'select-checkbox',
        targets:   0
        }],

      rowCallback: function(row, data, dataIndex){
         var rowId = data.id;
         //console.log('id:'.rowId);
         if($.inArray(rowId, rows_selected) !== -1){
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
         }
      },

      fnServerData: function ( sSource, aoData, fnCallback ) {
            rows_selected.length = 0;
            aoData.push({ "name": "pos",  "value" : $('#posted').val() });
            aoData.push({ "name": "rekkd",  "value" : $('#rekkd').val() });
            aoData.push({ "name": "tgl1", "value" : $('#start_date').val() });
            aoData.push({ "name": "tgl2", "value" : $('#end_date').val() });

            $.getJSON( sSource, aoData, function (json) {
                fnCallback(json);
            });
      },

      language: {
        lengthMenu: "&nbsp;Show: _MENU_",
      },
    }
  );

    var tb_array = [
      '<div class="pull-left form-inline">',
      '    <button id="posting" class="btn btn-primary btn-sm">Proses</button>',
      '    <div class="form-group">',
      '    <label>Filter:</label>',
      '    <select id="posted" name="posted" class="form-control input-sm"  style="width:100px;">',
      '                    <option value="0">Unposted</option>',
      '                    <option value="1">Posted</option>',
      '                    </select>',
      '    </div>',
      '    <div class="form-group">',
      '    <label>Rekening:</label><?=$select_rekkd;?>',
      '    <div class="form-group">',
      '    <label>Range:</label>',
      '    <input type="date" class="form-control input-sm" style="width:90px;" value="<?=date('Y-m-01');?>" id="start_date" name="start_date"> s.d. ',
      '    <input type="date" class="form-control input-sm" style="width:90px;" value="<?=date('Y-m-d');?>" id="end_date" name="end_date">',
      '    </div>',
      '</div>',
    ];
    var tb = tb_array.join(' ');
    // $("div.toolbar").html(tb);
    $("div.dataTables_length").prepend(tb);
    $("div.dataTables_filter").addClass("pull-right");
    $("div.dataTables_length").append($("div.dataTables_filter"));

    $("#start_date, #end_date").datepicker({
        dateFormat:'yy-mm-dd',
        changeMonth:true,
        changeYear:true
    });

   // Handle click on checkbox
   $('#table1 tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');

      // Get row data
      var data = oTable.row($row).data();

      // Get row ID
      var rowId = data.id;
      //alert(rowId);
      // Determine whether row ID is in the list of selected row IDs
      var index = $.inArray(rowId, rows_selected);
      //console.log('id:'.rowId);
      // If checkbox is checked and row ID is not in list of selected row IDs
      if(this.checked && index === -1){
         rows_selected.push(rowId);

      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      } else if (!this.checked && index !== -1){
         rows_selected.splice(index, 1);
      }

      if(this.checked){
         $row.addClass('selected');
      } else {
         $row.removeClass('selected');
      }

      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(oTable);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle click on table cells with checkboxes
   $('#table1').on('click', 'tbody td, thead th:first-child', function(e){
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('thead input[name="select_all"]', oTable.table().container()).on('click', function(e){
      if(this.checked){
         $('#table1 tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#table1 tbody input[type="checkbox"]:checked').trigger('click');
      }

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
   oTable.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(oTable);
   });

   // Handle form submission event
   $('#frm-example').on('submit', function(e){
      var form = this;

      // Iterate over all selected checkboxes
      $.each(rows_selected, function(index, rowId){
         // Create a hidden element
         $(form).append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
         );
      });

      // FOR DEMONSTRATION ONLY

      // Output form data to a console
      $('#example-console').text($(form).serialize());
      console.log("Form submission", $(form).serialize());

      // Remove added elements
      $('input[name="id\[\]"]', form).remove();

      // Prevent actual form submission
      e.preventDefault();
   });
   // $('#posted').change( function() {
      // oTable.columns(9).search('='+$(this).val()).draw();
   // });
   $('#posted, #start_date, #end_date, #rekkd').change( function() {
      oTable.ajax.reload();
   });
   $('#posting').click( function() {
      if (rows_selected.length==0){
        alert('Tidak ada baris yang dipilih');
      }else{
        $.ajax({
          type: "GET",
          url: '<?=base_url()?>pad/jinvoice/posting/'+$('#posted').val(),
          data: {id : rows_selected.toString()},
          success: function(html){
              msg = jQuery.parseJSON(html);
              alert(msg.message);
              if (msg.status==1) {

                rows_selected = [];
                oTable.draw();
                //oTable.columns(6).search('='+$('#posted').val()).draw();
              }
          }
        });
      }
   });

   // oTable.columns(9).search('='+$('#posted').val()).draw();

});

//
// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}
</script>
