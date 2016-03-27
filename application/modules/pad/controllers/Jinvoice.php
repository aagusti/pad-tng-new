<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jinvoice extends CI_Controller
{
    private $module = 'jurnal';
    private $controller = 'jinvoice';

    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }

        $this->load->model(array(
            'apps_model'
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

        $options = array(
            '0' => 'BLM Posting',
            '1' => 'Posted',
        );
        $this->load->view(active_module().'/vjinvoice', $data);
    }

    function grid()
    {
        $proses_id   = $this->uri->segment(4);
        $validasi_id = $this->uri->segment(5);
        $terimatgl = $this->uri->segment(6);
        $terimatgl2 = $this->uri->segment(7);

        $terimatgl = empty($terimatgl) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl));
        $terimatgl2 = empty($terimatgl2) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl2));


        $this->load->library('Datatables');
        //pad.get_nopd(cu.id, true) as nopd
        $this->datatables->select("s.id, s.nomor_tagihan, s.created as tanggal, 
            s.npwpd, s.nama_op || '/ '||s.nama_wp as customernm, 
            s.rekening_pokok,
            s.pokok,
            s.rekening_denda,
            s.denda 
            ", false);
        $this->datatables->from('pad.pad_invoice as s');
        //$this->datatables->join('pad.pad_customer_usaha cu', 'sp.customer_usaha_id=cu.id', 'left');
        //$this->datatables->join('pad.pad_customer c', 'cu.customer_id=c.id', 'left');
        //$this->datatables->join('pad.pad_usaha u', 'cu.usaha_id=u.id', 'left');

        if ($this->input->get('iSortCol_0') == 1) {
            $sort = $this->input->get('sSortDir_0');
            $this->datatables->order_by('s.sptno', $sort);
        }
        $this->datatables->rupiah_column('6,8');
        $this->datatables->date_column('2');

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
                       
        $inv_id    = $this->uri->segment(4);
        $prosestgl = $this->uri->segment(5);

        $query= $this->db->query("select source_id from pad_invoice where id=$inv_id and source_nama='pad_spt'");
        foreach ($query->result() as $row){
                    $spt_id = $row->source_id;
        }
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {         
            //koreksi dari AA supaya pajak_terhutang - bunga (menghindari bunga berbunga)
            $pajak = (int)$sptpd->pajak_terhutang;
            $pokok = $pajak - (int)$sptpd->bunga;
            
            $denda = hit_denda($pokok, pad_bunga(), $sptpd->jatuhtempotgl, $prosestgl);
            $bunga = round($denda->denda + $sptpd->bunga);
            $nbln  = $denda->bulan;
            $jml_bayar = $pokok + $bunga;
            $sspd_data = new stdClass();
            if($bunga>0) {
                /*
                $msg = "Pembayaran telah melewati jatuh tempo!\n";
                $msg.= "# Pajak : Rp. ".number_format($pokok,0,',','.')."\n";
                $msg.= "# Telat : ".$nbln." bulan\n";
                $msg.= "# Bunga : Rp. ".number_format($bunga,0,',','.')."\n";
                $msg.= "# TOTAL : Rp. ".number_format($jml_bayar,0,',','.')."\n";
                echo $msg;
                */
                $sspd_data->pokok = $pokok;
                $sspd_data->bunga = $bunga;
                $sspd_data->terlambat = $nbln;
                $sspd_data->jatuhtempotgl = $sptpd->jatuhtempotgl;

                echo json_encode($sspd_data);
            } else
                echo "nobunga";
        } else echo "hmm";
    }

    public function proses_sspd()
    {
        $this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
        
        $inv_id   = $this->uri->segment(4);
        $prosestgl = $this->uri->segment(5);
        $bunga = $this->uri->segment(6);
        $total = $this->uri->segment(7);
        $sisa = $this->uri->segment(8);
        $nbln =  $this->uri->segment(9);
        $jenis_bayar =  $this->uri->segment(10);
        $petugas_id = $this->uri->segment(11);
        $pejabat_id = $this->uri->segment(12);
        $keterangan = $this->uri->segment(13);



        $keterangan = str_replace("%20"," ",$keterangan);

        $query= $this->db->query("select source_id from pad_invoice where id=$inv_id and source_nama='pad_spt'");
        foreach ($query->result() as $row){
                    $spt_id = $row->source_id;
        }
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {            
            //koreksi dari AA supaya pajak_terhutang - bunga (menghindari bunga berbunga)
            
            date_default_timezone_set("Asia/Jakarta"); 
            
            $sspdno = $this->sspd_model->generate_sspdno(pad_tahun_anggaran());
            $post_data = array (
                'sspdno' => $sspdno,
                'tahun' => pad_tahun_anggaran(),
                'sspdtgl' => date('Y-m-d', strtotime($prosestgl)),
                'sspdjam' => date('H:i:s'),
                'jml_bayar' => $total,
                'bunga' => $bunga,
                'sisa' => $sisa,
                'jenis_bayar' => $jenis_bayar,
                'keterangan' => $keterangan,
                'bulan_telat' => (int)$nbln,
                'invoice_id' => $inv_id,
                'is_valid' => 1,
                'enabled' => 1,
                'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
                'petugas_id' => $petugas_id,
                'pejabat_id' => $pejabat_id,
            );

            if($this->sspd_model->save($post_data)) {
                // update stat pembayaran spt (OLD)
                $this->db->where('id', $spt_id);
                $this->db->update('pad_spt', array('status_pembayaran'=>1));
                //update pad INVOICE
                $this->db->where('id', $inv_id);
                $this->db->update('pad_invoice', array('status_bayar'=>1));
            }

             if($sisa>0){
                $query = $this->db->query("select tahun, get_nostpd() as stpdno, invoice_id, 0 as printed, 
                                            0 as stpdcount, bunga from pad_sspd where  invoice_id = $inv_id 
                                            order by invoice_id desc limit 1 ");
                    foreach ($query->result() as $row){
                        $tahun = $row->tahun;
                        $stpdno = $row->stpdno;
                        $bunga = $row->bunga;
                        $stpdcount = (int)$nbln;
                    }
                $query = $this->db->query("select inv.jatuh_tempo, jp.jatuhtempo as jenis_jatuhtempo  from pad_invoice inv                                                                        
                                            inner join pad_spt s on inv.source_id = s.id and inv.source_nama = 'pad_spt'                                                                    
                                            inner join pad_jenis_pajak jp on s.pajak_id = jp.id                                                                 
                                            where inv.id= $inv_id order by inv.id desc limit 1");
                     foreach ($query->result() as $row){
                        $jatuh_tempo = $row->jatuh_tempo;
                        $jenis_jatuhtempo = $row->jenis_jatuhtempo;
                    }
                if($jenis_jatuhtempo==0) {
                    $jatuhtempotgl = date('Y-m-t', strtotime($prosestgl));
                } else{  
                    $add_days = 30; //30 hr penetapan
                    $jatuhtempotgl = date('Y-m-d',strtotime($prosestgl) + (24*3600*$add_days));
                }

                $prosestgl = date('Y-m-d', strtotime($prosestgl));
                $created = date('Y-m-d');
                $create_uid = sipkd_user_id();
                $this->db->query("insert into pad_stpd (tahun, stpdno, stpdtgl, spt_id, jatuhtempotgl, stpdcount, bunga, create_uid, created) 
                                               values ($tahun, $stpdno,'$prosestgl', $spt_id, '$jatuhtempotgl', $stpdcount, $bunga, $create_uid, '$created')");
                //INSERT INVOICE

                $query = $this->db->query("select source_id from pad_invoice where id = $inv_id limit 1 ");
                foreach ($query->result() as $row){
                        $source_id = $row->source_id;
                    }

                $query = $this->db->query("select id from pad_stpd where spt_id = $source_id limit 1 ");
                foreach ($query->result() as $row){
                        $stpd_id = $row->id;
                    }

                $query = $this->db->query(" select s.id as source_id, 'pad_stpd' as source_nama, inv.tahun, inv.usaha_id, get_invno(inv.source_id) as invoice_no,                                                                                                   
                                            inv.jenis_usaha, inv.jenis_pajak, inv.npwpd, inv.nama_wp, inv.alamat_wp, inv.nama_op, inv.alamat_op,                                                                                                
                                            inv.tahun||lpad(inv.usaha_id::text, 2, '0')||lpad(get_invno(inv.source_id)::text, 5, '0') as nomor_tagihan,                                                                                             
                                            s.bunga, s.bunga as total, 0 as status_bayar, s.jatuhtempotgl as jatuh_tempo, inv.periode, inv.rekening_pokok,                                                                                              
                                            inv.rekening_denda, inv.nama_pokok, inv.nama_denda                                                                                              
                                            from pad_stpd s 
                                            inner join pad_invoice inv on inv.source_nama = 'pad_spt' and inv.source_id = s.spt_id                                                                                       
                                            where s.id= $stpd_id");

                foreach ($query->result() as $row)
                {
                   $source_id   = $row->source_id;
                   $source_nama = 'pad_stpd';
                   $tahun       = $row->tahun;
                   $npwpd       = $row->npwpd;
                   $nama_wp     = $row->nama_wp;
                   $alamat_wp   = $row->alamat_wp;
                   $op_nama      = $row->nama_op;
                   $op_alamat    = $row->alamat_op;
                   $rekening_pokok  = str_replace(".","",$row->rekening_pokok);//rekening pokok
                   $nama_pokok  = $row->nama_pokok; //nama pokok
                   $invoice_no     = $row->invoice_no; 
                   $usaha_id    = $row->usaha_id;
                   $spt_id      = $spt_id;
                   $jenis_usaha = $row->jenis_usaha;
                   $jenis_pajak = $row->jenis_pajak;
                   $nomor_tagihan = $row->nomor_tagihan;
                   $status_bayar =$row->status_bayar;
                   $periode     = $row->periode;
                   $rekening_denda = str_replace(".","",$row->rekening_denda);
                   $nama_denda  = $row->nama_denda;
                   $jatuh_tempo = $row->jatuh_tempo;
                   $total = $row->total;
                   $bunga = $row->bunga;
                }

                $update_data = array(
                                'tahun' => $tahun,
                                'usaha_id' => $usaha_id,
                                'invoice_no' => $invoice_no,
                                'source_id' => $source_id,
                                'source_nama' => $source_nama,  
                                'jatuh_tempo' => $jatuhtempotgl,
                                'nomor_tagihan' => $nomor_tagihan,
                                'jenis_usaha' => $jenis_usaha, //pad_usaha.nama
                                'jenis_pajak' => $jenis_pajak, //pad_jenis_pajak.nama
                                'bunga' => $bunga,
                                'total' => $total,
                                'npwpd' => $npwpd,
                                'nama_wp' => $nama_wp,
                                'alamat_wp' => $alamat_wp,
                                'alamat_op' => $op_alamat,
                                'nama_op' => $op_nama,
                                'status_bayar' => 0,
                                'rekening_pokok' => $rekening_pokok,
                                'rekening_denda' => $rekening_denda,
                                'periode' => $periode, //Desember 2015
                                'nama_pokok' => $nama_pokok,
                                'nama_denda' => $nama_denda,
                                'created' => date('Y-m-d h:i:s'),
                                'create_uid' => sipkd_user_id(),
                            );
                    $save = $this->invoice_model->save($update_data);
              }
            echo "ok";
        } else echo "hmm";
    } 
    
    public function proses_sspd_old()
    {
        $this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
        
        $spt_id   = $this->uri->segment(4);
        $prosestgl = $this->uri->segment(5);
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {            
            //koreksi dari AA supaya pajak_terhutang - bunga (menghindari bunga berbunga)
            $pajak = (int)$sptpd->pajak_terhutang;
            $pokok = $pajak - (int)$sptpd->bunga;
            
            $denda = hit_denda($pokok, pad_bunga()*100, $sptpd->jatuhtempotgl, $prosestgl);
            $bunga = round($denda->denda + $sptpd->bunga);
            $nbln  = $denda->bulan;
            $jml_bayar = $pokok + $bunga;
            
            date_default_timezone_set("Asia/Jakarta"); 
            
            $sspdno = $this->sspd_model->generate_sspdno(pad_tahun_anggaran());
            $post_data = array (
                'spt_id' => $spt_id,
                'sspdno' => $sspdno,
                'tahun' => pad_tahun_anggaran(),
                'sspdtgl' => date('Y-m-d', strtotime($prosestgl)),
                'sspdjam' => date('H:i:s'),
                'jml_bayar' => $jml_bayar,
                'bunga' => $bunga,
                'bulan_telat' => (int)$nbln,

                'is_valid' => 1,
                'enabled' => 1,
                'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
            );

            if($this->sspd_model->save($post_data)) {
                // update stat pembayaran spt
                $this->db->where('id', $spt_id);
                $this->db->update('pad_spt', array('status_pembayaran'=>1));
            }
            
            echo "ok";
        } else echo "hmm";
    }
    
    public function batal_sspd()
    {
        $this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
        
        $inv_id   = $this->uri->segment(4);
        if ($inv_id) {
        
        //DELETING and UPDATE status bayar
        $this->db->query("update pad_invoice set status_bayar = '0'  where id=$inv_id ");
        
        $this->sspd_model->delete($inv_id);      
            
        echo "ok";
        } else echo "hmm";
    }
    
    // penermaan / validasi
    public function proses_validasi()
    {
        $this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
        
        $spt_id = $this->uri->segment(4);
        $sptpd  = $this->sptpd_model->get($spt_id);
        $sspd   = $this->sspd_model->get_by_spt($spt_id);
        
        if ($spt_id && $sptpd && $sspd) {
            $terimano = $this->penerimaan_model->max_terimano(pad_tahun_anggaran());
            if ($sspd->hitung_bunga == 1 && $sspd->bunga > 0)
                $jmlterima = (float)$sptpd->pajak_terhutang + (float)$sspd->bunga;
            else 
                $jmlterima = (float)$sptpd->pajak_terhutang;

            $update_data = array (
                'is_validated' => 1,
                'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),
            );
            $this->sspd_model->update_by_spt($spt_id, $update_data);

            
            // terima
            $update_data = array(
                'tahun' => pad_tahun_anggaran(),
                'terimano' => $terimano,
                'terimatgl' => date('Y-m-d'),
                
                'jmlterima' => $jmlterima,
                
                'customer_id' => $sptpd->customer_id,
                'npwpd' => $this->load->model('subjek_pajak_model')->get_npwpd($sptpd->customer_id),
                
                'nobukti' => $sspd->sspdno,
                'notes' => 'Posting dari SSPD (Validasi)',
                
                'enabled' => 1,
                'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
            );
            $new_id = $this->penerimaan_model->save($update_data);
            
            // terima line - rek pajak
            $update_data = array(
                'terima_id' => $new_id,
                'spt_id' => $spt_id,
                
                'rekening_id' => $sptpd->rekening_id,
                'pajak_id' => $sptpd->pajak_id,
                
                'amount' => $sptpd->pajak_terhutang,
                
                'enabled' => 1,
                'created' => date('Y-m-d', strtotime($sspd->sspdtgl)),
                'create_uid' => sipkd_user_id(),
            );
            $this->penerimaan_model->save2($update_data);
            
            // terima line - rek denda (bunga)
            if ($sspd->hitung_bunga == 1 && $sspd->bunga > 0) {
                $update_data = array(
                    'terima_id' => $new_id,
                    'spt_id' => $spt_id,
                    
                    'rekening_id' => $this->load->model('pajak_model')->get_rek_denda($sptpd->pajak_id),
                    'pajak_id' => $sptpd->pajak_id,
                    
                    'amount' => $sspd->bunga,
                    
                    'enabled' => 1,
                    'created' => date('Y-m-d', strtotime($sspd->sspdtgl)),
                    'create_uid' => sipkd_user_id(),
                );
                $this->penerimaan_model->save2($update_data);
            }           
            
            echo "ok";
        } else echo "hmm";
    }
    
    public function batal_validasi()
    {
        $this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
        
        $spt_id   = $this->uri->segment(4);
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {
            //
            $update_data = array (
                'is_validated' => 0,
                'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),
            );
            $this->sspd_model->update_by_spt($spt_id, $update_data);
            
            //UPDATE STATE PEMBAYRAN DI SPT -> 0
            $update_data = array (
                'status_pembayaran' => 0,
            );
            $this->sptpd_model->update($spt_id, $update_data);
            
            //hapus terima + terimaline
            $terima_id = $this->penerimaan_model->get_terima_id_by_spt($spt_id);            
            $this->penerimaan_model->delete2_by_spt($spt_id);           
            $this->penerimaan_model->delete_if_no_spt($terima_id);          
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
                    
        $sptpd  = $this->load->model('sptpd_model')->get($this->input->get('id'));
        $sspd   = $this->load->model('sspd_model')->get_by_spt($this->input->get('id'));
        $cu = $this->load->model('objek_pajak_model')->get($sptpd->customer_usaha_id);
        $report = $this->load->model('report_model')->get_report($cu->usaha_id, $sptpd->type_id);
        
        // $bayar = $sptpd->pajak_terhutang+$sspd->bunga;
        // $bayar = ($sptpd->pajak_terhutang-$sptpd->bunga)+$sspd->bunga;
        $bayar = $sspd->jml_bayar;
        
        switch ($this->input->get('rpt')) {
            case 'sspd':
                $rpt = $report->sspdnm;
                $params = array(
                    "spt_id" => intval($sspd->invoice_id),
                );
                break;
            case 'validasi':
                $rpt = 'validasi_00';
                $params = array(
                    "spt_id" => intval($sspd->invoice_id),
                );
                break;
        }
        
        $params = array_merge($params, array(
            "alamat_lengkap" => pad_pemda_alamat_lengkap(),
            "terbilang" => strtoupper(terbilang($bayar)),
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),            
            //"logo" => base_url('assets/img/logorpt__.jpg'),
        ));
        
        $rpt = $rpt;
        // $rpt = $this->module.'/'.$rpt;
        
        $jasper = $this->load->library('Jasper');
        echo $jasper->cetak($rpt, $params, $type, false);
    }

}
