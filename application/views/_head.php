<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SIMPAD | Dashboard</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link href="<?=base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
  <link href="<?=base_url()?>assets/adminlte/dist/css/font-awesome.min.css" rel="stylesheet" type="text/css" />  
  <link href="<?=base_url()?>assets/adminlte/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
  <link href="<?=base_url()?>assets/adminlte/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
  <link href="<?=base_url()?>assets/datatables/extras/TableTools/media/css/TableTools.css" rel="stylesheet">
  <link href="<?=base_url()?>assets/datatables/media/css/demo_page.css" rel="stylesheet">
  <link href="<?=base_url()?>assets/datatables/media/css/demo_table_jui.css" rel="stylesheet">
  <link href="<?=base_url()?>assets/jq/smoothness/jquery-ui-1.8.4.custom.css" rel="stylesheet">
  <link href="<?=base_url()?>assets/css/datatablescustom.css" rel="stylesheet">
  <!--
  -->
  <link rel="shortcut icon" href="<?=base_url()?>assets/img/favicon.ico">
</head>
<script>
function initMenu(param) {
      $.ajax({
           type: "POST",
           url: '<? echo base_url(); ?>pad/menu/'+param,
           data:{action:'call_this'},
      });
 }
 </script>
<body class="skin-blue fixed <?echo $this->session->userdata('sidebar-collapse')?>">
  <div class="wrapper">
    <header class="main-header">
      <a href="<?base_url()?>/index.php" class="logo">DEVEL <b> PAD</b></a>
      <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" onclick="initMenu('SC')">
          <span class="sr-only">Toggle navigation</span>
        </a>


        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <!-- User Account: style can be found in dropdown.less -->
            <?if(is_login()) :?>
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?=base_url()?>assets/img/user.png" class="user-image" alt="User Image"/>
                  <span class="hidden-xs">MENU</span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="<?=base_url()?>assets/adminlte/dist/img/avatar5.png" class="img-circle" alt="User Image" />
                  <p>
                    <? echo $this->session->userdata('username');?>
                    <small>Last Login : <? echo $this->session->userdata('last_login');?> </small>
                  </p>
                </li>
                <li class="user-body">
               <?if(is_super_admin()) :?>
               <select multiple class="form-control" name="app_id" id="app_id" <?//if(!$app_enabled) echo 'disabled';?>>
                 <?php if( isset($apps) && $apps): ?>
                  <option value="admin">ADMIN</option>
                  <?php foreach($apps as $data): ?>
                    <option value="<?php echo $data->app_path;?>" <?php if(active_module()==$data->app_path) echo 'selected';?>><?php echo $data->nama;?></option>
                  <?php endforeach;?>
                <?php else:?>
                  <option value="">Not configured!</option>
                <?php endif; ?>
              </select>
              <?endif?>
    
             
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a class="btn btn-default btn-flat" href="<?echo base_url().'admin/users2/changepwd/'.sipkd_user_id();?>">Ubah Password</a>
                  </div>
                  <div class="pull-right">
                    <a class="btn btn-default btn-flat" href="<?echo base_url().'logout';?>">Logout</a>
                  </div>
                </li>
              </ul>
            </li>
            <?endif;?>
          </ul>
        </div>

      </nav>
    </header>
    <div class="wrapper">
      <script src="<?=base_url()?>assets/jquery/jquery-1.12.1.min"></script>
      <script src="<?=base_url()?>assets/adminlte/dist/js/jquery-ui.min.js" type="text/javascript"></script>
      <script src="<?=base_url()?>assets/adminlte/plugins/slimScroll/jquery.slimScroll.min.js" type="text/javascript"></script>
      <script src="<?=base_url()?>assets/adminlte/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
      <script src="<?=base_url()?>assets/adminlte/dist/js/app.min.js" type="text/javascript"></script>
   <!--
       -->
      <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
      <script>
        $.widget.bridge('uibutton', $.ui.button);
      </script>
      <script src="<?=base_url()?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>  
      <script src="<?=base_url()?>assets/bootstrap/js/bootstrap-typeahead.min.js" type="text/javascript"></script> 
      <script src="<?=base_url()?>assets/bootstrap/js/bootstrap-combobox.js" type="text/javascript"></script>   
      <script src="<?=base_url()?>assets/datatables/media/js/jquery.dataTables.min.js"></script>
      <script src="<?=base_url()?>assets/datatables/media/js/jquery.dataTables.columnFilter.js"></script>
      <script src="<?=base_url()?>assets/datatables/media/js/jquery.dataTables.ext.js"></script>
      <script src="<?=base_url()?>assets/datatables/extras/TableTools/media/js/ZeroClipboard.min.js"></script>
      <script src="<?=base_url()?>assets/datatables/extras/TableTools/media/js/TableTools.min.js"></script>
      <script src="<?=base_url()?>assets/js/numberFormatter.js"></script>	
      <script src="<?=base_url()?>assets/js/autoNumeric.min.js"></script>
      <script src="<?=base_url()?>assets/js/formremember.js"></script>
      <script src="<?=base_url()?>assets/js/date.js"></script>
<!--
-->
      <script>
/*
        var timer;
        var wait=10;
        document.onkeypress=resetTimer;
        document.onmousemove=resetTimer;

        function resetTimer() {
         clearTimeout(timer);
         timer=setTimeout("logout()", 60000*wait);
       }
       
       */

       function logout() {
			 window.location.href='<?=base_url()?>logout';
		}
		
		$(document).ready(function() {		

			$('#app_id').change( function() {
				window.location = '<?=base_url();?>change_module/'+$('#app_id').val();
			});

			$('#msg_helper').delay(5000).fadeOut('slow');
			
			$('#modalform').on('hidden', function() {
        $(this).removeData('modal');
      });

		});

    </script>





