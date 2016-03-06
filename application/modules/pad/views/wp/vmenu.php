<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/wp/_navbar'); ?>

<script>
$(document).ready(function() {
	$('#btn_sptpd').click(function() {
		window.location = '<? echo active_module_url('sptpd/add'); ?>';
	});
	$('#btn_sptpd_rek').click(function() {
		window.location = '<? echo active_module_url('sptpd/add/'.pad_reklame_id()); ?>';
	});
	$('#btn_sptpd_air').click(function() {
		window.location = '<? echo active_module_url('sptpd/add/'.pad_air_tanah_id()); ?>';
	});
});
</script>

<div class="content">
	<div class="container-fluid">		
		<?=msg_block();?>
		
		<div class="alert alert-block">
			<p>Selamat datang di SPTPD Online, <strong><?=wp_nm();?></strong>.</p>
			<p>Untuk melihat data transaksi SPTPD Anda klik <a href="<? echo active_module_url('sptpd'); ?>">di sini</a>.</p>
			<p>&nbsp;</p>
		
			<div class="alert alert-success">
				<h4>Pajak Hotel, Restoran, Hiburan, dan Parkir
					<button id="btn_sptpd" class="btn btn-large pull-right">Klik disini</button>
				</h4>
				<p>Pemberitahuan pajak daerah untuk kategori pajak hotel, restoran, hiburan, dan parkir.</p>
			</div>
			<div class="alert alert-error">
				<h4>Pajak Reklame
					<button id="btn_sptpd_rek" class="btn btn-large pull-right disabled" disabled >Klik disini</button>
				</h4>
				<p>Pemberitahuan pajak daerah untuk kategori pajak reklame.</p>
			</div>
			<div class="alert alert-info">
				<h4>Pajak Air Tanah
					<button id="btn_sptpd_air" class="btn btn-large pull-right disabled" disabled >Klik disini</button>
				</h4>
				<p>Pemberitahuan pajak daerah untuk kategori pajak air tanah.</p>
			</div>
		</div>
	</div>
	
</div>
<? $this->load->view('_foot'); ?>