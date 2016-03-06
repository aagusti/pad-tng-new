<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class monitoring_tagihan extends CI_Controller {
	private $module = 'pendataan';
	private $controller = 'monitoring_tagihan';
    
    function __construct() {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
		if ($this->uri->segment(2) == 'skpdj') {
			$this->module = 'penetapan';
			$this->controller = 'skpdj';
		}
        
        $this->load->model(array(
            'apps_model', 'subjek_pajak_model'
        ));
		
		$this->load->helper(active_module('pad_helper'));
    }
    
	function load_auth() {
        $this->load->library('module_auth', array('module' => $this->module));
    }
    
    public function index() {
		$this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        $month = date('m');
        $options = array(
            '01' => 'JANUARI',
            '02' => 'FEBRUARI',
            '03' => 'MARET',
            '04' => 'APRIL',
            '05' => 'MEI',
            '06' => 'JUNI',
            '07' => 'JULI',
            '08' => 'AGUSTUS',
            '09' => 'SEPTEMBER',
            '10' => 'OKTOBER',
            '11' => 'NOVEMBER',
            '12' => 'DESEMBER',
        );
        $js                   = 'id="bulan_id" class="input-medium"';
		$select = form_dropdown('usaha_id', $options, $month, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);;
        $data['select_bulan'] = $select;
        
        $options = array(
            '1' => 'BELUM PROSES',
            '2' => 'SUDAH PROSES',
        );
        $js = 'id="proses_id" class="input-medium"';
        $select = form_dropdown('proses_id', $options, 1, $js);
        $select = preg_replace("/[\r\n]+/", "", $select);
        $data['select_proses_id'] = $select;
        
        $data['current']    = $this->module;
        $data['controller'] = $this->controller;
        $data['apps']    = $this->apps_model->get_active_only();
        if($this->controller != 'skpdj')
            $data['judul']   = "PENDATAAN - DAFTAR SPTPD BELUM MASUK (MASA PAJAK ".strtoupper($this->bulan(date('m')-1)).")";
        else
            $data['judul']   = "PENETAPAN - SKPD JABATAN (MASA PAJAK ".strtoupper($this->bulan(date('m')-1)).")";
        $this->load->view('vmonitoring_tagihan', $data);
    }
    
    function grid() {
        $periode  = $this->uri->segment(4);
        
        $this->load->library('Datatables');
                

        $this->datatables->select("s.id as invoice_id, t.teguran_no, t.tanggal, s.nama_wp, s.nama_op, 0 as nopd, jenis_usaha, jenis_pajak,                                                                                                       
                                    get_bulan(right(periode::text,2)::int,true)||' '||left(periode::text,4) as masa,                                                                                                    
                                    s.jatuh_tempo, s.total pajak, t.cetak_ke, t.id as teguran_id, periode" , false);
        $this->datatables->from('pad_invoice s ');
        $this->datatables->join('pad_teguran t', 't.invoice_id = s.id', 'left');
        $this->datatables->where('s.status_bayar', 0);
        $this->datatables->where('s.jatuh_tempo < now()');
        $this->datatables->where('periode', "$periode" );
        $this->datatables->order_by('s.id', 'ASC');
             /*  
            $sort = $this->input->get('sSortDir_0');
            if ($this->input->get('iSortCol_0') == 1) {
                $this->datatables->order_by('npwpd', $sort);
            }
            */
            
		$this->datatables->rupiah_column('10');
        $this->datatables->date_column('9');

        echo $this->datatables->generate();
    }
    
    function proses_cetak($invoice_id){
        $tahun = date('Y');
        $query = $this->db->query("select id, cetak_ke from pad_teguran where invoice_id=$invoice_id");
        foreach ($query->result() as $row)
        {
            $teguran_id =  $row->id;
            $cetak_ke =  $row->cetak_ke;
        }
        if($teguran_id > 0){
            $query = $this->db->query("update pad_teguran set cetak_ke=$cetak_ke+1 where id=$teguran_id");
        }else{
            $query = $this->db->query("insert into pad_teguran (tahun, tanggal, teguran_no, invoice_id, cetak_ke) 
                         values ($tahun, now(), cast(get_noteguran() as integer), $invoice_id, 1)");   
        }
        if($query)
            echo "ok";
        else
            echo "not_ok";                                                                                                                  
    }
    function bulan($bulan) {
        Switch ($bulan){
            case 1 : $bulan="Januari";
                Break;
            case 2 : $bulan="Februari";
                Break;
            case 3 : $bulan="Maret";
                Break;
            case 4 : $bulan="April";
                Break;
            case 5 : $bulan="Mei";
                Break;
            case 6 : $bulan="Juni";
                Break;
            case 7 : $bulan="Juli";
                Break;
            case 8 : $bulan="Agustus";
                Break;
            case 9 : $bulan="September";
                Break;
            case 10 : $bulan="Oktober";
                Break;
            case 11 : $bulan="November";
                Break;
            case 12 : $bulan="Desember";
                Break;
            }
        return $bulan;
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
		$rpt = $this->input->get('rpt');
		$uid = $this->input->get('uid');
		$cuid = $this->input->get('cuid');
		$sptid = $this->input->get('cuid'); // spt_id on skpdj
        
        $kondisi = $uid=='999' ? '' : " and cu.usaha_id={$uid}";
        $kondisi.= $cuid=='' ? '' : " and cu.id={$cuid}";
        
        if($rpt!='skpdj_30') {
            $rpt = $this->module.'/'.$rpt;
            $params = array(
                "kondisi" => $kondisi,
                "tglcetak" => date('Y-m-d'),
                "tahun" => pad_tahun_anggaran(),
            );
        } else {
            $rpt = $rpt;
         
            $sptpd  = $this->load->model('sptpd_model')->get($sptid);
            $cu = $this->load->model('objek_pajak_model')->get($sptpd->customer_usaha_id);
            $report = $this->load->model('report_model')->get_report($cu->usaha_id, $sptpd->type_id);
        
            $rpt = $report->sknm;
            $params = array(
                'spt_id' => intval($sptid),
            );
        }
        
        $params = array_merge($params, array(
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),            
            "logo" => base_url('assets/img/logorpt__.jpg'), 
            "ttd" => base_url('assets/img/ttd.gif'),
        ));
        
		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, false);
	}
}
