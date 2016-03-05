<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
//direname jadi Reklames baru bisa muncul grid -_-
class reklames extends CI_Controller
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
            'apps_model', 'reklame_model'
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
        
        $this->load->view('vreklame', $data);
    }
    
    function grid()
    {       
        $this->load->library('Datatables');
        $this->datatables->select('r.id, r.kode, kec.nama, kel.nama as kelurahannm, r.latitude,r.longitude, 
                                   r.pemilik_nama, r.pemilik_alamat');
        $this->datatables->from('pad_reklame r');
        $this->datatables->join('pad_kecamatan kec', 'kec.id=r.kecamatan_id');
        $this->datatables->join('pad_kelurahan kel', 'kel.id=r.kelurahan_id and kel.kecamatan_id=r.kecamatan_id', false);
        $sort = $this->input->get('sSortDir_0');
        if ($this->input->get('iSortCol_0') == 1)
            $this->datatables->order_by('kode', $sort);
        echo $this->datatables->generate();
    }


    private function fvalidation() {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        
        // $this->form_validation->set_rules('id','id','required|numeric');
        $this->form_validation->set_rules('kode','Kode','required');
        $this->form_validation->set_rules('kecamatan_id','kecamatan_id','required|numeric');
        $this->form_validation->set_rules('kelurahan_id','Kelurahan','required|numeric');
        $this->form_validation->set_rules('pemilik_nama','Pemilik','required');
        $this->form_validation->set_rules('pemilik_alamat','Alamat Pemilik','required');
    }
    
    private function fpost() {
        $data['id'] = $this->input->post('id');
        $data['kode'] = $this->input->post('kode');
        $data['kecamatan_id'] = $this->input->post('kecamatan_id');
        $data['kelurahan_id'] = $this->input->post('kelurahan_id');
        $data['latitude'] = pad_to_decimal($this->input->post('latitude'));
        $data['longitude'] = pad_to_decimal($this->input->post('longitude'));
        
        $data['pemilik_nama'] = $this->input->post('pemilik_nama');
        $data['pemilik_alamat'] = $this->input->post('pemilik_alamat');
        $data['pemilik_kecamatan'] = $this->input->post('pemilik_kecamatan');
        $data['pemilik_kelurahan'] = $this->input->post('pemilik_kelurahan');
        $data['pemilik_kota'] = $this->input->post('pemilik_kota');
        
        return $data;
    }
    
    function get_kelurahan() {
        $kec_id    = $this->uri->segment(4);
        $kelurahan = $this->load->model('kelurahan_model')->get_select($kec_id);
        echo json_encode($kelurahan);
    }
    
    public function add() {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('reklames'));
        }
        $data['current'] = 'referensi';
        $data['controller'] = $this->uri->segment(2);
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('reklames/add');
        $data['dt']      = $this->fpost();
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $this->fpost();
            
            $post_data  = array(
                'kode' => $input_post['kode'],
                'kecamatan_id' => $input_post['kecamatan_id'],
                'kelurahan_id' => $input_post['kelurahan_id'],
                'latitude' => $input_post['latitude'],
                'longitude' => $input_post['longitude'],
                
                'pemilik_nama' => empty($input_post['pemilik_nama']) ? NULL : $input_post['pemilik_nama'],
                'pemilik_alamat' => empty($input_post['pemilik_alamat']) ? NULL : $input_post['pemilik_alamat'],
                'pemilik_kecamatan' => empty($input_post['pemilik_kecamatan']) ? NULL : $input_post['pemilik_kecamatan'],
                'pemilik_kelurahan' => empty($input_post['pemilik_kelurahan']) ? NULL : $input_post['pemilik_kelurahan'],
                'pemilik_kota' => empty($input_post['pemilik_kota']) ? NULL : $input_post['pemilik_kota'],
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->reklame_model->save($post_data);
            $this->session->set_flashdata('msg_success', 'Data telah disimpan.');
            redirect(active_module_url('reklames'));            
        }
        
        $select_data  = $this->load->model('kecamatan_model')->get_select();
        $options      = array();
        $kec_id = '';
        foreach ($select_data as $row) {
            if($kec_id == '') $kec_id = $row->id;
            $options[$row->id] = $row->nama;
        }
        $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" required ';
        $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, '', $js);

        $select_data = $this->load->model('kelurahan_model')->get_select($kec_id);
        $options     = array();
        if ($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                       = 'id="kelurahan_id" class="input-large" required ';
        $data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, '', $js);

        $this->load->view('vreklame_form', $data);
    }
    
    public function edit() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('reklames'));
        }
        $data['current'] = 'referensi';
        $data['controller'] = $this->uri->segment(2);
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('reklames/update');
        
        $id = $this->uri->segment(4);
        if ($id && $get = $this->reklame_model->get($id)) {
            $data['dt']['id'] = empty($get->id) ? NULL : $get->id;
            $data['dt']['kode'] = empty($get->kode) ? NULL : $get->kode;
            $data['dt']['kecamatan_id'] = empty($get->kecamatan_id) ? NULL : $get->kecamatan_id;
            $data['dt']['kelurahan_id'] = empty($get->kelurahan_id) ? NULL : $get->kelurahan_id;
            $data['dt']['latitude'] = $get->latitude;
            $data['dt']['longitude'] = $get->longitude;
            
            $data['dt']['pemilik_nama'] = empty($get->pemilik_nama) ? NULL : $get->pemilik_nama;
            $data['dt']['pemilik_alamat'] = empty($get->pemilik_alamat) ? NULL : $get->pemilik_alamat;
            $data['dt']['pemilik_kecamatan'] = empty($get->pemilik_kecamatan) ? NULL : $get->pemilik_kecamatan;
            $data['dt']['pemilik_kelurahan'] = empty($get->pemilik_kelurahan) ? NULL : $get->pemilik_kelurahan;
            $data['dt']['pemilik_kota'] = empty($get->pemilik_kota) ? NULL : $get->pemilik_kota;
        
            $select_data  = $this->load->model('kecamatan_model')->get_select();
            $options      = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }
            $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" required ';
            $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, $get->kecamatan_id, $js);

            $select_data = $this->load->model('kelurahan_model')->get_select($get->kecamatan_id);
            $options     = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }
            $js                       = 'id="kelurahan_id" class="input-large" required ';
            $data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, $get->kelurahan_id, $js);
            
            $this->load->view('vreklame_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('reklames'));
        }
        $data['current'] = 'referensi';
        $data['controller'] = $this->uri->segment(2);
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('reklames/update');
        
        $post_data  = $this->fpost();
        $data['dt'] = $post_data;
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            
            $post_data  = array(
                'kode' => $input_post['kode'],
                'kecamatan_id' => $input_post['kecamatan_id'],
                'kelurahan_id' => $input_post['kelurahan_id'],
                'latitude' => $input_post['latitude'],
                'longitude' => $input_post['longitude'],
                
                'pemilik_nama' => empty($input_post['pemilik_nama']) ? NULL : $input_post['pemilik_nama'],
                'pemilik_alamat' => empty($input_post['pemilik_alamat']) ? NULL : $input_post['pemilik_alamat'],
                'pemilik_kecamatan' => empty($input_post['pemilik_kecamatan']) ? NULL : $input_post['pemilik_kecamatan'],
                'pemilik_kelurahan' => empty($input_post['pemilik_kelurahan']) ? NULL : $input_post['pemilik_kelurahan'],
                'pemilik_kota' => empty($input_post['pemilik_kota']) ? NULL : $input_post['pemilik_kota'],
                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
            );
            $this->reklame_model->update($this->input->post('id'), $post_data);       
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('reklames'));
        }
        
        $get               = (object) $post_data;
        
        $select_data  = $this->load->model('kecamatan_model')->get_select();
        $options      = array();
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" required ';
        $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, $get->kecamatan_id, $js);

        $select_data = $this->load->model('kelurahan_model')->get_select($get->kecamatan_id);
        $options     = array();
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                       = 'id="kelurahan_id" class="input-large" required ';
        $data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, $get->kelurahan_id, $js);
        
        $this->load->view('vreklame_form', $data);
    }
    
    public function delete() {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('reklames'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->reklame_model->get($id)) {
            $this->reklame_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('reklames'));
        } else {
            show_404();
        }
    }
}
