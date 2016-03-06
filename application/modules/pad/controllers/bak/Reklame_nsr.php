<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class reklame_nsr extends CI_Controller
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
            'apps_model', 'reklame_nsr_model'
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
		
        $this->load->view('vreklame_nsr', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select('n.id, k.nama, m.media, satuan, njopr, nspr, nsr, tmt, n.enabled');
        $this->datatables->from('pad_reklame_nsr n');
        $this->datatables->join('pad_reklame_media m', 'n.media_id = m.id');
        $this->datatables->join('pad_reklame_kelas_jalan k', 'n.kelas_jalan_id = k.id');
        $this->datatables->rupiah_column('4,5,6');
        $this->datatables->date_column('7');
        $this->datatables->checkbox_column('8');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_reklame_nsr = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('satuan','Satuan','required');
		$this->form_validation->set_rules('njopr','NJOPR','required');
        $this->form_validation->set_rules('nspr','NSPR','required');
        $this->form_validation->set_rules('nsr','NSR','required');
        $this->form_validation->set_rules('tmt','TMT','required');

    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
        $data['kelas_jalan_id'] = $this->input->post('kelas_jalan_id');
		$data['media_id'] = $this->input->post('media_id');
        $data['satuan'] = $this->input->post('satuan');
        $data['njopr'] = $this->input->post('njopr');
        $data['nspr'] = $this->input->post('nspr');
        $data['nsr'] = $this->input->post('nsr');
        $data['tmt'] = $this->input->post('tmt');
        $data['kriteria'] = $this->input->post('kriteria');
        $data['enabled'] = $this->input->post('enabled');
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('reklame_nsr'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('reklame_nsr/add');

        $select_data = $this->load->model('jalan_kelas_model')->get_select2();
        $options     = array();
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                        = 'id="kelas_jalan_id" class="form-control" required ';
        $data['select_jalan_kelas'] = form_dropdown('kelas_jalan_id', $options, '', $js);

        $select_data = $this->load->model('reklame_media_model')->get_select();
        $options     = array();
        foreach ($select_data as $row) {
            $options[$row->id] = $row->media;
        }
        $js                        = 'id="media_id" class="form-control" required ';
        $data['select_media'] = form_dropdown('media_id', $options, '', $js);

        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
                'kelas_jalan_id' => empty($input_post['kelas_jalan_id']) ? NULL : $input_post['kelas_jalan_id'],
				'media_id' => empty($input_post['media_id']) ? NULL : $input_post['media_id'],
                'satuan' => empty($input_post['satuan']) ? NULL : $input_post['satuan'],
                'njopr' => pad_to_decimal($input_post['njopr']),
                'nspr' => pad_to_decimal($input_post['nspr']),
                'nsr' => pad_to_decimal($input_post['nsr']),
                'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->reklame_nsr_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('reklame_nsr'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vreklame_nsr_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('reklame_nsr'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("reklame_nsr/update/{$p_id}");
        
        if ($p_id && $get = $this->reklame_nsr_model->get($p_id)) {
            $data['dt']['id'] = $get->id;
            $data['dt']['satuan'] = empty($get->satuan) ? NULL : $get->satuan;
            $data['dt']['njopr'] = empty($get->njopr) ? NULL : $get->njopr;
            $data['dt']['nspr'] = empty($get->nspr) ? NULL : $get->nspr;
			$data['dt']['nsr'] = empty($get->nsr) ? NULL : $get->nsr;
            $data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
            $data['dt']['enabled'] = $get->enabled == 1 ? 'checked' : '';

            $select_data = $this->load->model('jalan_kelas_model')->get_select2();
            $options     = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }
            $js                        = 'id="kelas_jalan_id" class="form-control" required ';
            $data['select_jalan_kelas'] = form_dropdown('kelas_jalan_id', $options, $get->kelas_jalan_id, $js);

            $select_data = $this->load->model('reklame_media_model')->get_select();
            $options     = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->media;
            }
            $js                        = 'id="media_id" class="form-control" required ';
            $data['select_media'] = form_dropdown('media_id', $options, $get->media_id, $js);

			$this->load->view('vreklame_nsr_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('reklame_nsr'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("reklame_nsr/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
                'kelas_jalan_id' => empty($input_post['kelas_jalan_id']) ? NULL : $input_post['kelas_jalan_id'],
                'media_id' => empty($input_post['media_id']) ? NULL : $input_post['media_id'],
                'satuan' => empty($input_post['satuan']) ? NULL : $input_post['satuan'],
                'njopr' => pad_to_decimal($input_post['njopr']),
                'nspr' => pad_to_decimal($input_post['nspr']),
                'nsr' => pad_to_decimal($input_post['nsr']),
                'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
                'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            $this->reklame_nsr_model->update($p_id, $update_data);
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('reklame_nsr'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vreklame_nsr_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('reklame_nsr'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->reklame_nsr_model->get($id)) {
            $this->reklame_nsr_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('reklame_nsr'));
        } else {
            show_404();
        }
    }
}