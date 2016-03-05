<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class coba extends CI_Controller
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
            'apps_model', 'coba_model'
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
        
        $this->load->view('vcoba', $data);
    }
    
    function grid()
    {       
      
        $this->load->library('Datatables');
        $this->datatables->select('id, tbl1, tbl2');
        $this->datatables->from('coba');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_coba = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('tbl1','Tbl1','required|trim');
        $this->form_validation->set_rules('tbl2','Tbl2','required|trim');
    }
    
    private function fpost()
    {
        $data['id'] = $this->input->post('id');
        $data['tbl1'] = $this->input->post('tbl1');
        $data['tbl2'] = $this->input->post('tbl2');
        
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('coba'));
        }
        
        $p_coba_id = $this->uri->segment(4);
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('coba/add/' . $p_coba_id);
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
                'tbl1' => empty($input_post['tbl1']) ? NULL : $input_post['tbl1'],
                'tbl2' => empty($input_post['tbl2']) ? NULL : $input_post['tbl2'], 
            );
            $this->coba_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('coba'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vcoba_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('coba'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("coba/update/{$p_id}");
        
        if ($p_id && $get = $this->coba_model->get($p_id)) {
            $data['dt']['id'] = empty($get->id) ? NULL : $get->id;
            $data['dt']['tbl1'] = empty($get->tbl1) ? NULL : $get->tbl1;
            $data['dt']['tbl2'] = empty($get->tbl2) ? NULL : $get->tbl2;
            
            $this->load->view('vcoba_form', $data);
        } else {
            show_404();
        }
    }
    
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('coba'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("coba/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
                'tbl1' => empty($input_post['tbl1']) ? NULL : $input_post['tbl1'],
                'tbl2' => empty($input_post['tbl2']) ? NULL : $input_post['tbl2'],
            );
            
            $this->coba_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('coba'));
        }
        
        $data['dt'] = $post_data;       
        $this->load->view('vcoba_form', $data);
    }

    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('coba'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->coba_model->get($id)) {
            $this->coba_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('coba'));
        } else {
            show_404();
        }
    }
}
