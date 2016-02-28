<? $this->load->view('_head'); ?>
<? //$this->load->view('_navbar'); ?>

<style>
      /* Override some defaults */
      html, body {
        background-color: #eee;
      }
      body {
        padding-top: 80px; 
      }
      .container {
        width: 600px;
      }

      /* The white background content wrapper */
      .container > .content {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px; 
        -webkit-border-radius: 10px 10px 10px 10px;
           -moz-border-radius: 10px 10px 10px 10px;
                border-radius: 10px 10px 10px 10px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }

	   .alert {
		margin-right: 45px;
	   }
	   
	  .login-form {
		margin-left: 65px;
	  }
	
	  legend {
		margin-right: -50px;
		font-weight: bold;
	  	color: #404040;
	  }
</style>

<div class="container">
    <div class="content">
      <div class="row">
        <div class="login-form">
		  <img src="<?=base_url()?>assets/img/logo-banjar-login.png" style="width:520px;">
		  <p>&nbsp;</p>
		  <?=msg_block();?>
		  <p>Silahkan masukan User ID dan Password.</p>
		  <?php echo form_open('login', array('id'=>'frmlogin', 'class'=>'form-horizontal'));?>
            <fieldset>
				<div class="control-group">
					<label class="control-label" for="userid">User ID</label>
					<div class="controls"> 
						<div class="input-prepend">
							<span class="add-on"><i class="icon-user"></i></span>
							<input type="text" name="userid" placeholder="User ID" autocomplete="off" />
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="passwd">Password</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><i class="icon-lock"></i></span>
							<input type="password" name="passwd" placeholder="Password" autocomplete="off" />
						</div>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Sign in</button>
					</div>
				</div>
          </form>
        </div>
      </div>
    </div>
  </div> <!-- /container -->

<? $this->session->sess_destroy();?>
<? $this->load->view('_foot'); ?>