<?if(is_login()) :?>
<!-- Left side column. contains the logo and sidebar -->
    <div class="col-sm-3 col-md-2 sidebar">
      <ul class="nav nav-sidebar">
        <li class="active">
          <a href="#">ADMIN</a>
          <span class="sr-only">(current)</span>
        </li>
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

        
        <li>
          <a href="#">Reports</a>
        </li>
        <li>
          <a href="#">Analytics</a>
        </li>        
    </div>
<? endif; ?>
