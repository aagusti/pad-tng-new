<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class reklame_media extends CI_Controller
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
            'apps_model', 'reklame_media_model'
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
		
        $this->load->view('vreklame_media', $data);
    }
    
    function grid()
    {		
        $this->load->library('Datatables');
        $this->datatables->select("m.id, p.nama, media, singkatan, 
            (case when masa = '0' then 'Tahun' when masa = '1' then 'Bulan'  when masa = '2' then 'Minggu' END) as masa, 
            (case when keterangan = '0' then 'Permanen' when masa = '1' then 'Non-Permanen'  END) as keterangan, 
             m.enabled");
        $this->datatables->from('pad_reklame_media m');
        $this->datatables->join('pad_jenis_pajak p', 'm.jenis_pajak_id = p.id');
        $this->datatables->checkbox_column('6');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_reklame_media = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('jenis_pajak_id','Jenis Pajak','required|trim');
		$this->form_validation->set_rules('media','Media','required');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
        $data['jenis_pajak_id'] = $this->input->post('jenis_pajak_id');
        $data['jenis_pajak'] = $this->input->post('jenis_pajak');
		$data['media'] = $this->input->post('media');
        $data['singkatan'] = $this->input->post('singkatan');
        $data['masa'] = $this->input->post('masa');
        $data['keterangan'] = $this->input->post('keterangan');
        $data['enabled'] = $this->input->post('enabled');
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('reklame_media'));
        }
        
        $post_data  = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('reklame_media/add');

       $options = array(
            0 => 'Tahun',
            1 => 'Bulan',
            2 => 'Minggu',
        );
        $js = 'id="masa" class="form-control" ';
        $data['select_masa'] = form_dropdown('masa', $options, '', $js);

        $options = array(
            0 => 'Permanen',
            1 => 'Non-Permanen',
        );
        $js = 'id="keterangan" class="form-control" ';
        $data['select_keterangan'] = form_dropdown('keterangan', $options, '', $js);
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $update_data = array(
                'jenis_pajak_id' => empty($input_post['jenis_pajak_id']) ? NULL : $input_post['jenis_pajak_id'],
				'media' => empty($input_post['media']) ? NULL : $input_post['media'],
                'singkatan' => empty($input_post['singkatan']) ? NULL : $input_post['singkatan'],
				'masa' => $input_post['masa'],
                'keterangan' => $input_post['keterangan'], 
                'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->reklame_media_model->save($update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('reklame_media'));
        }
        
        $data['dt']      = $post_data;
        $this->load->view('vreklame_media_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('reklame_media'));
        }
        
        $p_id = $this->uri->segment(4);
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("reklame_media/update/{$p_id}");
        
        if ($p_id && $get = $this->reklame_media_model->get($p_id)) {
            $p_id = $get->jenis_pajak_id;
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
            $data['dt']['jenis_pajak_id'] = empty($get->jenis_pajak_id) ? NULL : $get->jenis_pajak_id;
            $data['dt']['media'] = empty($get->media) ? NULL : $get->media;
            $data['dt']['singkatan'] = empty($get->singkatan) ? NULL : $get->singkatan;
            $data['dt']['keterangan'] = empty($get->keterangan) ? NULL : $get->keterangan;
            $data['dt']['enabled'] = $get->enabled==1 ? 'checked' : '';

            $options = array(
                0 => 'Tahun',
                1 => 'Bulan',
                2 => 'Minggu',
            );
            $js = 'id="masa" class="form-control"';
            $data['select_masa'] = form_dropdown('masa', $options, $get->masa, $js);

            $options = array(
                0 => 'Permanen',
                1 => 'Non-Permanen',
            );
            $js = 'id="keterangan" class="form-control" ';
            $data['select_keterangan'] = form_dropdown('keterangan', $options, $get->keterangan, $js);

            $query = $this->db->query("select nama from pad_jenis_pajak where id=$p_id");
                foreach ($query->result() as $row)
                {
                   $data['dt']['jenis_pajak'] =  $row->nama;
                }

			
			$this->load->view('vreklame_media_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('reklame_media'));
        }
        
        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();
        
        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("reklame_media/update/{$p_id}");
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
                'jenis_pajak_id' => empty($input_post['jenis_pajak_id']) ? NULL : $input_post['jenis_pajak_id'],
                'media' => empty($input_post['media']) ? NULL : $input_post['media'],
                'singkatan' => empty($input_post['singkatan']) ? NULL : $input_post['singkatan'],
                'masa' => $input_post['masa'],
                'keterangan' => $input_post['keterangan'], 
                'enabled' => empty($input_post['enabled']) ? 0 : 1,
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
            );
            
            $this->reklame_media_model->update($p_id, $update_data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('reklame_media'));
        }
        
        $data['dt'] = $post_data;		
		$this->load->view('vreklame_media_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('reklame_media'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->reklame_media_model->get($id)) {
            $this->reklame_media_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('reklame_media'));
        } else {
            show_404();
        }
    }
}