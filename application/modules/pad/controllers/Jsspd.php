<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jinvoice extends CI_Controller
{
    private $module = 'jurnal';
    private $controller = 'jsspd';

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
            s.npwpd nopd, s.nama_op || '/ '||s.nama_wp as customernm, 
            s.rekening_pokok as rekening_pokok,
            s.pokok as pokok,
            s.rekening_denda as rekening_denda,
            s.denda as denda,
            s.posted as posted
            ", false);
        $this->datatables->from('pad.pad_invoice as s');
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
      if ($state==0)
      {   $sql = "SELECT c.id, pad.get_npwpd(c.id, true) as npwpd, c.nama, c.alamat,
                         cu.opnm, cu.opalamat, cu.usaha_id, pad.get_nopd(cu.id, true) as nopd
                  FROM pad.pad_customer c
                       INNER JOIN pad.pad_customer_usaha cu 
                                on c.id = cu.customer_id
                  WHERE c.id IN ($req_id)
                  ORDER BY 2,8  ";
          
          $query = $this->db->query($sql);
          $args = array();
          $arr = array();

          if ($query->num_rows() > 0)
          { $mkey = '';
            $objekPajaks = array();
            foreach ($query->result() as $row)
            { if ($mkey!=$row->npwpd)
              { if (count($arr)>0)
                  {  $arr["objekPajaks"]=$objekPajaks;
                        array_push($args,$arr);
                        $arr = array();
                  }
                  $arr = array("nama"=>$row->nama,
                              "npwpd"=>$row->npwpd,
                              "alamat"=>$row->alamat,
                              "userName"=>SPEKTRA_USER,
                              "password"=>SPEKTRA_PASS);
                    $objekPajaks=array();
              }
              $objekPajak = array("nop"=>$row->nopd,
                                    "namaObjekPajak"=>$row->opnm,
                                    "alamatObjekPajak"=>$row->opalamat,
                                    "jenisPajak"=>(int)$row->usaha_id);
              array_push($objekPajaks, $objekPajak);
            }
             
             $arr["objekPajaks"]=$objekPajaks;
             array_push($args,$arr);
             $amt = $this->rest_client->put(
                          'insertWajibPajaks',json_encode($args)); #realisasi
          }
          if (substr($amt,0,8)=='Berhasil')
          {
            $sql = "UPDATE pad.pad_customer
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
          $sql = "SELECT id, pad.get_npwpd(c.id, true) as npwpd
                  FROM pad.pad_customer c
                  WHERE c.id IN (".$this->input->get_post('id').")
                  ORDER BY 2  ";
          
          $query = $this->db->query($sql);
          $args = array();
          $arr = array();
          $c = 0;
          $n = 0;
          if ($query->num_rows() > 0)
          {
             $mkey = '';
             $wajibPajaks = array();
             foreach ($query->result() as $row)
             {
                $id = $row->id;
                $arr = array(//"nama"=>$row->nama,
                          "npwpd"=>$row->npwpd,
                          //"alamat"=>$row->alamat,
                          "userName"=>SPEKTRA_USER,
                          "password"=>SPEKTRA_PASS);
                          
                $json_data = json_encode($arr);
                $amt = $this->rest_client->put(
                              "deleteWajibPajak/$row->npwpd/SPEKTRA_USER",$json_data); 
                //echo $json_data;
                //var_dump($amt);
                //(substr($amt,0,5)=='Gagal') #
                if (substr($amt,0,8)=='Berhasil')
                {   $sql = "UPDATE pad.pad_customer
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
      {  $amt = $this->rest_client->get('ambilContohWajibPajakJson');
         //var_dump($amt);
      }
      echo json_encode($result);
    }
}
