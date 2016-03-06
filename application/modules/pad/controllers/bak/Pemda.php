<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pemda extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
        $module = 'referensi';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model', 'pemda_model'
        ));
		
		$this->load->helper(active_module());
    }
    
    public function index() 
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
		$this->edit();
    }
    
    private function fvalidation()
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('type','type','required|trim');
		$this->form_validation->set_rules('kepala_nama','kepala_nama','required|trim');
		$this->form_validation->set_rules('alamat','alamat','required|trim');
		$this->form_validation->set_rules('daerah','daerah','required|trim');
		$this->form_validation->set_rules('pemda_nama','pemda_nama','required|trim');
		$this->form_validation->set_rules('ibukota','ibukota','required|trim');
		$this->form_validation->set_rules('jabatan','jabatan','required|trim');
		$this->form_validation->set_rules('ppkd_id','ppkd_id','required|numeric');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['type'] = $this->input->post('type');
		$data['pemda_nama'] = $this->input->post('pemda_nama');
		$data['pemda_nama_singkat'] = $this->input->post('pemda_nama_singkat');
		$data['kepala_nama'] = $this->input->post('kepala_nama');
		$data['jabatan'] = $this->input->post('jabatan');
		$data['alamat'] = $this->input->post('alamat');
		$data['ibukota'] = $this->input->post('ibukota');
		$data['telp'] = $this->input->post('telp');
		$data['ppkd_id'] = $this->input->post('ppkd_id');
		$data['tmt'] = $this->input->post('tmt');
        
		$data['alamat_lengkap'] = $this->input->post('alamat_lengkap');
		$data['daerah'] = $this->input->post('daerah');
		$data['fax'] = $this->input->post('fax');
		$data['website'] = $this->input->post('website');
		$data['email'] = $this->input->post('email');
		
		$data['reklame_id'] = pad_to_decimal($this->input->post('reklame_id'));
		$data['airtanah_id'] = pad_to_decimal($this->input->post('airtanah_id'));
		$data['parkir_id'] = pad_to_decimal($this->input->post('parkir_id'));
		$data['hiburan_id'] = pad_to_decimal($this->input->post('hiburan_id'));
		$data['ppj_id'] = pad_to_decimal($this->input->post('ppj_id'));
		$data['restauran_id'] = pad_to_decimal($this->input->post('restauran_id'));
		$data['hotel_id'] = pad_to_decimal($this->input->post('hotel_id'));
		$data['walet_id'] = pad_to_decimal($this->input->post('walet_id'));

        $data['surat_no'] = $this->input->post('surat_no');
        $data['ijin_kd'] = $this->input->post('ijin_kd');
        $data['reklame_kd'] = $this->input->post('reklame_kd');
        $data['airtanah_kd'] = $this->input->post('airtanah_kd');
        $data['parkir_kd'] = $this->input->post('parkir_kd');
        $data['hiburan_kd'] = $this->input->post('hiburan_kd');
        $data['ppj_kd'] = $this->input->post('ppj_kd');
        $data['restauran_kd'] = $this->input->post('restauran_kd');
        $data['hotel_kd'] = $this->input->post('hotel_kd');

		$data['self_dok_id'] = pad_to_decimal($this->input->post('self_dok_id'));
		$data['office_dok_id'] = pad_to_decimal($this->input->post('office_dok_id'));
		
		$data['tgl_jatuhtempo_self'] = pad_to_decimal($this->input->post('tgl_jatuhtempo_self'));
		$data['spt_denda'] = pad_to_decimal($this->input->post('spt_denda'));
		$data['tgl_spt'] = pad_to_decimal($this->input->post('tgl_spt'));
		$data['pad_bunga'] = pad_to_decimal($this->input->post('pad_bunga'));
      
		$data['thn_ang'] = pad_to_decimal($this->input->post('thn_ang'));
		
        return $data;
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('pemda'));
        }
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("pemda/update");
        
        if ($get = $this->pemda_model->get_one()) {		
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['ppkd_id'] = empty($get->ppkd_id) ? NULL : $get->ppkd_id;
			$data['dt']['pemda_nama'] = empty($get->pemda_nama) ? NULL : $get->pemda_nama;
			$data['dt']['pemda_nama_singkat'] = empty($get->pemda_nama_singkat) ? NULL : $get->pemda_nama_singkat;
			$data['dt']['type'] = empty($get->type) ? NULL : $get->type;
			$data['dt']['kepala_nama'] = empty($get->kepala_nama) ? NULL : $get->kepala_nama;
			$data['dt']['jabatan'] = empty($get->jabatan) ? NULL : $get->jabatan;
			$data['dt']['alamat'] = empty($get->alamat) ? NULL : $get->alamat;
			$data['dt']['ibukota'] = empty($get->ibukota) ? NULL : $get->ibukota;
			$data['dt']['telp'] = empty($get->telp) ? NULL : $get->telp;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			
			$data['dt']['alamat_lengkap'] = empty($get->alamat_lengkap) ? NULL : $get->alamat_lengkap;
			$data['dt']['daerah'] = empty($get->daerah) ? NULL : $get->daerah;
			$data['dt']['fax'] = empty($get->fax) ? NULL : $get->fax;
			$data['dt']['website'] = empty($get->website) ? NULL : $get->website;
			$data['dt']['email'] = empty($get->email) ? NULL : $get->email;
              
			$data['dt']['reklame_id'] = $get->reklame_id;
			$data['dt']['airtanah_id'] = $get->airtanah_id;
			$data['dt']['parkir_id'] = $get->parkir_id;    
			$data['dt']['hiburan_id'] = $get->hiburan_id;
			$data['dt']['ppj_id'] = $get->ppj_id;
			$data['dt']['hotel_id'] = $get->hotel_id;
			$data['dt']['walet_id'] = $get->walet_id;
			$data['dt']['restauran_id'] = $get->restauran_id;

            $data['dt']['surat_no'] = $get->surat_no;
            $data['dt']['ijin_kd'] = $get->ijin_kd;
            $data['dt']['reklame_kd'] = $get->reklame_kd;
            $data['dt']['airtanah_kd'] = $get->airtanah_kd;
            $data['dt']['parkir_kd'] = $get->parkir_kd;    
            $data['dt']['hiburan_kd'] = $get->hiburan_kd;
            $data['dt']['ppj_kd'] = $get->ppj_kd;
            $data['dt']['hotel_kd'] = $get->hotel_kd;
            $data['dt']['restauran_kd'] = $get->restauran_kd;

			$data['dt']['self_dok_id'] = $get->self_dok_id;
			$data['dt']['office_dok_id'] = $get->office_dok_id;
			
			$data['dt']['tgl_spt'] = $get->tgl_spt;
			$data['dt']['tgl_jatuhtempo_self'] = $get->tgl_jatuhtempo_self;
			$data['dt']['spt_denda'] = $get->spt_denda;
			$data['dt']['pad_bunga'] = $get->pad_bunga;
            
			$data['dt']['thn_ang'] = pad_tahun_anggaran();
			
			$this->load->view('vpemda_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('pemda'));
        }
        
        $post_data = $this->fpost();
        $p_id       = $post_data['id'];
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("pemda/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'ppkd_id' => empty($input_post['ppkd_id']) ? NULL : $input_post['ppkd_id'],
				'pemda_nama' => empty($input_post['pemda_nama']) ? NULL : $input_post['pemda_nama'],
				'pemda_nama_singkat' => empty($input_post['pemda_nama_singkat']) ? NULL : $input_post['pemda_nama_singkat'],
				'type' => empty($input_post['type']) ? NULL : $input_post['type'],
				'kepala_nama' => empty($input_post['kepala_nama']) ? NULL : $input_post['kepala_nama'],
				'jabatan' => empty($input_post['jabatan']) ? NULL : $input_post['jabatan'],
				'alamat' => empty($input_post['alamat']) ? NULL : $input_post['alamat'],
				'ibukota' => empty($input_post['ibukota']) ? NULL : $input_post['ibukota'],
				'telp' => empty($input_post['telp']) ? NULL : $input_post['telp'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
                
				'alamat_lengkap' => empty($input_post['alamat_lengkap']) ? NULL : $input_post['alamat_lengkap'],
				'daerah' => empty($input_post['daerah']) ? NULL : $input_post['daerah'],
				'fax' => empty($input_post['fax']) ? NULL : $input_post['fax'],
				'email' => empty($input_post['email']) ? NULL : $input_post['email'],
				'website' => empty($input_post['website']) ? NULL : $input_post['website'],

				'reklame_id' => empty($input_post['reklame_id']) ? NULL : $input_post['reklame_id'],
				'airtanah_id' => empty($input_post['airtanah_id']) ? NULL : $input_post['airtanah_id'],
				'parkir_id' => empty($input_post['parkir_id']) ? NULL : $input_post['parkir_id'],
				'ppj_id' => empty($input_post['ppj_id']) ? NULL : $input_post['ppj_id'],
				'hiburan_id' => empty($input_post['hiburan_id']) ? NULL : $input_post['hiburan_id'],
				'walet_id' => empty($input_post['walet_id']) ? NULL : $input_post['walet_id'],
				'restauran_id' => empty($input_post['restauran_id']) ? NULL : $input_post['restauran_id'],
                'hotel_id' => empty($input_post['hotel_id']) ? NULL : $input_post['hotel_id'],

                'surat_no' => empty($input_post['surat_no']) ? NULL : $input_post['surat_no'],
                'ijin_kd' => empty($input_post['ijin_kd']) ? NULL : $input_post['ijin_kd'],
                'reklame_kd' => empty($input_post['reklame_kd']) ? NULL : $input_post['reklame_kd'],
                'airtanah_kd' => empty($input_post['airtanah_kd']) ? NULL : $input_post['airtanah_kd'],
                'parkir_kd' => empty($input_post['parkir_kd']) ? NULL : $input_post['parkir_kd'],
                'ppj_kd' => empty($input_post['ppj_kd']) ? NULL : $input_post['ppj_kd'],
                'hiburan_kd' => empty($input_post['hiburan_kd']) ? NULL : $input_post['hiburan_kd'],
                'restauran_kd' => empty($input_post['restauran_kd']) ? NULL : $input_post['restauran_kd'],
                'hotel_kd' => empty($input_post['hotel_kd']) ? NULL : $input_post['hotel_kd'],

				'self_dok_id' => empty($input_post['self_dok_id']) ? NULL : $input_post['self_dok_id'],
				'office_dok_id' => empty($input_post['office_dok_id']) ? NULL : $input_post['office_dok_id'],

				'tgl_spt' => empty($input_post['tgl_spt']) ? NULL : $input_post['tgl_spt'],
				'tgl_jatuhtempo_self' => empty($input_post['tgl_jatuhtempo_self']) ? NULL : $input_post['tgl_jatuhtempo_self'],
				'spt_denda' => empty($input_post['spt_denda']) ? NULL : $input_post['spt_denda'],
				'pad_bunga' => empty($input_post['pad_bunga']) ? NULL : $input_post['pad_bunga'],

            );
            
            $this->pemda_model->update($p_id, $update_data);
            $this->update_session();
			
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('pemda'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vpemda_form', $data);
    }
	
	
	function update_session() {
		$row = (object) $this->fpost();
        $sess_data = array(
            'pad_pemda_nama' => $row->pemda_nama,
            'pad_pemda_type' => $row->type,
            'pad_pemda_kepala' => $row->kepala_nama,
            'pad_pemda_jabatan' => $row->jabatan,
            'pad_pemda_ibukota' => $row->ibukota,
            'pad_pemda_unitid' => $row->ppkd_id,

            'pad_pemda_daerah' => $row->daerah,
            'pad_pemda_alamat' => $row->alamat,
            'pad_pemda_alamat_lengkap' => $row->alamat_lengkap,
            'pad_pemda_telp' => $row->telp,
            'pad_pemda_fax' => $row->fax,
            'pad_pemda_website' => $row->website,
            'pad_pemda_email' => $row->email,
            'pad_pemda_singkatan' => $row->pemda_nama_singkat,

            'pad_reklame_id' => $row->reklame_id,
            'pad_air_tanah_id' => $row->airtanah_id,
            'pad_parkir_id' => $row->parkir_id,
            'pad_ppj_id' => $row->ppj_id,
            'pad_hiburan_id' => $row->hiburan_id,
            'pad_hotel_id' => $row->hotel_id,
            'pad_restauran_id' => $row->restauran_id,
            'pad_walet_id' => $row->walet_id,

            'pad_surat_no' => $row->surat_no,
            'pad_ijin_kd' => $row->ijin_kd,
            'pad_reklame_kd' => $row->reklame_kd,
            'pad_air_tanah_kd' => $row->airtanah_kd,
            'pad_parkir_kd' => $row->parkir_kd,
            'pad_ppj_kd' => $row->ppj_kd,
            'pad_hiburan_kd' => $row->hiburan_kd,
            'pad_hotel_kd' => $row->hotel_kd,
            'pad_restauran_kd' => $row->restauran_kd,

            'pad_dok_self_id' => $row->self_dok_id,
            'pad_dok_office_id' => $row->office_dok_id,

            'pad_spt_date' => $row->tgl_spt,
            'pad_spt_due_date' => $row->tgl_jatuhtempo_self,

            'pad_spt_denda' => $row->spt_denda,
            'pad_bunga' => $row->pad_bunga,

            'pad_tahun_anggaran' => $row->thn_ang,
        );
        		
        $this->unset_session();
        $this->session->set_userdata($sess_data);
	}
    
	function unset_session() {		
        $sess_data = array(
            'pad_pemda_nama' => '',
            'pad_pemda_type' => '',
            'pad_pemda_kepala' => '',
            'pad_pemda_jabatan' => '',
            'pad_pemda_ibukota' => '',
            'pad_pemda_unitid' => '',

            'pad_pemda_daerah' => '',
            'pad_pemda_alamat' => '',
            'pad_pemda_alamat_lengkap' => '',
            'pad_pemda_telp' => '',
            'pad_pemda_fax' => '',
            'pad_pemda_website' => '',
            'pad_pemda_email' => '',
            'pad_pemda_singkatan' => '',

            'pad_reklame_id' => '',
            'pad_air_tanah_id' => '',
            'pad_hotel_id' => '',
            'pad_walet_id' => '',
            'pad_parkir_id' => '',
            'pad_ppj_id' => '',
			'pad_hiburan_id' => '',
            'pad_restauran_id' => '',

            'pad_no_surat' => '',
            'pad_ijin_kd' => '',
            'pad_reklame_kd' => '',
            'pad_air_tanah_kd' => '',
            'pad_hotel_kd' => '',
            'pad_parkir_kd' => '',
            'pad_ppj_kd' => '',
            'pad_hiburan_kd' => '',
            'pad_restauran_kd' => '',

            'pad_dok_self_id' => '',
            'pad_dok_office_id' => '',

            'pad_spt_date' => '',
            'pad_spt_due_date' => '',

            'pad_spt_denda' => '',
            'pad_bunga' => '',

            'pad_tahun_anggaran' => '',
        );
        
        $this->session->unset_userdata($sess_data);
	}
}