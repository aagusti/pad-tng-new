<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class air_manfaat extends CI_Controller
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
            'apps_model', 'air_manfaat_model'
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
        
        $this->load->view('vair_manfaat', $data);
    }
    
    function grid()
    {       
        $this->load->library('Datatables');
        $this->datatables->select('id, nama');
        $this->datatables->from('pad_air_manfaat');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_air_manfaat = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('nama','Uraian','required|trim');
    }
    
    private function fpost()
    {
        $data['id'] = $this->input->post('id');
        $data['nama'] = $this->input->post('nama');
        
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('air_manfaat'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('air_manfaat/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
                'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->air_manfaat_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_manfaat'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vair_manfaat_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_manfaat'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_manfaat/update/{$p_id}");
        
        if ($p_id && $get = $this->air_manfaat_model->get($p_id)) {
            $data['dt']['id'] = empty($get->id) ? NULL : $get->id;
            $data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;            
            $this->load->view('vair_manfaat_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_manfaat'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_manfaat/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
                'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            
            $this->air_manfaat_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_manfaat'));
        }
        
        $data['dt'] = $post_data;       
        $this->load->view('vair_manfaat_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('air_manfaat'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->air_manfaat_model->get($id)) {
            $this->air_manfaat_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('air_manfaat'));
        } else {
            show_404();
        }
    }
}