<script>
function initMenu(param) {
      $.ajax({
           type: "POST",
           url: '<? echo base_url(); ?>pad/menu/'+param,
           data:{action:'call_this'},
      });
 }
</script>

  <ul class="sidebar-nav">
    <!--li class="sidebar-brand">
      <a href="#">opensSIPKD-NPB</a>
    </li-->

    <li>
      <a href="<?=active_module_url('jop');?>">Jurnal Objek Pajak</a>
    </li>

    <li>
      <a href="<?=active_module_url('jinvoice');?>">Jurnal Penetapan</a>
    </li>
    <li>
      <a href="<?=active_module_url('jsspd');?>">Jurnal Realisasi</a>
    </li>

    <li>
      <!--a href="<?=active_module_url('pemda');?>">PAD Module</a-->
      <a href="#">PAD Module</a>
    </li>
  </ul>
