<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class kecamatan extends CI_Controller
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
            'apps_model', 'kecamatan_model'
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
		
        $this->load->view('vkecamatan', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('k.id, k.kode, k.nama, z.nama as zona, k.tmt, k.enabled');
        $this->datatables->from('pad_kecamatan k');
        $this->datatables->join('pad_air_zona z', 'z.id = k.zona_id');

        $this->datatables->date_column('4');
        $this->datatables->checkbox_column('5');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_kecamatan = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('kode','Kode','required|trim|max_length[3]');
		$this->form_validation->set_rules('nama','Nama','required|trim');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
        $data['zona_id'] = $this->input->post('zona_id');
		$data['tmt'] = $this->input->post('tmt');
		$data['enabled'] = $this->input->post('enabled');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('kecamatan'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('kecamatan/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'zona_id' => empty($input_post['zona_id']) ? NULL : $input_post['zona_id'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,    
            );
            $this->kecamatan_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('kecamatan'));
        }
        $select_data = $this->load->model('air_zona_model')->get_select();
        $options     = array();
        if($select_data)
            foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                        = 'id="zona_id" class="input-xlarge" required ';
        $data['select_air_zona'] = form_dropdown('zona_id', $options, '', $js);

        $data['dt']      = $post_data;
        $this->load->view('vkecamatan_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('kecamatan'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("kecamatan/update/{$p_id}");
        
        if ($p_id && $get = $this->kecamatan_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['kode'] = empty($get->kode) ? NULL : $get->kode;
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
            $data['dt']['zona_id'] = empty($get->zona_id) ? NULL : $get->zona_id;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';

            $select_data = $this->load->model('air_zona_model')->get_select();
            $options     = array();
            if($select_data){
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }
            }
            $js                        = 'id="zona_id" class="input-xlarge" required ';
            $data['select_air_zona'] = form_dropdown('zona_id', $options, $get->zona_id, $js);
			
			$this->load->view('vkecamatan_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('kecamatan'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("kecamatan/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
                'zona_id' => empty($input_post['zona_id']) ? NULL : $input_post['zona_id'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
            );
            
            $this->kecamatan_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('kecamatan'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vkecamatan_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('kecamatan'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->kecamatan_model->get($id)) {
            $this->kecamatan_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('kecamatan'));
        } else {
            show_404();
        }
    }
}