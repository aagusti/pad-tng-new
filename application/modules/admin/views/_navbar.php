
<?if(is_login()) :?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">

		<ul class="sidebar-menu">
			<li class="header">MAIN NAVIGATION</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-gears"></i> <span>PENGATURAN</span>
					<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li><a href="<?=active_module_url('apps');?>"><i class="fa fa-laptop"></i>Aplikasi</a></li>

				</ul>
			</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-th"></i>
					<span>User &amp; Privileges</span>
					<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">

					<li><a href="<?=active_module_url('users');?>"><i class="fa fa-user"></i>Users</a></li>
					<li><a href="<?=active_module_url('groups');?>"><i class="fa fa-users"></i>Group Users</a></li>
					<li><a href="<?=active_module_url('privileges');?>"><i class="fa fa-circle-o"></i>Group Privileges</a></li>
				</ul>
			</li>

		</ul>
	</section>
	<!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<link href="<?=base_url()?>assets/adminlte/plugins/datepicker/datepicker3.min.css" rel="stylesheet" type="text/css" />
	<script src="<?=base_url()?>assets/adminlte/plugins/datepicker/bootstrap-datepicker.min.js"></script>	

<? endif; ?>
