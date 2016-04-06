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
        $this->datatables->select("s.id, s.nomor_tagihan, s.tanggal_invoice as tanggal, 
            s.nopd nopd, s.nama_op || '/ '|| s.nama_wp as customernm, 
            s.rekening_pokok as rekening_pokok,
            s.pokok as pokok,
            s.rekening_denda as rekening_denda,
            s.denda as denda,
            s.posted as posted
            ", false);
        $this->datatables->from('public.pad_invoice as s');
        // $this->datatables->join('pad.pad_spt as spt on s.source_id=spt.id');
        // $this->datatables->join('pad.pad_customer_usaha as cu on spt.customer_usaha_id=cu.id');
        // $this->datatables->where("spt.source_nama='pad_spt'");
        
        $this->datatables->rupiah_column('6,8');
        $this->datatables->date_column('2');

        echo $this->datatables->generate();
    }

    function posting() {
      /*
      http://ws1.sp3ktra.com:8080/EgovService/webresources/WajibPajakRestService/insertWajibPajaks
      http://ws1.sp3ktra.com:8080/EgovService/webresources/WajibPajakRestService/replaceAllWajibPajaks
      http://ws1.sp3ktra.com:8080/EgovService/webresources/WajibPajakRestService/deleteAllWajibPajak/userName
      http://ws1.sp3ktra.com:8080/EgovService/webresources/WajibPajakRestService/deleteWajibPajak/npwpd/userName
      */
      $state = $this->uri->segment(4);
      #$server = 'http://192.168.56.4/~aagusti/padl-tng/api/transaksi';
      $server = SPEKTRA_SERVER;
      $this->rest_client->initialize(array( 'server'=>$server));
      $this->rest_client->http_header('Content-Type','application/json'); 
      $req_id = $this->input->get_post('id');
      $amt ='Other Error';
      $sql = "SELECT s.id, s.nomor_tagihan nomor, s.tanggal_invoice as tanggal, 
            s.type_id as jenis ,
            0 as is_cia ,
            s.nama_wp,
            s.npwpd npwpd, 
            s.alamat_wp,
            s.nopd,
            s.nama_op,
            s.alamat_op,
            s.jatuh_tempo,
            s.pokok,
            s.usaha_id,
            s.type_id,
            s.denda+s.bunga as denda,
            s.rekening_pokok as rekening_pokok,
            s.nama_pokok as nama_pokok,
            s.pokok as pokok,
            s.rekening_denda as rekening_denda,
            s.nama_denda as nama_denda
            FROM public.pad_invoice s
            WHERE s.id IN ($req_id)
                  AND s.tanggal_invoice is not null
                  AND s.type_id is not null
                  AND nopd is not null
                  AND usaha_id is not null
                  AND (s.pokok>0 or s.denda+s.bunga>0)
            ORDER BY 2,3  
            ";
            
      $query = $this->db->query($sql);
      if ($query->num_rows() > 0)
      {         
        if ($state==0)
        {  $args = array();
           $arr = array();

           $mkey = '';
           //$skp = array();
           foreach ($query->result() as $row)
           { 
           
              $arr = array("nomorSkp"=>$row->nomor,
              "tanggalSkp"=>date_format(date_create($row->tanggal), 'd-m-Y'),
              "tahun"=>date_format(date_create($row->tanggal), 'Y'),
              "selfAssessment"=> ($row->type_id==1 ? True : False),
              "casInAdvance"=>False,
              "jenisPajak"=>(int)$row->usaha_id,
              "jenisSkp"=>(int)$row->type_id,
              "namaWajibPajak"=>$row->nama_wp,
              "npwpd"=>$row->npwpd,
              "alamatWajibPajak"=>$row->alamat_wp,
              "nop"=>$row->nopd,
              "objekPajak"=>$row->nama_op,
              "alamatObjekPajak"=>$row->alamat_op,
              "jatuhTempo"=>date_format(date_create($row->jatuh_tempo), 'd-m-Y'),
              "nilaiPajak"=>(int) $row->pokok,
              "nilaiDenda"=>(int) $row->denda,
              "nilaiKenaikan"=>0, //$row->npwpd,
              "nilaiBunga"=>0, //$row->npwpd,
              "userName"=>SPEKTRA_USER,
              "password"=>SPEKTRA_PASS);
              $arrDets=array();
              if ((int) $row->pokok>0)
              {
                  $arrDet = array("kodeRekening"=>$row->rekening_pokok,
                                  "namaRekening"=>$row->nama_pokok,
                                  "Nilai"=>(int) $row->pokok);
                  array_push($arrDets,$arrDet);    
              }
              
              if ((int) $row->denda>0)
              {
                  $arrDet = array("kodeRekening"=>$row->rekening_denda,
                                    "namaRekening"=>$row->nama_denda,
                                    "Nilai"=>(int) $row->denda);
                  array_push($arrDets,$arrDet);    
              }
              $arr["Rincians"]=$arrDets;
              array_push($args, $arr);
            }
             
             die (json_encode($args));
             //$amt = $this->rest_client->put(
             //             'SkpRestService/insertSkp',json_encode($args)); #realisasi
          
            if (substr($amt,0,8)=='Berhasil')
            {
              $sql = "UPDATE public.pad_invoice
                               SET posted = 1
                        WHERE id IN ($req_id)";
              $query = $this->db->query($sql);         
              $result = array("status"=>1,
                               "message"=>$amt);     
            }
            elseif (substr($amt,0,5)=='Gagal')
            { $result = array("status"=>0,
                               "message"=>$amt);          
            }
            else
            { $result = array("status"=>2,
                              "message"=>$amt);             
            } 
        }
        elseif ($state==1) 
        {

             foreach ($query->result() as $row)
             {
                $id = $row->id;
                $arr = array(//"nama"=>$row->nama,
                          "nomorSkp"=>$row->nomor,
                          "userName"=>SPEKTRA_USER,
                          "password"=>SPEKTRA_PASS);
                          
                $json_data = json_encode($arr);
                $amt = $this->rest_client->put(
                              "SkpRestService/deleteSkp/$row->nomor/".SPEKTRA_USER,$json_data); 
                //echo $json_data;
                //var_dump($amt);
                //(substr($amt,0,5)=='Gagal') #
                if (substr($amt,0,8)=='Berhasil')
                {   $sql = "UPDATE public.pad_invoice
                                 SET posted = 0
                          WHERE id=$id ";
                    $query = $this->db->query($sql);         
                    $result = array("status"=>1,
                                 "message"=>$amt);
                    $n = $n+1;
                }
                $c = $c+1;
             }
             $result = array("status"=>1,
                             "message"=>"Data $n dari $c record(s) Unposted");     
          }
          else
          {   $result = array("status"=>0,
                             "message"=>'Tidak ada data yang dibatalkan');          
          }
      }
      else
      {  
         $result = array("status"=>0,
                             "message"=>'Data tidak ditemukan');
      }
      echo json_encode($result);
    }
}
