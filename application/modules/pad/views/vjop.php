<? $this->load->view('_head'); ?>
<? $this->load->view('_head'); ?>
<link href="<?=base_url()?>assets/css/simple-side-bar.css" rel="stylesheet" type="text/css" />
<div id="wrapper" class="">
  <div id="sidebar-wrapper">
    <? $this->load->view(active_module().'/_navbar'); ?>
  </div>
  <div id="page-content-wrapper">
 <div class="content">
   <div class="container-fluid">

    <ul class="nav nav-tabs">
      <li class="active">
        <a href="#"><strong>Daftar - Wajib/Objek Pajak</strong></a>
      </li>
    </ul>

  <div class="container-fluid">
    <?=msg_block();?>
  </div>

    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="table1">
      <thead>
        <tr>
          <th><input type="checkbox"id="select_all" name="select_all"></th>
          <th>NPWPD</th>
          <th>Wajib Pajak</th>
          <th>Pemilik/Pengelola</th>
          <th>Alamat</th>
          <th>J.OP</th>
          <th>Posted</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<? $this->load->view('_foot'); ?>
<style>
.toolbar {
    float:left;
    text-align: right;
}
</style>
<script>
$.fn.dataTableExt.afnFiltering.push(
  function( oSettings, aData, iDataIndex ) {
    // var iPosted = document.getElementById('posted').value;
    return true;
  }
);

$(document).ready(function() {
  var oTable;

  var rows_selected = [];
  var oTable = $('#table1').DataTable({
        paginationType: "full_numbers",
        processing: true,
        serverSide: true,
        ajaxSource: "<?=active_module_url();?>jop/grid",

        dom: '<"toolbar">flrtip',
        order: [[ 1, "asc" ]],
        columns: [
            { data: "id",
                render: function ( data, type, row ) {
                        return '<input type="checkbox" class="editor-active" value="'+data.id+'">';
                    }},
            { data: "npwpd" },
            { data: "customernm" },
            { data: "nama" },
            { data: "alamat" },
            { data: "jml_op" },
            { data: "posted" }
            ],
      columnDefs: [ {
        orderable: false,
        visible: true,
        className: 'select-checkbox',
        targets:   0
        },{
        orderable: false,
        visible: false,
        className: 'select-checkbox',
        targets:   6 }],



      rowCallback: function(row, data, dataIndex){
         var rowId = data.id;
         //console.log('id:'.rowId);
         if($.inArray(rowId, rows_selected) !== -1){
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
         }
      },

      fnServerData: function ( sSource, aoData, fnCallback ) {
            aoData.push({ "name": "pos",  "value" : $('#posted').val() });
            $.getJSON( sSource, aoData, function (json) {
                fnCallback(json);
            });
      },

      language: {
        lengthMenu: "&nbsp;Show: _MENU_",
      },
    });


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
      '</div>',
    ]
    var tb = tb_array.join(' ');
    $("div.dataTables_length").prepend(tb);
    $("div.dataTables_filter").addClass("pull-right");
    $("div.dataTables_length").append($("div.dataTables_filter"));

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
   $('#posted').change( function() {
      // oTable.columns(6).search('='+$(this).val()).draw();
      oTable.ajax.reload();
   });
   $('#posting').click( function() {
      if (rows_selected.length==0){
        alert('Tidak ada baris yang dipilih');
      }else{
        $.ajax({
          type: "GET",
          url: '<?=base_url()?>pad/jop/posting/'+$('#posted').val(),
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

   // oTable.columns(6).search('='+$('#posted').val()).draw();

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