 <?$this->load->view('_head'); ?>

 <!-- $this->load->view('_navbar'); ?-->
<link href="<?=base_url()?>assets/css/dashboard.css" rel="stylesheet" type="text/css" />
  
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" type="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Pajak Daerah Non PBB BPHTB</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <form class="navbar-form navbar-right">
        <div class="form-group">
          <input class="form-control" type="text" placeholder="Email">
        </div>
        <div class="form-group">
          <input class="form-control" type="password" placeholder="Password">
        </div>
        <button class="btn btn-success" type="submit">Sign in</button>
      </form>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <!--
    <div class="col-md-2 sidebar">
      <ul class="nav nav-sidebar">
        <li class="active">
          <a href="#">Overview</a>
          <span class="sr-only">(current)</span>
        </li>
        <li>
          <a href="#">Reports</a>
        </li>
        <li>
          <a href="#">Analytics</a>
        </li>        
    </div>
    
    col-sm-offset-3 
    -->
    <div class="col-sm-9 col-md-10 col-md-offset-1 main">
      <!--
      <h1 class="page-header">Dashboard</h1>
      <div class="row placeholders">
        <div class="col-xs-6 col-sm-3 placeholder">
          <img class="img-responsive" width="200" height="200" alt="Generic placeholder thumbnail" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==">
          <h4>Label</h4>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <img class="img-responsive" width="200" height="200" alt="Generic placeholder thumbnail" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==">
          <h4>Label</h4>
        </div>        
        <div class="col-xs-6 col-sm-3 placeholder">
          <img class="img-responsive" width="200" height="200" alt="Generic placeholder thumbnail" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==">
          <h4>Label</h4>
        </div>        
        <div class="col-xs-6 col-sm-3 placeholder">
          <img class="img-responsive" width="200" height="200" alt="Generic placeholder thumbnail" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==">
          <h4>Label</h4>
        </div>
      </div>
      -->
      <h2 class="sub-header">Detail Transaksi</h2>
      <div class="container">
        <div class="row">
          <div class="col-md-4 hidden-xs hidden-md">
            <div class="panel panel-warning">
              <div class="panel-heading">
                  <h3 class="panel-title">Perhitungan</h3>
              </div>
              <div class="panel-body"> 
                <ul class="list-group">
                  <li class="list-group-item list-group-item-success">
                    <h2>Hari Ini</h2>
                  </li>
                  <li class="list-group-item list-group-item-info">
                    <h2>Minggu Ini</h2>
                  </li>
                  <li class="list-group-item list-group-item-warning">
                    <h2>Bulan Ini</h2>
                  </li>
                  <li class="list-group-item list-group-item-danger">
                    <h2>Tahun Ini</h2>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="panel panel-danger">
              <div class="panel-heading">
                  <h3 class="panel-title">Transaksi</h3>
              </div>
              <div class="panel-body"> 
                <ul class="list-group">
                  <li class="list-group-item list-group-item-success">
                    <h2 align="right"><?=$today_trans?></h2>
                  </li>
                  <li class="list-group-item list-group-item-info">
                    <h2 align="right"><?=$week_trans?></h2>
                  </li>
                  <li class="list-group-item list-group-item-warning">
                    <h2 align="right"><?=$month_trans?></h2>
                  </li>
                  <li class="list-group-item list-group-item-danger">
                    <h2 align="right"><?=$year_trans?></h2>
                  </li>
                </ul>
              </div>
            </div>
          </div>
            
          <div class="col-md-4">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Nominal</h3>
              </div>
              <div class="panel-body"> 
                <ul class="list-group">
                  <li class="list-group-item list-group-item-success">
                    <h2 align="right"><?=$today_amt?></h2>
                  </li>
                  <li class="list-group-item list-group-item-info">
                    <h2 align="right"><?=$week_amt?></h2>
                  </li>
                  <li class="list-group-item list-group-item-warning">
                    <h2 align="right"><?=$month_amt?></h2>
                  </li>
                  <li class="list-group-item list-group-item-danger">
                    <h2 align="right"><?=$year_amt?></h2>
                  </li>
                </ul>
              </div>
            </div>          
          </div>
    </div>
        <div class="row">
          <!--span class="label label-default">Default</span>
          <span class="label label-primary">Primary</span>
          <span class="label label-success">Hari Ini</span>
          <span class="label label-info">Minggu Ini</span>
          <span class="label label-warning">Bulan Ini</span>
          <span class="label label-danger">Tahun Ini</span-->
        </div>
  </div>
</div>
 
<!--    
<div class="jumbotron">
    <p>This is a template for a simple marketing or informational website. It includes a large callout called a jumbotron and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
    <p>
      <a class="btn btn-primary btn-lg" role="button" href="#">Learn more »</a>
    </p>
  </div>
    -->


  <hr>
</div>
 
  <script>
  /*$(document).ready(function(){
      $('.isotope-container').isotope({ filter: $('input[name=dashboardview]:checked').val() });
      $('input[name=dashboardview]').change(function(){
          var base = this;
          setTimeout(function(){
              $('.isotope-container').isotope({filter: $(base).val()});},500);
      });
  });*/
  </script>  
  <? $this->load->view('_foot'); ?>