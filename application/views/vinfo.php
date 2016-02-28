<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<div class="container-fluid">
	<div class="page-header" style="margin-bottom:8px;">
		<strong>:: Info</strong>	
	</div>
</div>

<div class="container-fluid">
	<?=msg_block();?>
</div>	
<div class="container-fluid">
	<a href="<?=base_url();?>">Kembali ke Awal</a>
</div>	

<? $this->load->view('_foot'); ?>