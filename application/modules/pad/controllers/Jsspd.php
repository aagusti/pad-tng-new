<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jsspd extends CI_Controller
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
            'apps_model','rekening_model'
        ));

        $this->load->helper(array(active_module(),'pad_helper'));
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

        $select_data = $this->rekening_model->get_select_padl(3,1);
        $options     = array();
        if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->kode] = $row->nama;
        }}
        $js = 'id="rekkd" class="form-control input-sm" style="width:100px;"';
        $options[999] = 'SEMUA REKENING';
        $select = form_dropdown('rekkd', $options, 999, $js);
        $select = preg_replace("/[\r\n]+/", "", $select);;
        $data['select_rekkd'] = $select;

        $options = array(
            '0' => 'BLM Posting',
            '1' => 'Posted',
        );
        $this->load->view(active_module().'/vjsspd', $data);
    }

    public function grid()
    {
        $rekkd = $this->input->get('rekkd');
        $posted = $this->input->get('pos');
        $posted = empty($posted) ? 0 : $posted;
        $terimatgl = $this->input->get('tgl1');
        $terimatgl = empty($terimatgl) ? date('Y-m-01') : date('Y-m-d', strtotime($terimatgl));
        $terimatgl2 = $this->input->get('tgl2');
        $terimatgl2 = empty($terimatgl2) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl2));

        $this->load->library('Datatables');
        $this->datatables->select("ss.id, pad.get_sspdno(s.id)  nomor, ss.sspdtgl::date as tanggal,
            s.nopd nopd, s.nama_op || '/ '|| s.nama_wp as customernm,
            s.rekening_pokok as rekening_pokok,
            (ss.jml_bayar-(ss.denda+ss.bunga)) as pokok,
            s.rekening_denda as rekening_denda,
            (ss.denda+ss.bunga) as denda,
            ss.posted as posted
            ", false);
        $this->datatables->from('public.pad_invoice as s');
        $this->datatables->join('pad.pad_sspd as ss','ss.invoice_id=s.id');
        $this->datatables->where("ss.sspdtgl::date between '{$terimatgl}' and '{$terimatgl2}'", null, false);
        $this->datatables->where("ss.posted = '{$posted}'", null, false);
        if ($rekkd<>'999')
            $this->datatables->where("s.rekening_pokok ilike '{$rekkd}%'", null, false);

        $this->datatables->rupiah_column('6,8');
        $this->datatables->date_column('2');

        echo $this->datatables->generate();
    }

    public function posting() {
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
      $sql = "SELECT ss.id, s.nomor_tagihan nomor, s.tanggal_invoice,
            pad.get_sspdno(s.id) sspdno, ss.sspdtgl as tanggal,
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
            ss.denda+ss.bunga as denda,
            s.rekening_pokok as rekening_pokok,
            s.nama_pokok as nama_pokok,
            ss.jml_bayar-(ss.denda+ss.bunga) as pokok,
            s.rekening_denda as rekening_denda,
            s.nama_denda as nama_denda
            FROM public.pad_invoice s
                 inner join pad.pad_sspd ss on s.id=ss.invoice_id
            WHERE ss.id IN ($req_id)
                  AND s.tanggal_invoice is not null
                  AND s.type_id is not null
                  AND s.nopd is not null
                  AND s.usaha_id is not null
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
              "nomorNotaPenerimaan"=>$row->sspdno,
              "tanggalNotaPenerimaan"=>date_format(date_create($row->tanggal), 'd-m-Y'),
              "tahun"=>date_format(date_create($row->tanggal), 'Y'),
              "selfAssessment"=> ($row->type_id==1 ? True : False),
              "casInAdvance"=>False,
              "jenisPajak"=>map_jns_pajak($row->usaha_id),
              "jenisSkp"=> map_jns_skp($row->type_id),
              "tahunPajak"=>date_format(date_create($row->tanggal_invoice), 'Y'),
              "bulanPajak"=>date_format(date_create($row->tanggal_invoice), 'm'),
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
                  $arrDet = array("kodeRekening"=>get_rekening_dotted($row->rekening_pokok),
                                  "namaRekening"=>$row->nama_pokok,
                                  "nilai"=>(int) $row->pokok);
                  array_push($arrDets,$arrDet);
              }

              if ((int) $row->denda>0)
              {
                  $arrDet = array("kodeRekening"=>get_rekening_dotted($row->rekening_denda),
                                    "namaRekening"=>$row->nama_denda,
                                    "nilai"=>(int) $row->denda);
                  array_push($arrDets,$arrDet);
              }
              $arr["rincians"]=$arrDets;
              array_push($args, $arr);
            }


             $amt = $this->rest_client->put(
                          'PenerimaanPajakRestService/insertPenerimaanPajaks',json_encode($args)); #realisasi
                          #http://ws1.sp3ktra.com:8080/EgovService/webresources/
            if (substr($amt,0,8)=='Berhasil')
            {
              $sql = "UPDATE public.pad_sspd
                               SET posted = 1
                        WHERE id IN ($req_id)";
              $query = $this->db->query($sql);
              $result = array("status"=>1,
                               "message"=>$amt,
                              "data"=>json_encode($args));
            }
            elseif (substr($amt,0,5)=='Gagal')
            { $result = array("status"=>0,
                               "message"=>$amt,
                              "data"=>json_encode($args));
            }
            else
            { if ($amt=="") $amt="No Response";
              $result = array("status"=>2,
                              "message"=>$amt,
                              "data"=>json_encode($args));
            }
        }
        elseif ($state==1)
        {

             foreach ($query->result() as $row)
             {
                $id = $row->id;
                $arr = array(//"nama"=>$row->nama,
                          "nomorSkp"=>$row->sspdno,
                          "userName"=>SPEKTRA_USER,
                          "password"=>SPEKTRA_PASS);

                $json_data = json_encode($arr);
                $amt = $this->rest_client->put("PenerimaanPajakRestService/deletePenerimaanPajak/$row->sspdno/".SPEKTRA_USER,$json_data);
                //echo $json_data;
                //var_dump($amt);
                //(substr($amt,0,5)=='Gagal') #
                //if (substr($amt,0,8)=='Berhasil')
                //{   
                $sql = "UPDATE pad.pad_sspd
                                 SET posted = 0
                          WHERE id=$id ";
                    $query = $this->db->query($sql);
                    $result = array("status"=>1,
                                 "message"=>$amt);
                    $n = $n+1;
                //}
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
