<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class sptpd_tegur extends CI_Controller {
	private $module = 'pendataan';
	private $controller = 'sptpd_tegur';
    
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
        
        $select_data = $this->load->model('usaha_model')->get_without();
        $options     = array();
        if ($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                   = 'id="usaha_id" class="input-medium"';
		$options[999] = 'SEMUA PAJAK';
		$select = form_dropdown('usaha_id', $options, 999, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);;
        $data['select_usaha'] = $select;
        
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
        $this->load->view('vsptpd_tegur', $data);
    }
    
    function grid() {
        $usaha_id  = $this->uri->segment(4);
        $proses_id = $this->uri->segment(5); // on skpdj
        
        $this->load->library('Datatables');
        if($proses_id!=2) {
            $this->datatables->select("cu.id as cu_id, get_npwpd(c.id, true) as npwpd, upper(c.nama) as nama,cu.opnm, 
                (case when c.wpnama='' then c.pnama else c.wpnama end) as namawp, c.alamat, 
                u.nama as usahanm, 
                coalesce((select max(dasar) from pad_spt where customer_usaha_id=cu.id),0) as max_omset,
                kel.nama as kelnm, kec.nama as kecnm, c.id as c_id, cu.usaha_id"
                , false);
            $this->datatables->from('pad_customer c');
            $this->datatables->join('pad_customer_usaha cu', 'cu.customer_id=c.id');
            $this->datatables->join('pad_usaha u', 'u.id=cu.usaha_id');
            $this->datatables->join('pad_kecamatan kec', 'kec.id = cu.kecamatan_id');
            $this->datatables->join('pad_kelurahan kel', 'kel.kecamatan_id = cu.kecamatan_id and kel.id = cu.kelurahan_id');
            $this->datatables->join('pad_jenis_pajak jp', 'jp.id = cu.def_pajak_id'); //kata pa wawan jgn catering ditampilkan
            
            $this->datatables->where('c.rp', 'P');
            $this->datatables->where('cu.customer_status_id', 1);
            //$this->datatables->where('sp.proses', 1);

            $pad_reklame_id  = pad_reklame_id();
            $pad_airtanah_id = pad_air_tanah_id();
            $pad_ppj_id = 5;
            $this->datatables->where("cu.usaha_id not in ({$pad_reklame_id},{$pad_ppj_id})");
            $this->datatables->where("cu.def_pajak_id not in (64)");//kata pa wawan jgn catering ditampilkan
            //$this->datatables->where("extract(month from cu.created)<extract(month from now())");
            //$this->datatables->where("extract(year from cu.created)=extract(year from now())");
            
            $this->datatables->where("(
                  select count(*)
                  from pad.pad_spt s1
                  where s1.customer_usaha_id = cu.id
                  and extract(month from s1.masadari)=extract(month from now()-interval '1 month')
                  and extract(year from s1.masadari)=extract(year from now()-interval '1 month')
                ) < 1");
                
            if($usaha_id <> 999 && !empty($usaha_id))
                $this->datatables->filter('cu.usaha_id', $usaha_id);
                
            $sort = $this->input->get('sSortDir_0');
            if ($this->input->get('iSortCol_0') == 1) {
                $this->datatables->order_by('npwpd', $sort);
            }
        } else {
            $skpdj_type_id = '3'; // <--------- id default
            $skpdj_type = $this->db->query("select id from pad_spt_type where typenm ilike '%skpdj%' limit 1");
            if($skpdj_type->num_rows()!==0)
                $skpdj_type_id = $skpdj_type->row()->id;
                
            $this->datatables->select("s.id as spt_id, get_npwpd(c.id, true) as npwpd, upper(c.nama) as nama, 
                (case when c.wpnama='' then c.pnama else c.wpnama end) as nama, c.alamat, 
                u.nama, 
                coalesce((select max(dasar) from pad_spt where customer_usaha_id=cu.id),0) as max_omset,
                c.id as c_id, cu.usaha_id"
                , false);
            $this->datatables->from('pad_spt s');
            $this->datatables->join('pad_kohir k', 'k.spt_id=s.id');
            $this->datatables->join('pad_customer_usaha cu', 'cu.id=s.customer_usaha_id');
            $this->datatables->join('pad_customer c', 'cu.customer_id=c.id');
            $this->datatables->join('pad_usaha u', 'u.id=cu.usaha_id');
            
            $this->datatables->where('c.rp', 'P');
            $this->datatables->where('cu.customer_status_id', 1);
            
            $pad_reklame_id  = pad_reklame_id();
            $pad_airtanah_id = pad_air_tanah_id();
            $this->datatables->where("cu.usaha_id not in ({$pad_reklame_id}, {$pad_airtanah_id})");
            $this->datatables->where("extract(month from s.masadari)=extract(month from now()-interval '1 month')");
            $this->datatables->where("extract(year from s.masadari)=extract(year from now()-interval '1 month')");
            
            $this->datatables->where("s.type_id", $skpdj_type_id);
                
            if($usaha_id <> 999 && !empty($usaha_id))
                $this->datatables->filter('cu.usaha_id', $usaha_id);
                
            $sort = $this->input->get('sSortDir_0');
            if ($this->input->get('iSortCol_0') == 1) {
                $this->datatables->order_by('npwpd', $sort);
            }
        }
            
		$this->datatables->rupiah_column('7');
        echo $this->datatables->generate();
    }
    
	// SKPD-J
	public function proses_skpdj() {
        $this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
		
        $this->load->model('sptpd_model');
        $this->load->model('skpd_model');
        
        $usaha_id = $this->uri->segment(4);
        $usaha_id = ($usaha_id != 999) ? $usaha_id : '';
        $cu_id    = $this->uri->segment(5);
        
        // cari data yg kena skpbj
        if ($data = $this->skpd_model->get_skpdj_data($usaha_id, $cu_id)) {
            foreach ($data as $row) {
                // cari max omsetnya - kalo ada
                $spt_max = $this->sptpd_model->get_max_omset($row->cu_id);
                if($spt_max->id > 0) {
                    // duplicate datanya
                    $this->skpd_model->proses_skpdj($spt_max->id, $spt_max->omset);
                } 
            }
			echo "ok";
		} else echo "hmm";
	}
    
    function bulan($bulan) {
        Switch ($bulan){
            case 0 : $bulan="Desember";
                Break;
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
                "usahaid" => $uid,
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
