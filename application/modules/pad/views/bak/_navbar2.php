
<?if($this->session->userdata('login')) :?>
<!-- Left side column. contains the logo and sidebar -->
<i class="fa fa-refresh fa-spin"></i>
<aside class="main-sidebar">   
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>
    <li class="treeview">
      <a href="#">
        <i class="fa fa-pencil"></i><span>PENDAFTARAN </span>

        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="treeview-menu">
        <li><a href="<?=active_module_url('subjek_pajak');?>">
         <i class="fa fa-circle-o"></i>Wajib Pajak</a></li>
         <li><a href="<?=active_module_url('objek_pajak');?>">
             <i class="fa fa-circle-o"></i>Objek Pajak</a></li>
          <!--   
         <li><a href="<?=active_module_url('potensi');?>">
             <i class="fa fa-circle-o text-warning" ></i>Potensi</a></li>
             -->
         <li><a href="<?=active_module_url('lap_pendaftaran');?>">
             <i class="fa  fa-file-text-o" ></i>Laporan</a></li>
             </ul>
      </li>
      
      <li class="treeview">
        <a href="#">
          <i class="fa fa-folder-open"></i> <span>PENDATAAN</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_reklame_id');?>">
            <i class="fa fa-photo"></i>Reklame</a></li>
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_air_tanah_id');?>">
            <i class="fa fa-support"></i>Air Tanah</a></li>
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_hotel_id');?>">
            <i class="fa fa-hospital-o"></i>Hotel</a></li>
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_restauran_id');?>">
            <i class="fa fa-cutlery"></i>Restauran</a></li>
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_hiburan_id');?>">
            <i class="fa fa-film"></i>Hiburan</a></li>
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_parkir_id');?>">
            <i class="fa fa-car"></i>Parkir</a></li>
        <li><a href="<?=active_module_url('sptpd/index');?><?echo $this->session->userdata('pad_ppj_id');?>">
            <i class="fa fa-flash"></i>PPJ</a></li>
        <li><a href="<?=active_module_url('teguran');?>">
            <i class="fa fa-circle-o"></i>Teguran</a></li>
         <li><a href="<?=active_module_url('lap_pendataan');?>">
            <i class="fa  fa-file-text-o"></i>Laporan</a></li>
       </ul>

       

     </li>

           <li class="treeview">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>PENETAPAN</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li><a href="<?=active_module_url('sptpd_all');?>">SKPD</a></li>
              <li><a href="<?=active_module_url('skpdj');?>">SKPD JABATAN</a></li>
              <li><a href="<?=active_module_url('skpd');?>">SKPD-KB</a></li>
              <li><a href="<?=active_module_url('skpd');?>">SKPD-KBT</a></li>
              <li><a href="<?=active_module_url('stpd');?>">STPD</a></li>
              <li><a href="<?=active_module_url('kartu_data');?>">Kartu Data</a></li>
              <li><a href="<?=active_module_url('lap_penetapan');?>">
                  <i class="fa fa-file-text-o"></i>Laporan</a></li>
            </ul>
          </li>


          <li class="treeview">
            <a href="#">
              <i class="fa fa-check-square-o"></i>
              <span>PEMERIKSAAN</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li><a href="<?=active_module_url('sptpd_all');?>">HASIL PEMERIKSAAN</a></li>
              <li><a href="<?=active_module_url('lap_penetapan');?>">
                  <i class="fa fa-file-text-o"></i>Laporan</a></li>
            </ul>
          </li>

          <li class="treeview">
            <a href="#">
              <i class="fa fa-envelope-o"></i> <span>PENAGIHAN</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
             <li><a href="<?=active_module_url('stpd');?>">MONITORING TAGIHAN</a></li>
             <li><a href="<?=active_module_url('sptpd_tegur');?>">Teguran SPTPD</a></li>
             <li><a href="<?=active_module_url('lap_penagihan');?>">
                <i class="fa fa-file-text-o"></i>Laporan</a></li>
           </ul>
         </li>

         <li class="treeview">
            <a href="#">
              <i class="fa fa-money"></i> <span>PENERIMAAN</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li><a href="<?=active_module_url('sspd');?>">SSPD</a></li>
              <!--li><a href="<?=active_module_url('penerimaan');?>">Penerimaan</a></li-->
              <li><a href="<?=active_module_url('lap_penerimaan');?>">
                  <i class="fa fa-file-text-o"></i>Laporan</a></li>
            </ul>
          </li>

          <li class="treeview">
          <a href="#">
            <i class="fa fa-paperclip"></i> <span>PELAYANAN</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?=active_module_url('cicilan');?>">Cicilan</a></li>
            <li><a href="<?=active_module_url('keberatan');?>">Keberatan</a></li>
            <li><a href="<?=active_module_url('pembetulan');?>">Pembetulan</a></li>
            <li><a href="<?=active_module_url('penundaan');?>">Penundaan</a></li>
            <li><a href="<?=active_module_url('penghapusan');?>">Penghapusan / Pembatalan</a></li>
            <li><a href="<?=active_module_url('pengembalian');?>">Pengembalian Kelebihan</a></li>
          </ul>
         </li>

         <li class="treeview">
          <a href="#">
            <i class="fa fa-suitcase"></i> <span>REFERENSI</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?=active_module_url('pegawai');?>">Pegawai</a></li>
            <li><a href="<?=active_module_url('anggaran');?>">Anggaran</a></li>
            <li><a href="<?=active_module_url('rekening');?>">Rekening</a></li>
            <li><a href="<?=active_module_url('usaha');?>">Jenis Usaha</a></li>
            <li><a href="<?=active_module_url('pajak');?>">Jenis Pajak</a></li>
            <li><a href="<?=active_module_url('kecamatan');?>">Kecamatan</a></li>
            <li><a href="<?=active_module_url('kelurahan');?>">Kelurahan</a></li>
            <li><a href="#"><i class="fa fa-circle-o"></i> Reklame <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
               <li><a href="<?=active_module_url('reklames');?>">Master Data Reklame</a></li>
               <li><a href="<?=active_module_url('jalan_kelas');?>">Kelas Jalan</a></li>
               <li><a href="<?=active_module_url('jalan');?>">Nama Jalan</a></li>
               <li><a href="<?=active_module_url('reklame_nilai_strategis');?>">Nilai Strategis</a></li>
               <li><a href="<?=active_module_url('reklame_sudut_pandang');?>">Sudut Pandang</a></li>
               <li><a href="<?=active_module_url('reklame_lokasi_pasang');?>">Lokasi Pasang</a></li>
               <li><a href="<?=active_module_url('reklame_mall');?>">Daftar Mall</a></li>
             </ul>
           </li>

           <li><a href="#"><i class="fa fa-circle-o"></i> Air Tanah <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li><a href="<?=active_module_url('air_manfaat');?>">Manfaat</a></li>
              <li><a href="<?=active_module_url('air_zona');?>">Zona</a></li>
              <li><a href="<?=active_module_url('air_hda');?>">HDA</a></li>
            </ul>
          </li>
        </ul>
      </li>
          
      <li class="header">SETTING</li>
      <li><a href="<?=active_module_url('pemda');?>">
          <i class="fa fa-circle-o text-danger"></i>PAD Module</a></li>
    </ul>
  </section>

</aside>
<div class="content-wrapper">
<!--
 <link href="<?=base_url()?>assets/adminlte/plugins/datepicker/datepicker3.min.css" rel="stylesheet" type="text/css" />
 <script src="<?=base_url()?>assets/adminlte/plugins/datepicker/bootstrap-datepicker.min.js"></script> 
-->

 <link href="<?=base_url()?>assets/adminlte/plugins/datepicker/datepicker3.min.css" rel="stylesheet" type="text/css" />
<script src="<?=base_url()?>assets/pad/js/bootstrap-datepicker.js"></script>  

 

<? endif; ?>

</body>