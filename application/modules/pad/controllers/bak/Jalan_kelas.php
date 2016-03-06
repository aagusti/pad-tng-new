<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class jalan_kelas extends CI_Controller
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
            'apps_model', 'jalan_kelas_model'
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
		
        $this->load->view('vjalan_kelas', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select("id, nama, singkatan, 
        (case when kriteria = '0' then 'Jalan' when kriteria = '1' then 'Bandara'  END) as kriteria, nilai, enabled");
        $this->datatables->from('pad_reklame_kelas_jalan');
        $this->datatables->numeric_column('2');
        $this->datatables->checkbox_column('5');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_jalan_kelas = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama','Uraian','required|trim');
		$this->form_validation->set_rules('nilai','Nilai','required');
        $this->form_validation->set_rules('singkatan','Singkatan','required|trim|max_length[2]');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['nama'] = $this->input->post('nama');
        $data['singkatan'] = $this->input->post('singkatan');
        $data['kriteria'] = $this->input->post('kriteria');
		$data['nilai'] = pad_to_decimal($this->input->post('nilai'));
        $data['enabled'] = $this->input->post('enabled');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('jalan_kelas'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('jalan_kelas/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'singkatan' => empty($input_post['singkatan']) ? NULL : $input_post['singkatan'],
                'kriteria' => $input_post['kriteria'],
				'nilai' => empty($input_post['nilai']) ? NULL : $input_post['nilai'],
                'enabled' => empty($input_post['enabled']) ? 0 : 1, 
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->jalan_kelas_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('jalan_kelas'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vjalan_kelas_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('jalan_kelas'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("jalan_kelas/update/{$p_id}");
        
        if ($p_id && $get = $this->jalan_kelas_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
            $data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
            $data['dt']['singkatan'] = empty($get->singkatan) ? NULL : $get->singkatan;
            $data['dt']['kriteria'] = empty($get->kriteria) ? NULL : $get->kriteria;
			$data['dt']['nilai'] = empty($get->nilai) ? NULL : $get->nilai;
            $data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';
			
			$this->load->view('vjalan_kelas_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('jalan_kelas'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("jalan_kelas/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'singkatan' => empty($input_post['singkatan']) ? NULL : $input_post['singkatan'],
                'kriteria' => $input_post['kriteria'],
				'nilai' => empty($input_post['nilai']) ? NULL : $input_post['nilai'],
                'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            
            $this->jalan_kelas_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('jalan_kelas'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vjalan_kelas_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('jalan_kelas'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->jalan_kelas_model->get($id)) {
            $this->jalan_kelas_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('jalan_kelas'));
        } else {
            show_404();
        }
    }
}