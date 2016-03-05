<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_reg extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
        $module = 'registrasi';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model', 'users_model', 
        ));
		
		$this->load->helper(active_module('pad_helper'));
    }
    
    public function index() {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $data['current'] = 'registrasi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vuser_reg', $data);
    }
    
    function grid() {
        $this->load->library('Datatables');
        $this->datatables->select('u.id, u.userid, u.nama, u.created, u.disabled', false);
        $this->datatables->from('users u');
        $this->datatables->join('user_groups ug', 'ug.user_id=u.id');
        $this->datatables->join('groups g', 'ug.group_id=g.id');
        $this->datatables->where('g.kode', 'esptpd_wp'); // <-------------------------------
        
        $this->datatables->date_column('3');
        $this->datatables->checkbox_column('4');
        
        echo $this->datatables->generate();
    }
	
    function grid_wp() {
        $this->load->library('Datatables',$this->load->database('pad', TRUE));
        $this->datatables->select('c.id, get_npwpd(c.id, true) as npwpd, c.nama as customernm, 
			(case when c.wpnama=\'\' then c.pnama else c.wpnama end) as nama, c.alamat', false);
        $this->datatables->from('pad_customer c');
        $this->datatables->join('(select cu.id, cu.usaha_id, u.nama, cu.customer_id 
			from pad_customer_usaha cu 
			inner join pad_usaha u on cu.usaha_id = u.id and u.id not in ('.pad_reklame_id().','.pad_air_tanah_id().')
            ) as cu', 'cu.customer_id = c.id');
        $this->datatables->where('c.rp', 'P');
					
        echo $this->datatables->generate();
    }
	
    //admin
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('userid', 'userid', 'required|min_length[1]');
		$this->form_validation->set_rules('nama', 'Uraian', 'required');
		$this->form_validation->set_rules('passwd', 'Password', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['userid'] = $this->input->post('userid');
		$data['nama'] = $this->input->post('nama');
		$data['passwd'] = $this->input->post('passwd');
		$data['nip'] = '-';
		$data['jabatan'] = 'Wajib Pajak';
		$data['disabled'] = $this->input->post('disabled') ? 'checked' :'';
		
		return $data;
	}
	
	//admin
	public function add() {
		$data['current']     = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']     = active_module_url('user_reg/add');
		$data['dt']          = $this->fpost();
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'userid' => $this->input->post('userid'),
				'nama' => $this->input->post('nama'),
				'passwd' => $this->input->post('passwd'),
				// 'nip' => $this->input->post('nip'),
				'jabatan' => 'Wajib Pajak',
				'disabled' => $this->input->post('disabled') ? 1 : 0,
				'created' => date('Y-m-d')
			);
			if($user_id = $this->users_model->save($data)) {
                $group_id = $this->db->query("SELECT id FROM groups WHERE kode='esptpd_wp'")->row()->id;
                $data = array(
                    'user_id'  => $user_id,
                    'group_id' => $group_id,
                );
                $this->db->insert('user_groups',$data);
            }
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('user_reg'));
		}
		$this->load->view('vuser_reg_form',$data);
	}
	
	public function edit() {
		$data['current']   = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('user_reg/update');
			
		$id = $this->uri->segment(4);
		if($id && $get = $this->users_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['userid'] = $get->userid; 
			$data['dt']['nama'] = $get->nama;
			$data['dt']['passwd'] = $get->passwd;
			$data['dt']['nip'] = $get->nip;
			$data['dt']['jabatan'] = $get->jabatan;
			$data['dt']['disabled'] = $get->disabled ? 'checked' : '';
			
			$this->load->view('vuser_reg_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('user_reg/update');
		$data['dt'] = $this->fpost();
					
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				'userid' => $this->input->post('userid'),
				'nama' => $this->input->post('nama'),
				'passwd' => $this->input->post('passwd'),
				// 'nip' => $this->input->post('nip'),
				'jabatan' => 'Wajib Pajak',
				'disabled' => $this->input->post('disabled') ? 1 : 0
			);
			$this->users_model->update($this->input->post('id'), $data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('user_reg'));
		}
		$this->load->view('vuser_reg_form',$data);
	}
	
	public function delete() {
		$id = $this->uri->segment(4);
		if($id && $this->users_model->get($id)) {
            $this->db->delete('user_groups',array('user_id' => $id));
            
            if($this->users_model->delete($id)) 
                $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            else
                $this->session->set_flashdata('msg_error', 'Gagal');
        
			redirect(active_module_url('user_reg'));
		} else {
			show_404();
		}
	}
}