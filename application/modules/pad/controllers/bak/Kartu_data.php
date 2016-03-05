<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class kartu_data extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
        $module = 'penetapan';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model', 'subjek_pajak_model'
        ));
		
		$this->load->helper(active_module('pad_helper'));
    }
    
    public function index() {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $select_data = $this->load->model('usaha_model')->get_select();
        $options     = array();
        if($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                   = 'id="usaha_id" class="input-medium"';
		$options[999] = 'SEMUA PAJAK';
		$select = form_dropdown('usaha_id', $options, 999, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);;
        $data['select_usaha'] = $select;
        
        $data['current'] = 'penetapan';
        $data['controller'] = $this->uri->segment(2);
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vkartu_data', $data);
    }
    
    function grid() {
        $usaha_id = $this->uri->segment(4);
        $terimatgl = $this->uri->segment(5);
        $terimatgl = empty($terimatgl) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl));
        
        $this->load->library('Datatables');
        $this->datatables->select("cu.id, get_npwpd(c.id, true) as npwpd, c.nama, 
            (case when c.wpnama='' then c.pnama else c.wpnama end) as pemiliknm, c.alamat, u.nama as usahanm, s.id as sptid"
            , false);

        $this->datatables->from('pad_invoice s');
        $this->datatables->join('pad_spt sp', "sp.id=s.source_id and s.source_nama = 'pad_spt'", 'left');
        $this->datatables->join('pad_customer_usaha cu', 'cu.id=sp.customer_usaha_id', 'left');
        $this->datatables->join('pad_customer c', 'c.id=cu.customer_id', 'left');
        $this->datatables->join('pad_usaha u', 'u.id=cu.usaha_id', 'left');
		$this->datatables->join('pad_sspd ss', 'ss.invoice_id=s.id', 'left');
        $this->datatables->where('c.rp', 'P');
        
		if($usaha_id <> 999 && !empty($usaha_id))
			$this->datatables->filter('cu.usaha_id', $usaha_id);
            
        if($this->input->get('sSearch') == '')
            $this->datatables->filter('date(ss.sspdtgl)', $terimatgl);
                
        $sort = $this->input->get('sSortDir_0');
        if ($this->input->get('iSortCol_0') == 1) {
            $this->datatables->order_by('npwpd', $sort);
        } else
            $this->datatables->order_by('npwpd', $sort);
            
        echo $this->datatables->generate();
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
					
		$cu = $this->load->model('objek_pajak_model')->get($this->input->get('id'));
        $report = $this->load->model('report_model')->get_report($cu->usaha_id, 0);
        
        //untuk reklame niy
		$sptpd  = $this->load->model('sptpd_model')->get($this->input->get('sid'));
        
		switch ($this->input->get('rpt')) {
			case 'kartudt':
                $rpt = $report->kartudtnm;
				$params = array(
					'cuid' => intval($cu->id),
                    
					'terbilang' =>  strtoupper(terbilang($sptpd->pajak_terhutang)),
					'sptid' => intval($this->input->get('sid')),
				);
				break;
		}
        
        $params = array_merge($params, array(
            "tahun" => pad_tahun_anggaran(),
            
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),            
            "logo" => base_url('assets/img/logorpt__.jpg'),
        ));
        
		$rpt = $rpt;
		// $rpt = $this->module.'/'.$rpt;
		
		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, false);
	}
}
