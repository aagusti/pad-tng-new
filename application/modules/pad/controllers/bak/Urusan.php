<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Urusan extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('login')) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$module = 'referensi';
		$this->load->library('module_auth',array('module'=>$module));
		
		$this->load->model(array('apps_model'));
		$this->load->model(array('urusan_model'));
	}

	public function index() {
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect('info');
		}

		$data['current'] = 'referensi';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vurusan', $data);
	}
	
	function grid() {
		$i=0;
		if($query = $this->urusan_model->get_all()) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
				$responce->aaData[$i][]=$row->kode;
				$responce->aaData[$i][]=$row->nama;
				$i++;
			}
		} else {
			$responce->sEcho=1;
			$responce->iTotalRecords="0";
			$responce->iTotalDisplayRecords="0";
			$responce->aaData=array();
		}
		echo json_encode($responce);
	}

	//admin
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('kode', 'Kode', 'required|min_length[1]');
		$this->form_validation->set_rules('nama', 'Uraian', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
		
		return $data;
	}
	
	public function add() {
		if(!$this->module_auth->create) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
			redirect(active_module_url('urusan'));
		}
		$data['current']     = 'referensi';
		$data['faction']     = active_module_url('urusan/add');
		$data['dt']          = $this->fpost();
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama')
			);
			$this->urusan_model->save($data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('urusan'));
		}
		$this->load->view('vurusan_form',$data);
	}
	
	public function edit() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url('urusan'));
		}
		$data['current']   = 'referensi';
		$data['faction']   = active_module_url('urusan/update');
			
		$id = $this->uri->segment(4);
		if($id && $get = $this->urusan_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['kode'] = $get->kode; 
			$data['dt']['nama'] = $get->nama;
			
			$this->load->view('vurusan_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url('urusan'));
		}
		$data['current'] = 'referensi';
		$data['faction'] = active_module_url('urusan/update');
		$data['dt'] = $this->fpost();
				
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama')
			);
			$this->urusan_model->update($this->input->post('id'), $data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('urusan'));
		}
		$this->load->view('vurusan_form',$data);
	}
	
	public function delete() {
		if(!$this->module_auth->delete) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
			redirect(active_module_url('urusan'));
		}
		
		$id = $this->uri->segment(4);
		if($id && $this->urusan_model->get($id)) {
			$this->urusan_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url('urusan'));
		} else {
			show_404();
		}
	}
}