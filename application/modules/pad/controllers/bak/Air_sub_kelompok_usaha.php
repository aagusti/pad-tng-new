<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class air_sub_kelompok_usaha extends CI_Controller
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
            'apps_model', 'air_sub_kelompok_usaha_model'
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

        $induk_id = $this->uri->segment(4);
        $this->session->set_userdata('induk_id',$induk_id);  

        $this->load->view('vair_sub_kelompok_usaha', $data);
    }
    
    function grid()
    {       
        $induk_id = $this->session->userdata('induk_id');
        $this->load->library('Datatables');
        $this->datatables->select('d.id, d.kode, d.nama, s.nama as induk');
        $this->datatables->where('d.level',1);
        $this->datatables->where('d.induk_id',$induk_id);
        $this->datatables->from('pad_air_kelompok_usaha d');
        $this->datatables->join('pad_air_kelompok_usaha s','s.id = d.induk_id');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_air_sub_kelompok_usaha = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('nama','Uraian','required|trim');
    }
    
    private function fpost()
    {
        $data['id'] = $this->input->post('id');
        $data['induk_id'] = $this->input->post('induk_id');
        $data['kode'] = $this->input->post('kode');
        $data['nama'] = $this->input->post('nama');
        
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('air_sub_kelompok_usaha'));
        }
        
        $post_data  = $this->fpost();
        $induk_id = $this->session->userdata('induk_id');  


        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('air_sub_kelompok_usaha/add');
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
                'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
                'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'induk_id' => empty($input_post['induk_id']) ? NULL : $input_post['induk_id'],
                'level' => 1 ,
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->air_sub_kelompok_usaha_model->save($update_data);
            $induk_id = $input_post['induk_id'];
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_sub_kelompok_usaha/index/'.$induk_id));
        }
        
        $data['dt']      = $post_data;
        $select_data = $this->load->model('air_sub_kelompok_usaha_model')->get_select();
        $options     = array();
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                        = 'id="induk_id" class="form-control" required ';
        $data['select_kelompok_usaha'] = form_dropdown('induk_id', $options, $induk_id, $js);
        $this->load->view('vair_sub_kelompok_usaha_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_sub_kelompok_usaha'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_sub_kelompok_usaha/update/{$p_id}");
        
        if ($p_id && $get = $this->air_sub_kelompok_usaha_model->get($p_id)) {
            $data['dt']['id'] = empty($get->id) ? NULL : $get->id;
            $data['dt']['kode'] = empty($get->kode) ? NULL : $get->kode;  
            $data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama; 

            $select_data = $this->load->model('air_sub_kelompok_usaha_model')->get_select();
            $options     = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }
        $js                        = 'id="induk_id" class="form-control" required ';
        $data['select_kelompok_usaha'] = form_dropdown('induk_id', $options, $get->induk_id, $js);   


            $this->load->view('vair_sub_kelompok_usaha_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_sub_kelompok_usaha'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_sub_kelompok_usaha/update/{$p_id}");
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
                'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
                'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'induk_id' => empty($input_post['induk_id']) ? NULL : $input_post['induk_id'],
                'level' => 1 ,
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            $induk_id = $input_post['induk_id'];
            $this->air_sub_kelompok_usaha_model->update($p_id, $update_data);
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_sub_kelompok_usaha/index/'.$induk_id));
        }
        
        $data['dt'] = $post_data;       
        $this->load->view('vair_sub_kelompok_usaha_form', $data);
    }

   function get_sub_kelompok_usaha()
    {
        $induk_id = $this->uri->segment(4);
        $model = $this->load->model('air_sub_kelompok_usaha_model');

        if ($rows = $model->get_select_sub_kelompok_usaha($induk_id)) {
            $jalan = "";
            foreach ($rows as $row) {
                $jalan .= "<option value={$row->id}>{$row->nama}</option>";
            }
            echo $jalan;
        }
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('air_sub_kelompok_usaha'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->air_sub_kelompok_usaha_model->get($id)) {
            $this->air_sub_kelompok_usaha_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('air_sub_kelompok_usaha'));
        } else {
            show_404();
        }
    }
}