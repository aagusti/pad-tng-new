<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class air_zona extends CI_Controller
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
            'apps_model', 'air_zona_model'
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
		
        $this->load->view('vair_zona', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('id, nama, indeks');
        $this->datatables->from('pad_air_zona');
        $this->datatables->numeric_column('2');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_air_zona = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama','Uraian','required|trim');
		$this->form_validation->set_rules('indeks','indeks','required');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['nama'] = $this->input->post('nama');
		$data['indeks'] = pad_to_decimal($this->input->post('indeks'));
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('air_zona'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('air_zona/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'indeks' => empty($input_post['indeks']) ? NULL : $input_post['indeks'],
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->air_zona_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_zona'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vair_zona_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_zona'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_zona/update/{$p_id}");
        
        if ($p_id && $get = $this->air_zona_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
			$data['dt']['indeks'] = empty($get->indeks) ? NULL : $get->indeks;
			
			$this->load->view('vair_zona_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_zona'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_zona/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'indeks' => empty($input_post['indeks']) ? NULL : $input_post['indeks'],
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            
            $this->air_zona_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_zona'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vair_zona_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('air_zona'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->air_zona_model->get($id)) {
            $this->air_zona_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('air_zona'));
        } else {
            show_404();
        }
    }
}