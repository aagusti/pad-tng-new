<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class rekening extends CI_Controller
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
            'apps_model', 'rekening_model'
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
		
        $this->load->view('vrekening', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('id, kode, nama, levelid, defsign, issummary, isppkd, tmt, enabled');
        $this->datatables->from('pad_rekening');
        $this->datatables->date_column('7');
        $this->datatables->checkbox_column('5,6,8');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_rekening = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('kode','Kode','required|trim');
		$this->form_validation->set_rules('nama','Nama','required|trim');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
		$data['levelid'] = $this->input->post('levelid');
		$data['issummary'] = $this->input->post('issummary');
		$data['defsign'] = $this->input->post('defsign');
		$data['isppkd'] = $this->input->post('isppkd');
		$data['tmt'] = $this->input->post('tmt');
		$data['enabled'] = $this->input->post('enabled');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('rekening'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('rekening/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'levelid' => empty($input_post['levelid']) ? NULL : $input_post['levelid'],
				'issummary' => empty($input_post['issummary']) ? 0 : 1,    
				'defsign' => empty($input_post['defsign']) ? NULL : $input_post['defsign'],
				'isppkd' => empty($input_post['isppkd']) ? 0 : 1,    
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,    
            );
            $this->rekening_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('rekening'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vrekening_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('rekening'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("rekening/update/{$p_id}");
        
        if ($p_id && $get = $this->rekening_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['kode'] = empty($get->kode) ? NULL : $get->kode;
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
			$data['dt']['levelid'] = empty($get->levelid) ? NULL : $get->levelid;
			$data['dt']['issummary'] = $get->issummary == 1 ? 'checked' : '';
			$data['dt']['isppkd'] = $get->isppkd == 1 ? 'checked' : '';
			$data['dt']['defsign'] = empty($get->defsign) ? NULL : $get->defsign;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';
			
			$this->load->view('vrekening_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('rekening'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("rekening/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'levelid' => empty($input_post['levelid']) ? NULL : $input_post['levelid'],
				'issummary' => empty($input_post['issummary']) ? 0 : 1,    
				'defsign' => empty($input_post['defsign']) ? NULL : $input_post['defsign'],
				'isppkd' => empty($input_post['isppkd']) ? 0 : 1,    
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,       
            );
            
            $this->rekening_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('rekening'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vrekening_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('rekening'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->rekening_model->get($id)) {
            $this->rekening_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('rekening'));
        } else {
            show_404();
        }
    }
}