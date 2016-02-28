<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SIMPAD | LogIn</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link href="<?=base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />  
  <link href="<?=base_url()?>assets/adminlte/dist/css/font-awesome.min.css" rel="stylesheet" type="text/css" />  
  <link href="<?=base_url()?>assets/adminlte/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
      <img src="<?=app_img_logo('assets/img/logo-kota-tangerang.png')?>" alt="logo" style="max-height:70px;">
        <br> <b>SIMPAD</b>
        <p style="font-size: 14px">Sistem Informasi Pendapatan Daerah</p>
        <p style="font-size: 22px"><?=LICENSE_TO?></p>
      </div>
      <div class="login-box-body">
        <p class="login-box-msg">Silakan Login untuk memulai</p>
        <?php echo form_open('login', array('id'=>'frmlogin', 'class'=>'form-horizontal'));?>
        <?=msg_block();?>
          <div class="col-xs-12">  
            <div class="form-group has-feedback">
              <input type="text" class="form-control" name="userid" placeholder="User ID" required autocomplete="off"/>
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
              <input type="password" class="form-control" name="passwd" placeholder="Password" required autocomplete="off"/>
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
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
  </div>
</body>
<script src="<?=base_url()?>assets/adminlte/plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script>
  var timer;
  var wait=10;
  document.onkeypress=resetTimer;
  document.onmousemove=resetTimer;

  function resetTimer() {
    clearTimeout(timer);
    timer=setTimeout("logout()", 60000*wait);
  }

  function logout() {
      //window.location.href='<?=base_url()?>logout';
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
  
  

  <?php $this->session->sess_destroy();?>

  <footer>
    <div class="container-fluid" style="position:fixed; right:0px; bottom:0 ">
    <!--
      <p class="muted credit"><strong><a href="http://opensipkd.com">&copy; OpenSIPKD 2013 </a></strong> </p>
      -->
    </div>
  </footer>

</body>
</html>
