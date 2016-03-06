<?if($this->session->userdata('login')) :?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>
      <li <?echo $current=='beranda' ? 'class="active"' : '';?>><a class="brand" href="<?=active_module_url();?>">e-SPTPD</a></li>
                    <li>
						<a href="<?=base_url().'logout';?>"><?=wp_nm();?> :: Logout</a>
					</li>
    </ul>
  </section>

</aside>
<? endif; ?>

<div class="content-wrapper">
<!--
 <link href="<?=base_url()?>assets/adminlte/plugins/datepicker/datepicker3.min.css" rel="stylesheet" type="text/css" />
 <script src="<?=base_url()?>assets/adminlte/plugins/datepicker/bootstrap-datepicker.min.js"></script> 
-->
<link href="<?=base_url()?>assets/adminlte/plugins/datepicker/datepicker3.min.css" rel="stylesheet" type="text/css" />
<script src="<?=base_url()?>assets/pad/js/bootstrap-datepicker.js"></script>  


 

<? endif; ?>

</body>

