<?php $this->load->view('_head'); ?>
<div class="container-fluid">
  <div class="row">
    <?php $this->load->view(active_module().'/_navbar'); ?>
    <div class="col-sm-9 col-md-10 col-md-offset-1 main">
      <div class="hero-unit">
        <center>
          <h2>PEMERINTAH <?=LICENSE_TO?></h2>
          <h3><?=LICENSE_TO_SUB?></h3>
          <img src="<?=app_img_logo('assets/img/logo-kota-tangerang.png')?>" alt="logo" style="max-height:250px;">
          <h2>Halaman Administrasi</h2>			
          <P>Module pengaturan Aplikasi OpenSIPKD</P>
          <P><i class="icon-star"></i> SELAMAT BEKERJA <i class="icon-star"></i></P>
        </center>
      </div>
    </div>
  </div>
</div>    

<?php $this->load->view('_foot'); ?>