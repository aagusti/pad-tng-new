<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SIMPAD | LogIn</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link href="<?=base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />  
  <link rel="stylesheet" href="<?=base_url()?>assets/css/signin.css">
  <script src="<?=base_url()?>assets/jquery/jquery-1.12.1.min.js" type="text/javascript"></script>
  <script src="<?=base_url()?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</head>
<body>
  <div class="container-fluid"> 
    <?php echo form_open('login', array('id'=>'frmlogin', 'class'=>'form-signin'));?>
      <center>
        <img src="<?=app_img_logo('assets/img/logo-kota-tangerang.png')?>" alt="logo" style="max-height:70px;">
        <h1>SIMPAD</h1>
        <p style="font-size: 14px">Sistem Informasi Pendapatan Daerah</p>
        <p style="font-size: 22px"><?=LICENSE_TO?></p>
        <p class="login-box-msg">Silakan Login untuk memulai</p>
          <?=msg_block();?>
      </center>
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="userid" placeholder="User ID" required autocomplete="off"/>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="passwd" placeholder="Password" required autocomplete="off"/>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" value="remember-me">Remember me
        </label>
      </div>
      <div class="row">
          <div class="col-xs-6">
            <!--    
            <button href="pad_ereg/daftar_wajib_pajak_online" style="width:200px;" 
            class="btn btn-success btn-block btn-flat">Daftar Online (e-Registrasi)</button>
            -->
          </div>
          <div class="col-xs-6">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Log In</button>
          </div>
        </div>

        

  </div>
  <?php $this->session->sess_destroy();?>

  <footer>
    <div class="container-fluid" style="position:fixed; right:0px; bottom:0 ">
    <!--
      <p class="muted credit"><strong><a href="http://opensipkd.com">&copy; OpenSIPKD 2013 </a></strong> </p>
      -->
    </div>
  </footer>

</body>
  


</body>
</html>
