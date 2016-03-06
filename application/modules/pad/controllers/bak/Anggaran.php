<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class anggaran extends CI_Controller
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
            'apps_model', 'anggaran_model'
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
		
        $this->load->view('vanggaran', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('a.id, get_rekening(r.kode) as kode, tahun, status_anggaran, target, 
            bulan1, bulan2, bulan3, bulan4, bulan5, bulan6', false);
        $this->datatables->from('pad_anggaran a');
        $this->datatables->join('pad_rekening r', 'a.rekening_id = r.id');
        
		$this->datatables->where('a.tahun', pad_tahun_anggaran());
        
        $this->datatables->rupiah_column('3,4,5,6,7,8,9,10');
        echo $this->datatables->generate();
    }

    function grid2()
    {       
        $this->load->library('Datatables');
        $this->datatables->select('a.id, get_rekening(r.kode) as kode, tahun, status_anggaran, target, bulan7,
            bulan8, bulan9, bulan10, bulan11, bulan12, jumlah', false);
        $this->datatables->from('pad_anggaran a');
        $this->datatables->join('pad_rekening r', 'a.rekening_id = r.id');
        
        $this->datatables->where('a.tahun', pad_tahun_anggaran());
        
        $this->datatables->rupiah_column('3,4,5,6,7,8,9,10');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_anggaran = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('rekening_id','Rekening','required|numeric');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['rekening_id'] = $this->input->post('rekening_id');
		$data['tahun'] = $this->input->post('tahun');
        $data['status_anggaran'] = $this->input->post('status_anggaran');
        $data['target'] = pad_to_decimal($this->input->post('target'));
        $data['bulan1'] = pad_to_decimal($this->input->post('bulan1'));
        $data['bulan2'] = pad_to_decimal($this->input->post('bulan2'));
        $data['bulan3'] = pad_to_decimal($this->input->post('bulan3'));
        $data['bulan4'] = pad_to_decimal($this->input->post('bulan4'));
        $data['bulan5'] = pad_to_decimal($this->input->post('bulan5'));
        $data['bulan6'] = pad_to_decimal($this->input->post('bulan6'));
        $data['bulan7'] = pad_to_decimal($this->input->post('bulan7'));
        $data['bulan8'] = pad_to_decimal($this->input->post('bulan8'));
        $data['bulan9'] = pad_to_decimal($this->input->post('bulan9'));
        $data['bulan10'] = pad_to_decimal($this->input->post('bulan10'));
        $data['bulan11'] = pad_to_decimal($this->input->post('bulan11'));
        $data['bulan12'] = pad_to_decimal($this->input->post('bulan12'));
        $data['jumlah'] = pad_to_decimal($this->input->post('jumlah'));

        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('anggaran'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('anggaran/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
                'rekening_id' => empty($input_post['rekening_id']) ? NULL : $input_post['rekening_id'],
                'tahun' => empty($input_post['tahun']) ? NULL : $input_post['tahun'],
                'status_anggaran' => empty($input_post['status_anggaran']) ? NULL : $input_post['status_anggaran'],
                'target' => empty($input_post['target']) ? NULL : $input_post['target'],
                'bulan1' => empty($input_post['bulan1']) ? NULL : $input_post['bulan1'],
                'bulan2' => empty($input_post['bulan2']) ? NULL : $input_post['bulan2'],
                'bulan3' => empty($input_post['bulan3']) ? NULL : $input_post['bulan3'],
                'bulan4' => empty($input_post['bulan4']) ? NULL : $input_post['bulan4'],
                'bulan5' => empty($input_post['bulan5']) ? NULL : $input_post['bulan5'],
                'bulan6' => empty($input_post['bulan6']) ? NULL : $input_post['bulan6'],
                'bulan7' => empty($input_post['bulan7']) ? NULL : $input_post['bulan7'],
                'bulan8' => empty($input_post['bulan8']) ? NULL : $input_post['bulan8'],
                'bulan9' => empty($input_post['bulan9']) ? NULL : $input_post['bulan9'],
                'bulan10' => empty($input_post['bulan10']) ? NULL : $input_post['bulan10'],
                'bulan11' => empty($input_post['bulan11']) ? NULL : $input_post['bulan11'],
                'bulan12' => empty($input_post['bulan12']) ? NULL : $input_post['bulan12'],
                'jumlah' => empty($input_post['jumlah']) ? NULL : $input_post['jumlah'],
				'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(), 
            );
            $this->anggaran_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('anggaran'));
        }
        
		$select_data = $this->load->model('rekening_model')->get_select(4);
		$options     = array();
        if($select_data)
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kode.' | '.$row->nama;
		}
		$js                        = 'id="rekening_id" class="input-xlarge" required ';
		$data['select_rekening'] = form_dropdown('rekening_id', $options, '', $js);
		
        $data['dt']      = $post_data;
        $this->load->view('vanggaran_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('anggaran'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("anggaran/update/{$p_id}");
        
        if ($p_id && $get = $this->anggaran_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['tahun'] = empty($get->tahun) ? NULL : $get->tahun;
			$data['dt']['rekening_id'] = empty($get->rekening_id) ? NULL : $get->rekening_id;
            $data['dt']['status_anggaran'] = empty($get->status_anggaran) ? NULL : $get->status_anggaran;
            $data['dt']['target'] = empty($get->target) ? NULL : $get->target;
			$data['dt']['bulan1'] = empty($get->bulan1) ? NULL : $get->bulan1;
            $data['dt']['bulan2'] = empty($get->bulan2) ? NULL : $get->bulan2;
            $data['dt']['bulan3'] = empty($get->bulan3) ? NULL : $get->bulan3;
            $data['dt']['bulan4'] = empty($get->bulan4) ? NULL : $get->bulan4;
            $data['dt']['bulan5'] = empty($get->bulan5) ? NULL : $get->bulan5;
            $data['dt']['bulan6'] = empty($get->bulan6) ? NULL : $get->bulan6;
            $data['dt']['bulan7'] = empty($get->bulan7) ? NULL : $get->bulan7;
            $data['dt']['bulan8'] = empty($get->bulan8) ? NULL : $get->bulan8;
            $data['dt']['bulan9'] = empty($get->bulan9) ? NULL : $get->bulan9;
            $data['dt']['bulan10'] = empty($get->bulan10) ? NULL : $get->bulan10;
            $data['dt']['bulan11'] = empty($get->bulan11) ? NULL : $get->bulan11;
            $data['dt']['bulan12'] = empty($get->bulan12) ? NULL : $get->bulan12;
            $data['dt']['jumlah'] = empty($get->jumlah) ? NULL : $get->jumlah;

			$select_data = $this->load->model('rekening_model')->get_select(4);
			$options     = array();
			foreach ($select_data as $row) {
				$options[$row->id] = $row->kode.' | '.$row->nama;
			}
			$js                        = 'id="rekening_id" class="input-xxlarge" required ';
			$data['select_rekening'] = form_dropdown('rekening_id', $options, $get->rekening_id, $js);
			
			$this->load->view('vanggaran_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('anggaran'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("anggaran/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				//'tahun' => pad_tahun_anggaran(),
				//'unit_id' => sipkd_default_unit(), //pad_pemda_unitid() 
				'rekening_id' => empty($input_post['rekening_id']) ? NULL : $input_post['rekening_id'],
                'tahun' => empty($input_post['tahun']) ? NULL : $input_post['tahun'],
                'status_anggaran' => empty($input_post['status_anggaran']) ? NULL : $input_post['status_anggaran'],
                'target' => empty($input_post['target']) ? NULL : $input_post['target'],
                'bulan1' => empty($input_post['bulan1']) ? NULL : $input_post['bulan1'],
				'bulan2' => empty($input_post['bulan2']) ? NULL : $input_post['bulan2'],
                'bulan3' => empty($input_post['bulan3']) ? NULL : $input_post['bulan3'],
                'bulan4' => empty($input_post['bulan4']) ? NULL : $input_post['bulan4'],
                'bulan5' => empty($input_post['bulan5']) ? NULL : $input_post['bulan5'],
                'bulan6' => empty($input_post['bulan6']) ? NULL : $input_post['bulan6'],
                'bulan7' => empty($input_post['bulan7']) ? NULL : $input_post['bulan7'],
                'bulan8' => empty($input_post['bulan8']) ? NULL : $input_post['bulan8'],
                'bulan9' => empty($input_post['bulan9']) ? NULL : $input_post['bulan9'],
                'bulan10' => empty($input_post['bulan10']) ? NULL : $input_post['bulan10'],
                'bulan11' => empty($input_post['bulan11']) ? NULL : $input_post['bulan11'],
                'bulan12' => empty($input_post['bulan12']) ? NULL : $input_post['bulan12'],
                'jumlah' => empty($input_post['jumlah']) ? NULL : $input_post['jumlah'],

                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(), 
            );
            
            $this->anggaran_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('anggaran'));
        }

		$get = $post_data;
		$select_data = $this->load->model('rekening_model')->get_select(4);
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kode.' | '.$row->nama;
		}
		$js                        = 'id="rekening_id" class="input-xxlarge" required ';
		$data['select_rekening'] = form_dropdown('rekening_id', $options, $get->rekening_id, $js);
			
        $data['dt'] = $post_data;		
		$this->load->view('vanggaran_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('anggaran'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->anggaran_model->get($id)) {
            $this->anggaran_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('anggaran'));
        } else {
            show_404();
        }
    }
}