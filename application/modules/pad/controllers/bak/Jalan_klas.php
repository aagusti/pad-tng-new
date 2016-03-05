<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class jalan_klas extends CI_Controller
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
            'apps_model', 'jalan_klas_model'
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
		
        $this->load->view('vjalan_klas', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('id, kelasnm, nilai');
        $this->datatables->from('pad_rek_jalan_klas');
        $this->datatables->numeric_column('2');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_jalan_klas = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('kelasnm','Uraian','required|trim');
		$this->form_validation->set_rules('nilai','Nilai','required');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['kelasnm'] = $this->input->post('kelasnm');
		$data['nilai'] = pad_to_decimal($this->input->post('nilai'));
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('jalan_klas'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('jalan_klas/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'kelasnm' => empty($input_post['kelasnm']) ? NULL : $input_post['kelasnm'],
				'nilai' => empty($input_post['nilai']) ? NULL : $input_post['nilai'],
            );
            $this->jalan_klas_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('jalan_klas'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vjalan_klas_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('jalan_klas'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("jalan_klas/update/{$p_id}");
        
        if ($p_id && $get = $this->jalan_klas_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['kelasnm'] = empty($get->kelasnm) ? NULL : $get->kelasnm;
			$data['dt']['nilai'] = empty($get->nilai) ? NULL : $get->nilai;
			
			$this->load->view('vjalan_klas_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('jalan_klas'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("jalan_klas/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'kelasnm' => empty($input_post['kelasnm']) ? NULL : $input_post['kelasnm'],
				'nilai' => empty($input_post['nilai']) ? NULL : $input_post['nilai'],
            );
            
            $this->jalan_klas_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('jalan_klas'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vjalan_klas_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('jalan_klas'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->jalan_klas_model->get($id)) {
            $this->jalan_klas_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('jalan_klas'));
        } else {
            show_404();
        }
    }
}