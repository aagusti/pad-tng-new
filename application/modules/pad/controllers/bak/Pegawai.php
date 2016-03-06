<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pegawai extends CI_Controller
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
            'apps_model', 'pegawai_model'
        ));
		
		$this->load->helper(active_module());
    }
    
    public function index() 
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
		
        $this->load->view('vpegawai', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('id, nip, nama, jabatan, golongan, pangkat, bagian, nomor_telp, tmt, enabled');
        $this->datatables->from('pad_pegawai');
        $this->datatables->date_column('8');
        $this->datatables->checkbox_column('9');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_pegawai = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama','nama','required|trim');
		$this->form_validation->set_rules('jabatan','jabatan','required|trim');
		$this->form_validation->set_rules('golongan','golongan','required|trim');
		$this->form_validation->set_rules('nip','nip','required|trim');
		$this->form_validation->set_rules('pangkat','pangkat','required|trim');
        $this->form_validation->set_rules('bagian','bagian','required|trim');
    }
    
    private function fpost()
    {
		$data['nama'] = $this->input->post('nama');
		$data['jabatan'] = $this->input->post('jabatan');
		$data['golongan'] = $this->input->post('golongan');
		$data['nip'] = $this->input->post('nip');
		$data['pangkat'] = $this->input->post('pangkat');
		$data['id'] = $this->input->post('id');
        $data['bagian'] = $this->input->post('bagian');
        $data['nomor_telp'] = $this->input->post('nomor_telp');
		$data['tmt'] = $this->input->post('tmt');
		$data['enabled'] = $this->input->post('enabled');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('pegawai'));
        }
        
        $p_pegawai_id = $this->uri->segment(4);
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('pegawai/add/' . $p_pegawai_id);
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'jabatan' => empty($input_post['jabatan']) ? NULL : $input_post['jabatan'],
				'golongan' => empty($input_post['golongan']) ? NULL : $input_post['golongan'],
				'nip' => empty($input_post['nip']) ? NULL : $input_post['nip'],
                'nomor_telp' => empty($input_post['nomor_telp']) ? NULL : $input_post['nomor_telp'],
                'bagian' => empty($input_post['bagian']) ? NULL : $input_post['bagian'],
				'pangkat' => empty($input_post['pangkat']) ? NULL : $input_post['pangkat'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),    
            );
            $this->pegawai_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('pegawai'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vpegawai_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('pegawai'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("pegawai/update/{$p_id}");
        
        if ($p_id && $get = $this->pegawai_model->get($p_id)) {
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
			$data['dt']['jabatan'] = empty($get->jabatan) ? NULL : $get->jabatan;
			$data['dt']['golongan'] = empty($get->golongan) ? NULL : $get->golongan;
			$data['dt']['nip'] = empty($get->nip) ? NULL : $get->nip;
			$data['dt']['pangkat'] = empty($get->pangkat) ? NULL : $get->pangkat;
            $data['dt']['nomor_telp'] = empty($get->nomor_telp) ? NULL : $get->nomor_telp;
            $data['dt']['bagian'] = empty($get->bagian) ? NULL : $get->bagian;
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';
			
			$this->load->view('vpegawai_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('pegawai'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("pegawai/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'jabatan' => empty($input_post['jabatan']) ? NULL : $input_post['jabatan'],
				'golongan' => empty($input_post['golongan']) ? NULL : $input_post['golongan'],
				'nip' => empty($input_post['nip']) ? NULL : $input_post['nip'],
                'nomor_telp' => empty($input_post['nomor_telp']) ? NULL : $input_post['nomor_telp'],
                'bagian' => empty($input_post['bagian']) ? NULL : $input_post['bagian'],
				'pangkat' => empty($input_post['pangkat']) ? NULL : $input_post['pangkat'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,  
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),  
            );
            
            $this->pegawai_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('pegawai'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vpegawai_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('pegawai'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->pegawai_model->get($id)) {
            $this->pegawai_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('pegawai'));
        } else {
            show_404();
        }
    }
}