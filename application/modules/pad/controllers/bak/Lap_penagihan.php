<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class lap_penagihan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
        $module = 'lap_penagihan';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model'
        ));
		
		$this->load->helper(active_module());
    }
    
    public function index() 
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $data['current'] = 'penagihan';
        $data['apps']    = $this->apps_model->get_active_only();
			
		$select_data = $this->load->model('usaha_model')->get_select();
		$options     = array();
		if($select_data)
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js = 'id="usahaid" class="input-xlarge" required ';
		$data['select_usaha'] = form_dropdown('usahaid', $options, null, $js);
		
		$select_data = $this->load->model('kecamatan_model')->get_select();
		$options     = array();
		if($select_data)
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js = 'id="kecid" class="input-xlarge" onChange="get_kelurahan(this.value);" required ';
		$data['select_kecamatan'] = form_dropdown('kecid', $options, null, $js);
/* 
		$select_data = $this->load->model('kelurahan_model')->get_select();
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kelurahannm;
		}
		$js = 'id="kelid" class="input-xlarge" required ';
		$data['select_kelurahan'] = form_dropdown('kelid', $options, null, $js);
		 */
		$data['select_kelurahan'] = '';
		
        $this->load->view('vlap_penagihan', $data);
    }
    
	/* report */
	function show_rpt() {		
		$cls_mtd_html = $this->router->fetch_class()."/cetak/html";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf";
		$data['rpt_html'] = active_module_url($cls_mtd_html). '?' . $_SERVER['QUERY_STRING'] ;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf) . '?' . $_SERVER['QUERY_STRING'] ;
        $this->load->view('vjasper_viewer', $data);
	}

	function cetak() {		
		$type = $this->uri->segment(4);

		$qs   = urldecode ($_SERVER['QUERY_STRING']);
		parse_str($qs, $qs_data);
		
		$params = array();
		// foreach ($qs_data as $key => $val)
			// $params[$key] = $val;
		
		$kondisi = $this->input->get('kondisi');
		if (!empty($kondisi))
			$kondisi = 'and s.type_id='.$this->input->get('kondisi');
			
		$rpt = $this->input->get('rpt');
		$ignore_html_pg = TRUE;
		switch ($rpt) {
			case 'tgh_sdh_byr':						
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
				);
				break;	
			case 'tgh_sdh_byr_perkec':						
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'kecamatanid' => (int) $this->input->get('kecamatanid'),
				);
				break;			
			case 'tgh_sdh_byr_perkel':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'kecamatanid' => (int) $this->input->get('kecamatanid'),
					'kelurahanid' => (int) $this->input->get('kelurahanid'),
				);
				break;									
			case 'tgh_sdh_byr_jnskec':				
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'usahaid' => (int) $this->input->get('usahaid'),
					'kecamatanid' => (int) $this->input->get('kecamatanid'),
				);
				break;
			case 'tgh_sdh_byr_jnskel':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'usahaid' => (int) $this->input->get('usahaid'),
					'kecamatanid' => (int) $this->input->get('kecamatanid'),
					'kelurahanid' => (int) $this->input->get('kelurahanid'),
				);
				break;					
			case 'tgh_jatuh_tempo_blm_byr':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'usahaid' => (int) $this->input->get('usahaid'),
				);
				break;
				
			case 'tgh_jatuh_tempo_sdh_byr':		
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'usahaid' => (int) $this->input->get('usahaid'),
				);
				break;							
			case 'tgh_peringkat_byr':		
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
				);
				break;				
			case 'tgh_surat_teguran':	
				$ignore_html_pg = FALSE;
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => (int) $this->input->get('bulan'),
					'tahun' => (int) $this->input->get('tahun'),
					'usahaid' => (int) $this->input->get('usahaid'),
				);
				break;
		}
        $tambahan = array(
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),       
        );
        $params = array_merge($params, $tambahan);
        $ignore_html_pg = FALSE; //paging aja semua
        
		$rpt = 'penagihan/'.$rpt;
		// var_dump($params);
		// die;
		
		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, $ignore_html_pg);
	}
}
