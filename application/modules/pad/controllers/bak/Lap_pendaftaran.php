<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class lap_pendaftaran extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
        $module = 'lap_pendaftaran';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model'
        ));
		
		$this->load->helper(active_module());
		$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
    }
    
    public function index() 
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $data['current'] = 'pendaftaran';
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
		$js = 'id="kecid" class="input-xlarge" required ';
		$data['select_kecamatan'] = form_dropdown('kecid', $options, null, $js);
		
        $this->load->view('vlap_pendaftaran', $data);
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
		
		$kondisi = '';
        $status = $this->input->get('kondisi');
		if ($status!='') 
			$kondisi = "AND cd.customer_status_id={$status}";
			
		$rpt = $this->input->get('rpt');
		switch ($rpt) {
			case 'daf_induk_wp':
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_induk_wp_perjenis':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'usahaid' => (int) $this->input->get('usahaid'),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_induk_wp_perkec':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'kecid' => (int) $this->input->get('kecid'),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_wp_baru':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_wp_baru_jenis':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'usahaid' => (int) $this->input->get('usahaid'),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_wp_baru_kec':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'kecid' => (int) $this->input->get('kecid'),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_wp_jenis_perkec':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'usahaid' => (int) $this->input->get('usahaid'),
					'kondisi' => $kondisi,
				);
				break;
				
			case 'daf_wp_tutup':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
				);
				break;
				
			case 'daf_wp_ijin_berakhir':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
				);
				break;
				
			case 'daf_rekap_kec':					
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
				);
				break;
				
				
		}
        
        $tambahan = array(
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),       
        );
        $params = array_merge($params, $tambahan);
        
		$rpt = 'pendaftaran/'.$rpt;
		// var_dump($params);
		// die;
		

		$jasper = $this->load->library('Jasper');

		//IF PDF
		if($type=='pdf'){
			echo $jasper->cetak($rpt, $params, $type, false);
		}
		//IF HTML, convert ke excel
		else if($type=='html'){
		$assetpath = 'assets/file';
		$tmp = $assetpath.'/tmp/report'.sipkd_user_id().'.html';

		if (is_file($tmp))
		{
		    unlink($tmp);
		}

		ob_start();
		echo $jasper->cetak($rpt, $params, $type, false);
		echo '1';
		file_put_contents($tmp, ob_get_contents());

		$objPHPExcel = new PHPExcel();
		$inputFileType = 'HTML';
		$inputFileName =  $tmp;
		$outputFileType = 'Excel2007';
		$outputFileName = $assetpath.'/Report.xls';
		$filename = $rpt.date("d-m-Y").".xls";

		$objPHPExcelReader = IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);
	
		ini_set('zlib.output_compression','Off'); 
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		//the folowing two lines make sure it is saved as a xls file
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$filename);
						//simpan dalam file sample.xls
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');                
		$objWriter->save('php://output');
		}


	}
}
