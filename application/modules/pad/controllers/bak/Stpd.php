<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class stpd extends CI_Controller
{
	private $module = 'penagihan';
	private $controller = 'stpd';

    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }

        $this->load->model(array(
            'apps_model', 'sptpd_model', 'stpd_model',
        ));

		$this->load->helper(active_module());
    }

	function load_auth() {
        $this->load->library('module_auth', array('module' => $this->module));
    }

    public function index()
    {
		$this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $data['current']    = $this->module;
        $data['controller'] = $this->controller;
        $data['apps']       = $this->apps_model->get_active_only();

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

        $select_data = $this->load->model('pegawai_model')->get_select();
        $options     = array();
        if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }}
        $options[999] = 'TIDAK ADA';
        $js                       = 'id="petugas_id" class="input-large" ';
        $data['select_petugas'] = form_dropdown('petugas_id', $options, 999, $js);

        $js                       = 'id="pejabat_id" class="input-large" ';
        $data['select_pejabat'] = form_dropdown('pejabat_id', $options, 999, $js);

		$this->load->view(active_module().'/vstpd', $data);
    }

    function grid()
    {
        /*
        base querynya:
        select s.jatuhtempotgl, 
        (date_part('year',age(now(),s.jatuhtempotgl) )*12+date_part('month',age(now(),s.jatuhtempotgl))+1) as bulan,
        least(date_part('year',age(now(),s.jatuhtempotgl) )*12+date_part('month',age(now(),s.jatuhtempotgl))+1,24) as maks_bulan,
        least(date_part('year',age(now(),s.jatuhtempotgl) )*12+date_part('month',age(now(),s.jatuhtempotgl))+1,24) * s.pajak_terhutang * (select pad_bunga from pad_pemda where enabled=1 limit 1) as bunga,
        least(date_part('year',age(now(),s.jatuhtempotgl) )*12+date_part('month',age(now(),s.jatuhtempotgl))+1,24) * (select pad_bunga*100 from pad_pemda where enabled=1 limit 1) as persen

        from pad_spt s
        where s.jatuhtempotgl<now()
        and (date_part('year',age(now(),s.jatuhtempotgl) )*12+date_part('month',age(now(),s.jatuhtempotgl))+1)>24
        limit 10
        */

        $proses_id   = $this->uri->segment(4);
        $tahun = $this->uri->segment(5);
        $bulan  = $this->uri->segment(6);
        
        $date1 = '01-'.$bulan.'-'.$tahun ;
        if ($bulan=='02')
        $date2 = '28-'.$bulan.'-'.$tahun ;
        else
        $date2 = '30-'.$bulan.'-'.$tahun ;
        $this->load->library('Datatables');
	

			$this->datatables->select("ss.id,sp.id spt_id,  get_stpno(ss.id) as stpdno, ss.stpdtgl, 
                get_sptno(sp.id::int) as sptno, st.typenm, get_nopd(cu.id, true) as nopd, c.nama as customernm, 
                p.nama as pajaknm, get_bulan(extract(month from sp.masadari)::int, true)||extract(year from sp.masadari) 
                as masa, sp.jatuhtempotgl, sp.dasar, ss.bunga, case when k.id is null then get_sptno(sp.id::int) else 
                get_kohirno(sp.id::int) end as nomor_spt, sp.pajak_terhutang, cu.usaha_id, sp.type_id, ss.bunga, 
                case when sp.cara_bayar = 2 then 'TLR' when sp.cara_bayar = 1 then 'TRF' END as value,get_npwpd(c.id, true) 
                as npwpd", false);
			$this->datatables->from('pad_stpd ss');
			$this->datatables->join('pad_spt sp', "sp.id=ss.spt_id", 'right');
            $this->datatables->join('pad_kohir k', 'k.spt_id=sp.id', 'left');
			$this->datatables->join('pad_customer_usaha cu', 'sp.customer_usaha_id=cu.id');
			$this->datatables->join('pad_customer c', 'cu.customer_id=c.id');
			$this->datatables->join('pad_usaha u', 'cu.usaha_id=u.id');
			$this->datatables->join('pad_spt_type st', 'st.id=sp.type_id');
            $this->datatables->join('pad_jenis_pajak p', 'sp.pajak_id=p.id', 'left');
        //$this->datatables->where('periode', "$periode" );
		
		if ($proses_id == 2) {
			$this->datatables->where('ss.id is not NULL');
            //$this->datatables->where('ss.tahun',$tahun);
			//$this->datatables->where('ss.jatuhtempotgl >= date(now())');
		} else {
			// $this->datatables->where('stp.stpdno is NULL');
			$this->datatables->where('ss.id is NULL');
			$this->datatables->where('sp.dasar > 0');
            //menampilkan skpd yg sudah diproses saja
            $this->datatables->where('sp.jatuhtempotgl <= date(now())');
		}
        if ($tahun && $bulan){
        $this->datatables->where("sp.masadari BETWEEN '".$date1."' AND '".$date2."'");
        }
						
        
		
        $this->datatables->rupiah_column('11,12');
		$this->datatables->date_column('3,10');

        echo $this->datatables->generate();
    }

        //admin
    public function cek_bunga()
    {
        $this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
                       
        //$inv_id    = $this->uri->segment(4);
        $spt_id    = $this->uri->segment(4);
        $prosestgl = $this->uri->segment(5);

        /*
        $query= $this->db->query("select source_id from pad_invoice where id=$inv_id and source_nama='pad_spt'");
        foreach ($query->result() as $row){
                    $spt_id = $row->source_id;
        }
        */
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {         
            //koreksi dari AA supaya pajak_terhutang - bunga (menghindari bunga berbunga)
            $pajak = (int)$sptpd->pajak_terhutang;
            $pokok = $pajak - (int)$sptpd->bunga;

            $th_jtp = (int)date('Y', strtotime($sptpd->jatuhtempotgl));
            $th_byr = (int)date('Y', strtotime($prosestgl));
            $bln_jtp = (int)date('m', strtotime($sptpd->jatuhtempotgl));
            $bln_byr = (int)date('m', strtotime($prosestgl));

            
            $x = ($th_byr  - $th_jtp) * 12 ;                                           
            $y = ($bln_byr - $bln_jtp);                                               
            $bln_tunggak = $x + $y;

            if($bln_tunggak > 24){
                $bln_tunggak = 24;
            }else if($bln_tunggak < 1){
                $bln_tunggak = 0;
            }                                                                                                                        
            $denda = $pokok * ((pad_bunga()/100)*$bln_tunggak);                                              

            $nbln  = $bln_tunggak;

            $stpd_data = new stdClass();
            if($denda>0) {
                /*
                $msg = "Pembayaran telah melewati jatuh tempo!\n";
                $msg.= "# Pajak : Rp. ".number_format($pokok,0,',','.')."\n";
                $msg.= "# Telat : ".$nbln." bulan\n";
                $msg.= "# Bunga : Rp. ".number_format($bunga,0,',','.')."\n";
                $msg.= "# TOTAL : Rp. ".number_format($jml_bayar,0,',','.')."\n";
                echo $msg;
                */
                $stpd_data->pokok = $pokok;
                $stpd_data->bunga = $denda;
                $this->session->set_userdata('denda',$denda);
                $stpd_data->terlambat = $nbln;
                $stpd_data->jatuhtempotgl = $sptpd->jatuhtempotgl;

                echo json_encode($stpd_data);
            } else{
                echo "nobunga";
                $this->session->set_userdata('denda',0);
            }
        } else echo "hmm";
    }

    //admin
	public function proses_stpd()
	{
		$this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }

        $spt_id   = $this->uri->segment(4);
        $petugas_id = $this->uri->segment(5);
        $pejabat_id = $this->uri->segment(6);


        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {			
			// hitung bunga (telat bayar)
			$tgl_proses = new DateTime(Date('Y-m-d'));
			$tgl_jtempo = new DateTime(Date('Y-m-d', strtotime($sptpd->jatuhtempotgl)));
			$new_tgl_jtempo = $tgl_jtempo;
            			
			$diff = $tgl_proses->diff($tgl_jtempo);
			$nbln = (($diff->format('%y') * 12) + $diff->format('%m'));
			$nbln = $tgl_proses <= $tgl_jtempo ? 0 : $nbln;
			$nbln = $nbln > 24 ? 24 : $nbln;
            
			$pajak = $sptpd->pajak_terhutang; 
			$bunga = (float)$pajak * (int)$nbln * (float)pad_bunga() + (float)$sptpd->bunga;
			// end hitung bunga
			
            $usaha_id = $this->load->model('pad_model')->sptpd_get_usaha_id($sptpd->customer_usaha_id);
            $jth_tempo = $this->load->model('pajak_model')->get_jatuhtempo($usaha_id);
            
            if($nbln > 0) {
                if($jth_tempo > 0) {
                    $new_tgl_jtempo = $tgl_jtempo->modify("+{$nbln} month");
                } else {
                    $xbln = 2 + (int)$nbln;
                    $new_tgl_jtempo = $tgl_jtempo->modify("+{$xbln} month");
                    $new_tgl_jtempo = $new_tgl_jtempo->modify("-1 day");
                }
            } else {
                if($jth_tempo > 0) {
                    $new_tgl_jtempo = $tgl_jtempo->modify("+30 day");
                } else {
                    $xbln = 2;
                    $new_tgl_jtempo = $tgl_jtempo->modify("+{$xbln} month");
                    $new_tgl_jtempo = $new_tgl_jtempo->modify("-1 day");
                }
            }
            
            while ($new_tgl_jtempo <= $tgl_proses) {
                if($jth_tempo > 0) {
                    $new_tgl_jtempo = $tgl_jtempo->modify("+30 day");
                } else {
                    $xbln = 2;
                    $new_tgl_jtempo = $tgl_jtempo->modify("+{$xbln} month");
                    $new_tgl_jtempo = $new_tgl_jtempo->modify("-1 day");
                }
            }
            
			$stpdno = $this->stpd_model->generate_stpdno(pad_tahun_anggaran());
			$update_data = array (
				'stpdno' => $stpdno,
				'tahun' => pad_tahun_anggaran(),
				'stpdtgl' => date_format($tgl_proses, 'Y-m-d'), //date('Y-m-d'),
				'jatuhtempotgl' => date_format($new_tgl_jtempo, 'Y-m-d'),  //date('Y-m-d', $new_tgl_jtempo),
				'stpdcount' => $nbln+1,
				'bunga' =>  $this->session->userdata('denda'),
                'spt_id'=> $spt_id,
                'petugas_id' => $petugas_id,
                'pejabat_id' => $pejabat_id,
				'enabled' => 1,
				'created' => date('Y-m-d h:i:s'),
				'create_uid' => sipkd_user_id(),
			);
            $this->stpd_model->save($update_data);
            //$this->db->query("update pad_invoice set status_bayar = 3 where id=$spt_id");
            //$this->db->query("update pad_invoice set status_bayar = 2 where id=$spt_id");

			echo "ok";
            $this->session->set_userdata('denda',0);
		} else { echo "hmm";  $this->session->set_userdata('denda',0); }
	}
	
	public function batal_stpd()
	{
		$this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
		/*
        $spt_id   = $this->uri->segment(4);
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {
            $this->stpd_model->delete_by_spt($spt_id);
			echo "ok";
		} else echo "hmm";
        */
        $stp_id   = $this->uri->segment(4);
        if ($stp_id && $stpd = $this->stpd_model->get($stp_id)) {
            $this->stpd_model->delete($stp_id);
			echo "ok";
		} else echo "hmm";
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
					
		$stpd  = $this->load->model('stpd_model')->get($this->input->get('id'));
		$sptpd  = $this->load->model('sptpd_model')->get($stpd->spt_id);
		$cu = $this->load->model('objek_pajak_model')->get($sptpd->customer_usaha_id);
        $report = $this->load->model('report_model')->get_report($cu->usaha_id, $sptpd->type_id);
        $bayar = $sptpd->pajak_terhutang+$stpd->bunga;
        
		switch ($this->input->get('rpt')) {
			case 'stpd':
                $rpt = $report->stpnm;
				$params = array(
					'stp_id' => intval($stpd->id),
				);
				break;
		}
        
        $params = array_merge($params, array(
            "alamat_lengkap" => pad_pemda_alamat_lengkap(),
            "terbilang" => strtoupper(terbilang($stpd->bunga)),
            
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
