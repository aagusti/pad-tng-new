<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pad extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    if(active_module()!='pad') {
      show_404();
      exit;
    }
        
        $this->load->model('login_model');
        if ($grp = $this->login_model->check_user_app()) {            
            $this->session->set_userdata('groupid'  , $grp->group_id);
            $this->session->set_userdata('groupkd'  , $grp->group_kode);
            $this->session->set_userdata('groupname', htmlspecialchars($grp->group_nama));
        }
        
    $this->load->model(array('apps_model', 'login_model'));
    $this->load->helper(active_module());
  }
  
  public function index()
  {
    $this->load->model('pad_model');
    if($row = $this->pad_model->get_pemda()) {
      $ta = date('Y');
            $sess_data = array(
                'pad_tahun_anggaran' => $ta,
                'pad_pemda_daerah' => $row->daerah,
                'pad_pemda_alamat' => $row->alamat,
                'pad_pemda_alamat_lengkap' => $row->alamat_lengkap,
                'pad_pemda_telp' => $row->telp,
                'pad_pemda_fax' => $row->fax,
                'pad_pemda_website' => $row->website,
                'pad_pemda_email' => $row->email,
                'pad_pemda_nama' => $row->pemda_nama,
                'pad_pemda_singkatan' => $row->pemda_nama_singkat,
                'pad_pemda_type' => $row->type,
                'pad_pemda_kepala' => $row->kepala_nama,
                'pad_pemda_jabatan' => $row->jabatan,
                'pad_pemda_ibukota' => $row->ibukota,
                'pad_pemda_unitid' => $row->ppkd_id, // ini hrusnya id pemda ato ppkd yah? // ini link ke spt (unit_id), *seharusnya jg sama buat lookup persen bunga di penerimaan..
                
                'pad_reklame_id' => $row->reklame_id,
                'pad_air_tanah_id' => $row->airtanah_id,
                'pad_dok_self_id' => $row->self_dok_id,
                'pad_dok_office_id' => $row->office_dok_id,
                  
                'pad_hiburan_id' => $row->hiburan_id,
                'pad_ppj_id' => $row->ppj_id,
                'pad_hotel_id' => $row->hotel_id,
                'pad_walet_id' => $row->walet_id,
                'pad_restauran_id' => $row->restauran_id,
                'pad_parkir_id' => $row->parkir_id,  

                'pad_surat_no' => $row->surat_no,
                'pad_ijin_kd' => $row->ijin_kd,
                'pad_reklame_kd' => $row->reklame_kd,
                'pad_air_tanah_kd' => $row->airtanah_kd,
                'pad_parkir_kd' => $row->parkir_kd,
                'pad_ppj_kd' => $row->ppj_kd,
                'pad_hiburan_kd' => $row->hiburan_kd,
                'pad_hotel_kd' => $row->hotel_kd,
                'pad_restauran_kd' => $row->restauran_kd,

                'pad_spt_date' => $row->tgl_spt,
                'pad_spt_due_date' => $row->tgl_jatuhtempo_self,
                
                'pad_spt_denda' => $row->spt_denda,
                'pad_bunga' => $row->pad_bunga,
                'pad_ppkd_id' => $row->ppkd_id,
            );
            $this->session->set_userdata($sess_data);
    }

    $data['current'] = 'beranda';
    $data['apps']    = $this->apps_model->get_active_only();
    
    if(!wp_login()) 
      $this->load->view('vmain', $data);
    else
      $this->load->view('wp/vmenu', $data);
  }

    //metode sementara, kelemahan : respon lambat
    public function menu($param)
    {
        switch ($param) {
            case 1:
                $this->session->set_userdata('menu_daftar', 'active');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;
            case 2:
                $this->session->set_userdata('menu_pendataan', 'active');
                $array = array('menu_daftar' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;
            case 3:
                $this->session->set_userdata('menu_penetapan', 'active');
                $array = array('menu_pendataan' => '', 'menu_daftar' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;                
            case 4:
                $this->session->set_userdata('menu_pemeriksaan', 'active');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_daftar' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;
            case 5:
                $this->session->set_userdata('menu_penagihan', 'active');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_daftar' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;
            case 6:
                $this->session->set_userdata('menu_penerimaan', 'active');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_daftar' => '', 'menu_pelayanan' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;
            case 7:
                $this->session->set_userdata('menu_pelayanan', 'active');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_daftar' => '',
                               'menu_referensi' => '');
                $this->session->unset_userdata($array);
                break;
             case 8:
                $this->session->set_userdata('menu_referensi', 'active');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_daftar' => '');
                $this->session->unset_userdata($array);
                break;
            case 9:
                $this->session->set_userdata('menu_reklame', 'menu-open');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_daftar' => '');
                $this->session->unset_userdata($array);   
                break;
            case 10:
                $this->session->set_userdata('menu_at', 'menu-open');
                $array = array('menu_pendataan' => '', 'menu_penetapan' => '' , 'menu_pemeriksaan' => '',
                               'menu_penagihan' => '', 'menu_penerimaan' => '', 'menu_pelayanan' => '',
                               'menu_daftar' => '');
                $this->session->unset_userdata($array);  
                break; 
            case 'SC':
                if ($this->session->userdata('sidebar-collapse')=='sidebar-collapse')
                $this->session->set_userdata('sidebar-collapse', '');        
                else
                $this->session->set_userdata('sidebar-collapse', 'sidebar-collapse');        
            default:
                echo "unregistered menu";
        }
    }
}
