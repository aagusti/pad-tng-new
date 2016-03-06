<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class usaha extends CI_Controller
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
            'apps_model', 'usaha_model'
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
		
        $this->load->view('vusaha', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('id, nama, so, tmt, enabled');
        $this->datatables->from('pad_usaha');
        $this->datatables->checkbox_column('4');
        $this->datatables->date_column('3');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_usaha = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama','nama','required|trim');
		$this->form_validation->set_rules('tmt','tmt','required');
		$this->form_validation->set_rules('so','so','required|trim');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['nama'] = $this->input->post('nama');
		$data['so'] = $this->input->post('so');
		$data['tmt'] = $this->input->post('tmt');
		$data['enabled'] = $this->input->post('enabled');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('usaha'));
        }
        
        $p_usaha_id = $this->uri->segment(4);
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('usaha/add/' . $p_usaha_id);
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'so' => empty($input_post['so']) ? NULL : $input_post['so'],  
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'created' => date('Y-m-d h:i:s'),
				'create_uid' => sipkd_user_id(),
            );
            $this->usaha_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('usaha'));
        }
        
        $data['dt']      = $post_data;
		
		$options              = array(
			'S' => 'Self',
			'O' => 'Office',
		);
		$js                   = 'id="so" class="input-small" required ';
		$data['select_so'] = form_dropdown('so', $options, '', $js);
		
        $this->load->view('vusaha_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('usaha'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("usaha/update/{$p_id}");
        
        if ($p_id && $get = $this->usaha_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';
			$data['dt']['so'] = empty($get->so) ? NULL : $get->so;
			
			$options              = array(
				'S' => 'Self',
				'O' => 'Office',
			);
			$js                   = 'id="so" class="input-small" required ';
			$data['select_so'] = form_dropdown('so', $options, $get->so, $js);
			
			$this->load->view('vusaha_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('usaha'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("usaha/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'so' => empty($input_post['so']) ? NULL : $input_post['so'],  
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'updated' => date('Y-m-d h:i:s'),
				'update_uid' => sipkd_user_id(),
            );
            
            $this->usaha_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('usaha'));
        }
        
        $data['dt'] = $post_data;
		$get = $post_data;
		$options              = array(
			'S' => 'Self',
			'O' => 'Office',
		);
		$js                   = 'id="so" class="input-small" required ';
		$data['select_so'] = form_dropdown('so', $options, $get->so, $js);
		
		$this->load->view('vusaha_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('usaha'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->usaha_model->get($id)) {
            $this->usaha_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('usaha'));
        } else {
            show_404();
        }
    }
}