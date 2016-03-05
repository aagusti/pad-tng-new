<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pajak extends CI_Controller
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
            'apps_model', 'pajak_model'
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
		
        $this->load->view('vpajak', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('p.id, u.nama, r.kode, p.rekening_kd_sub, r.nama as rekeningnm, 
        						   p.nama as pajaknm, p.tmt, p.enabled as enable');
        $this->datatables->from('pad_jenis_pajak p');
        $this->datatables->join('pad_rekening r', 'p.rekening_id = r.id');
        $this->datatables->join('pad_usaha u', 'p.usaha_id = u.id');
        $this->datatables->date_column('6');
        $this->datatables->checkbox_column('7');
        echo $this->datatables->generate();
    }

    function grid_reklame()
    {		
    	$id_reklame = pad_reklame_id();
        $this->load->library('Datatables');
        $this->datatables->select('p.id, u.nama, r.kode, p.rekening_kd_sub, r.nama as rekeningnm, 
        						   p.nama as pajaknm, p.tmt, p.enabled as enable');
        $this->datatables->from('pad_jenis_pajak p');
        $this->datatables->join('pad_rekening r', 'p.rekening_id = r.id');
        $this->datatables->join('pad_usaha u', 'p.usaha_id = u.id');
        $this->datatables->where('usaha_id', $id_reklame);
        $this->datatables->date_column('6');
        $this->datatables->checkbox_column('7');
        echo $this->datatables->generate();
    }

    function grid_tarif()
    {		
        $pid = $this->uri->segment(4);
		
        $this->load->library('Datatables');
        $this->datatables->select('id, tarif, (tarif*100) as tarif_percent, minimal_omset, reklame, tmt, enabled');
        $this->datatables->from('pad_tarif_pajak');
        $this->datatables->where('pajak_id', $pid);
        $this->datatables->date_column('5');
        $this->datatables->checkbox_column('6');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_pajak = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama','Pajak','required|trim');
		$this->form_validation->set_rules('jatuhtempo','jatuhtempo','required|numeric');
		$this->form_validation->set_rules('rekening_id','Rekening','required|numeric');
		$this->form_validation->set_rules('usaha_id','Usaha','required|numeric');
		$this->form_validation->set_rules('rekdenda_id','Rekening Denda','required|numeric');
    }
    
    private function fpost()
    {
		$data['nama'] = $this->input->post('nama');
		$data['jatuhtempo'] = $this->input->post('jatuhtempo');
		$data['carahitung'] = $this->input->post('carahitung');
		$data['rekening_id'] = $this->input->post('rekening_id');
		$data['rekening_kd_sub'] = $this->input->post('rekening_kd_sub');
		$data['usaha_id'] = $this->input->post('usaha_id');
		$data['rekdenda_id'] = $this->input->post('rekdenda_id');
		$data['id'] = $this->input->post('id');
		$data['tmt'] = $this->input->post('tmt');
		$data['enabled'] = $this->input->post('enabled');
		$data['created'] = $this->input->post('created');
		$data['create_uid'] = $this->input->post('create_uid');
		$data['updated'] = $this->input->post('updated');
		$data['update_uid'] = $this->input->post('update_uid');
		$data['multiple'] = $this->input->post('multiple');
		$data['masapajak'] = $this->input->post('masapajak');
		$data['jalan_kelas_id'] = $this->input->post('jalan_kelas_id');
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('pajak'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('pajak/add');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'jatuhtempo' => $input_post['jatuhtempo'],
				'rekening_id' => empty($input_post['rekening_id']) ? NULL : $input_post['rekening_id'],
				'rekening_kd_sub' => empty($input_post['rekening_kd_sub']) ? NULL : $input_post['rekening_kd_sub'],
				'usaha_id' => empty($input_post['usaha_id']) ? NULL : $input_post['usaha_id'],
				'rekdenda_id' => empty($input_post['rekdenda_id']) ? NULL : $input_post['rekdenda_id'],
				'masapajak' => empty($input_post['masapajak']) ? NULL : $input_post['masapajak'],
				'jalan_kelas_id' => empty($input_post['jalan_kelas_id']) ? NULL : $input_post['jalan_kelas_id'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'multiple' => empty($input_post['multiple']) ? 0 : 1,
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
				'created' => date('Y-m-d h:i:s'),
				'create_uid' => sipkd_user_id(),
            );
            $new_id = $this->pajak_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan. Silahkan lengkapi Tarif Pajaknya.');
            //redirect(active_module_url('pajak'));
			redirect(active_module_url('pajak/edit/'.$new_id));
        }
        
		$options = array(
			0 => 'Akhir Bulan',
			1 => '30 Hari Penetapan',
		);
		$js = 'id="so" class="input-medium" required ';
		$data['select_jatuhtempo'] = form_dropdown('jatuhtempo', $options, '', $js);
		
		$options = array(
			1 => 'Tahunan',
			2 => 'Semesteran',
			3 => 'Triwulanan',
			4 => 'Bulanan',
			5 => 'Mingguan',
			6 => 'Harian',
		);
		$js = 'id="so" class="input-medium" required ';
		$data['select_masapajak'] = form_dropdown('masapajak', $options, '', $js);
		
		$select_data = $this->load->model('usaha_model')->get_select();
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                        = 'id="usaha_id" class="input-medium" required ';
		$data['select_usaha'] = form_dropdown('usaha_id', $options, '', $js);
		
		$select_data = $this->load->model('rekening_model')->get_select(false,0);
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kode.' | '.$row->nama;
		}
		$js                        = 'id="rekening_id" class="input-xlarge" required ';
		$data['select_rekening'] = form_dropdown('rekening_id', $options, '', $js);
		
		$select_data = $this->load->model('rekening_model')->get_select(false,0);
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kode.' | '.$row->nama;
		}
		$js                        = 'id="rekdenda_id" class="input-xlarge" required ';
		$data['select_rekdenda'] = form_dropdown('rekdenda_id', $options, '', $js);
		
        $data['dt']      = $post_data;
        $this->load->view('vpajak_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('pajak'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("pajak/update/{$p_id}");
        
        if ($p_id && $get = $this->pajak_model->get($p_id)) {
			$data['dt']['nama'] = empty($get->nama) ? NULL : $get->nama;
			$data['dt']['jatuhtempo'] = $get->jatuhtempo;
			$data['dt']['rekening_id'] = empty($get->rekening_id) ? NULL : $get->rekening_id;
			$data['dt']['rekening_kd_sub'] = $get->rekening_kd_sub;
			$data['dt']['usaha_id'] = empty($get->usaha_id) ? NULL : $get->usaha_id;
			$data['dt']['rekdenda_id'] = empty($get->rekdenda_id) ? NULL : $get->rekdenda_id;
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['enabled'] = $get->enabled==1 ? 'checked' : '';
			$data['dt']['multiple'] = $get->multiple==1 ? 'checked' : '';
			$data['dt']['masapajak'] = empty($get->masapajak) ? NULL : $get->masapajak;
			
			$options = array(
				0 => 'Akhir Bulan',
				1 => '30 Hari Penetapan',
			);
			$js = 'id="so" class="input-medium" required ';
			$data['select_jatuhtempo'] = form_dropdown('jatuhtempo', $options, $get->jatuhtempo, $js);
			
			$options = array(
				1 => 'Tahunan',
				2 => 'Semesteran',
				3 => 'Triwulanan',
				4 => 'Bulanan',
				5 => 'Mingguan',
				6 => 'Harian',
			);
			$js = 'id="so" class="input-medium" required ';
			$data['select_masapajak'] = form_dropdown('masapajak', $options, $get->masapajak, $js);
			
			$select_data = $this->load->model('usaha_model')->get_select();
			$options     = array();
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}
			$js                        = 'id="usaha_id" class="input-medium" required ';
			$data['select_usaha'] = form_dropdown('usaha_id', $options, $get->usaha_id, $js);
			
            $select_data = $this->load->model('rekening_model')->get_select(false,0);
			$options     = array();
			foreach ($select_data as $row) {
				$options[$row->id] = $row->kode.' | '.$row->nama;
			}
			$js                        = 'id="rekening_id" class="input-xlarge" required ';
			$data['select_rekening'] = form_dropdown('rekening_id', $options, $get->rekening_id, $js);
			
			$select_data = $this->load->model('rekening_model')->get_select(false,0);
			$options     = array();
			foreach ($select_data as $row) {
				$options[$row->id] = $row->kode.' | '.$row->nama;
			}
			$js                        = 'id="rekdenda_id" class="input-xlarge" required ';
			$data['select_rekdenda'] = form_dropdown('rekdenda_id', $options, $get->rekdenda_id, $js);
			
			$this->load->view('vpajak_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('pajak'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("pajak/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				'nama' => empty($input_post['nama']) ? NULL : $input_post['nama'],
				'jatuhtempo' => $input_post['jatuhtempo'],
				'rekening_id' => empty($input_post['rekening_id']) ? NULL : $input_post['rekening_id'],
				'rekening_kd_sub' => empty($input_post['rekening_kd_sub']) ? NULL : $input_post['rekening_kd_sub'],
				'usaha_id' => empty($input_post['usaha_id']) ? NULL : $input_post['usaha_id'],
				'rekdenda_id' => empty($input_post['rekdenda_id']) ? NULL : $input_post['rekdenda_id'],
				'masapajak' => empty($input_post['masapajak']) ? NULL : $input_post['masapajak'],
				'jalan_kelas_id' => empty($input_post['jalan_kelas_id']) ? NULL : $input_post['jalan_kelas_id'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'multiple' => empty($input_post['multiple']) ? 0 : 1,
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
				'updated' => date('Y-m-d h:i:s'),
				'update_uid' => sipkd_user_id(),
            );
            
            $this->pajak_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('pajak'));
        }

		$get = $post_data;
		$options = array(
			0 => 'Akhir Bulan',
			1 => '30 Hari Penetapan',
		);
		$js = 'id="so" class="input-medium" required ';
		$data['select_jatuhtempo'] = form_dropdown('jatuhtempo', $options, $get->jatuhtempo, $js);
		
		$options = array(
			1 => 'Tahunan',
			2 => 'Semesteran',
			3 => 'Triwulanan',
			4 => 'Bulanan',
			5 => 'Mingguan',
			6 => 'Harian',
		);
		$js = 'id="so" class="input-medium" required ';
		$data['select_masapajak'] = form_dropdown('masapajak', $options, $get->masapajak, $js);
		
		$select_data = $this->load->model('usaha_model')->get_select();
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                        = 'id="usaha_id" class="input-medium" required ';
		$data['select_usaha'] = form_dropdown('usaha_id', $options, $get->usaha_id, $js);
		
		$select_data = $this->load->model('rekening_model')->get_select(false,0);
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kode.' | '.$row->nama;
		}
		$js                        = 'id="rekening_id" class="input-xlarge" required ';
		$data['select_rekening'] = form_dropdown('rekening_id', $options, $get->rekening_id, $js);
		
		$select_data = $this->load->model('rekening_model')->get_select(false,0);
		$options     = array();
		foreach ($select_data as $row) {
			$options[$row->id] = $row->kode.' | '.$row->nama;
		}
		$js                        = 'id="rekdenda_id" class="input-xlarge" required ';
		$data['select_rekdenda'] = form_dropdown('rekdenda_id', $options, $get->rekdenda_id, $js);

        $data['dt'] = $post_data;		
		$this->load->view('vpajak_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('pajak'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->pajak_model->get($id)) {
            $this->pajak_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('pajak'));
        } else {
            show_404();
        }
    }
	
	// tarif
    function get_tarif() {
        $tarif_id    = $this->uri->segment(4);
        $data = $this->load->model('pajak_tarif_model')->get($tarif_id);
		$data->tmt = empty($data->tmt) ? NULL : date('d-m-Y', strtotime($data->tmt));
        echo json_encode($data);
    }
	
	function proces_tarif() {
        $mode = $this->uri->segment(4);					
		$tarif_id = $this->input->get_post('tarif_id');
		$aktif = $this->input->get_post('p_enabled');
		
		$update_data = array(
			'pajak_id' => $this->input->get_post('pajak_id'),
			'tarif' => pad_to_decimal($this->input->get_post('p_tarif')),
			'reklame' => pad_to_decimal($this->input->get_post('p_reklame')),
			'minimal_omset' => pad_to_decimal($this->input->get_post('p_minimal_omset')),
			'tmt' => trim($this->input->get_post('p_tmt')) == '' ? NULL : date('Y-m-d', strtotime($this->input->get_post('p_tmt'))),
			'enabled' => empty($aktif) ? 0 : 1,
		);
		
		$tarif_model = $this->load->model('pajak_tarif_model');
		if ($mode == 'add') {			
			$new_data = array(
				'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
			);
			$update_data = array_merge($update_data, $new_data);
			$tarif_model->save($update_data);
		}
		
		if ($mode == 'edit') {
			$new_data = array(
				'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),
			);
			$update_data = array_merge($update_data, $new_data);
			$tarif_model->update($tarif_id, $update_data);
		}	
	}
	
    public function delete_tarif() {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('subjek_pajak'));
        }
        
        $id = $this->uri->segment(4);
        $model = $this->load->model('pajak_tarif_model');
        if ($id && $model->get($id)) {
            $model->delete($id);
			echo "sip";
        } else {
            show_404();
        }
    }
}