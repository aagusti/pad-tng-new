<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class jalan extends CI_Controller
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
            'apps_model', 'jalan_model'
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
		
        $this->load->view('vjalan', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('j.id, kode, j.nama, jk.nama as klasnm, j.tmt, j.enabled');
        $this->datatables->from('pad_reklame_jalan j');
        $this->datatables->join('pad_reklame_kelas_jalan jk', 'jk.id = j.jalan_kelas_id');
        $this->datatables->date_column('4');
        $this->datatables->checkbox_column('5');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_jalan = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('kode','Kode','required|trim');
		$this->form_validation->set_rules('nama','Nama','required|trim');
		$this->form_validation->set_rules('jalan_kelas_id','Klas Jalan','required|numeric');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
		$data['jalan_kelas_id'] = $this->input->post('jalan_kelas_id');
		$data['tmt'] = $this->input->post('tmt');
		$data['enabled'] = $this->input->post('enabled');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('jalan'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('jalan/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'jalan_kelas_id' => empty($input_post['jalan_kelas_id']) ? NULL : $input_post['jalan_kelas_id'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),    
            );
            $this->jalan_model->save($update_data);
			
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('jalan'));
        }
        
		$select_data = $this->load->model('jalan_kelas_model')->get_select2();
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                        = 'id="jalan_kelas_id" class="input-xlarge" required ';
		$data['select_jalan_kelas'] = form_dropdown('jalan_kelas_id', $options, '', $js);
		
        $data['dt']      = $post_data;
        $this->load->view('vjalan_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('jalan'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("jalan/update/{$p_id}");
        
        if ($p_id && $get = $this->jalan_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['kode'] = empty($get->kode) ? NULL : $get->kode;
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
			$data['dt']['jalan_kelas_id'] = empty($get->jalan_kelas_id) ? NULL : $get->jalan_kelas_id;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';
			
            $select_data = $this->load->model('jalan_kelas_model')->get_select2();
            $options     = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }
            $js                        = 'id="jalan_kelas_id" class="input-xlarge" required ';
            $data['select_jalan_kelas'] = form_dropdown('jalan_kelas_id', $options, $get->jalan_kelas_id, $js);
			
			$this->load->view('vjalan_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('jalan'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("jalan/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'kode' => empty($input_post['kode']) ? NULL : $input_post['kode'],
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'jalan_kelas_id' => empty($input_post['jalan_kelas_id']) ? NULL : $input_post['jalan_kelas_id'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            
            $this->jalan_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('jalan'));
        }
        
		$get = $post_data;
		$select_data = $this->load->model('jalan_kelas_model')->get_select2();
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                        = 'id="jalan_kelas_id" class="input-xlarge" required ';
		$data['select_jalan_kelas'] = form_dropdown('jalan_kelas_id', $options, $get->jalan_kelas_id, $js);
		
        $data['dt'] = $post_data;		
		$this->load->view('vjalan_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('jalan'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->jalan_model->get($id)) {
            $this->jalan_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('jalan'));
        } else {
            show_404();
        }
    }
}
