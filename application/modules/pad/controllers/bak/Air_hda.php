<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class air_hda extends CI_Controller
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
            'apps_model', 'air_hda_model'
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

        $this->load->view('vair_hda', $data);
    }
    
    function grid()
    {		
        $str ="case when";
        $this->load->library('Datatables');
        $this->datatables->select("h.id, z.nama as zonanm, 
            (case when golongan = 2 then 'AP' when golongan = 1 then 'ABT' END) as golongans, 
            k.nama, h.volume, h.hda");
        $this->datatables->from('pad_air_hda h');
        $this->datatables->join('pad_air_zona z', 'h.zona_id = z.id' );
        $this->datatables->join('pad_air_kelompok_usaha k', 'h.kelompok_usaha_id = k.id' );
        $this->datatables->rupiah_column('4,5');
        //$this->datatables->checkbox_column('5');
        echo $this->datatables->generate();
    }
    
    private function fvalidation($jenis_air_hda = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('volume','volume','required');
        $this->form_validation->set_rules('kelompok_usaha','Kelompok Usaha','required');
        $this->form_validation->set_rules('hda','HDA','required');
    }
    
    private function fpost()
    {
      $data['id'] = $this->input->post('id');
      $data['zona_id'] = $this->input->post('zona_id');
      $data['manfaat_id'] = $this->input->post('manfaat_id');
      $data['kelompok_usaha_id'] = $this->input->post('kelompok_usaha_id');
      $data['golongan'] = $this->input->post('golongan');
      $data['kelompok_usaha'] = $this->input->post('kelompok_usaha');
      $data['volume'] = $this->input->post('volume');
      $data['hda'] = $this->input->post('hda');

      return $data;
    }

  public function add()
  {
    if (!$this->module_auth->create) {
        $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
        redirect(active_module_url('air_hda'));
    }

    $post_data  = $this->fpost();

    $data['current'] = 'referensi';
    $data['apps']    = $this->apps_model->get_active_only();
    $data['faction'] = active_module_url('air_hda/add');

    $this->fvalidation();
    if ($this->form_validation->run() == TRUE) {
        $input_post = $post_data;
        $hda= pad_to_decimal($input_post['hda']);
        $volume= pad_to_decimal($input_post['volume']);

        $update_data = array(
                //'id' => empty($input_post['id']) ? NULL : $input_post['id'],
            'manfaat_id' => empty($input_post['manfaat_id']) ? NULL : $input_post['manfaat_id'],
            'golongan' => empty($input_post['golongan']) ? NULL : $input_post['golongan'],
            'zona_id' => empty($input_post['zona_id']) ? NULL : $input_post['zona_id'],
            'volume' => empty($volume) ? NULL : $volume,
            'kelompok_usaha_id' => empty($input_post['kelompok_usaha_id']) ? NULL : $input_post['kelompok_usaha_id'],
            'hda' => empty($hda) ? NULL : $hda,
            'created' => date('Y-m-d h:i:s'),
            'create_uid' => sipkd_user_id(),
				//'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				//'enabled' => empty($input_post['enabled']) ? 0 : 1,    
            );
        $this->air_hda_model->save($update_data);

        $this->session->set_flashdata('msg_success', 'Data telah disimpan');
        redirect(active_module_url('air_hda'));
    }

    $select_data = $this->load->model('air_zona_model')->get_select();
    $options     = array();
    if($select_data)
      foreach ($select_data as $row) {
         $options[$row->id] = $row->nama;
     }
     $js                        = 'id="zona_id" class="input-xlarge" required ';
     $data['select_air_zona'] = form_dropdown('zona_id', $options, '', $js);

     $select_data = $this->load->model('air_manfaat_model')->get_select();
     $options     = array();
     if($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                        = 'id="manfaat_id" class="input-xlarge" required ';
        $data['select_air_manfaat'] = form_dropdown('manfaat_id', $options, '', $js);

        $data['dt']      = $post_data;
        $this->load->view('vair_hda_form', $data);
    }


    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_hda'));
        }

        $p_id = $this->uri->segment(4);

        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_hda/update/{$p_id}");
        if ($p_id && $get = $this->air_hda_model->get($p_id)) {
            $data['dt']['id'] = empty($get->id) ? NULL : $get->id;
            $data['dt']['golongan'] = empty($get->golongan) ? NULL : $get->golongan;
            $data['dt']['manfaat_id'] = empty($get->manfaat_id) ? NULL : $get->manfaat_id;
            $data['dt']['kelompok_usaha_id'] = empty($get->kelompok_usaha_id) ? NULL : $get->kelompok_usaha_id;
            $data['dt']['zona_id'] = empty($get->zona_id) ? NULL : $get->zona_id;
            $data['dt']['volume'] = empty($get->volume) ? NULL : $get->volume;
            $data['dt']['hda'] = empty($get->hda) ? NULL : $get->hda;
            $ku_id = $get->kelompok_usaha_id;
            $query = $this->db->query("select nama from pad_air_kelompok_usaha where id=$ku_id");
            foreach ($query->result() as $row) {
                    $data['dt']['kelompok_usaha'] = $row->nama;
                }
            $select_data = $this->load->model('air_zona_model')->get_select();
            $options     = array();
            if($select_data){
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }
            }
            $js                        = 'id="zona_id" class="input-xlarge" required ';
            $data['select_air_zona'] = form_dropdown('zona_id', $options, $get->zona_id, $js);

            $select_data = $this->load->model('air_manfaat_model')->get_select();
            $options     = array();
            if($select_data){
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }
            }
            $js                        = 'id="manfaat_id" class="input-xlarge" required ';
            $data['select_air_manfaat'] = form_dropdown('manfaat_id', $options, $get->manfaat_id, $js);

            $this->load->view('vair_hda_form', $data);


        } else {
            show_404();
        }
    }

    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('air_hda'));
        }

        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();

        $data['current'] = 'referensi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("air_hda/update/{$p_id}");

        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $hda = pad_to_decimal($input_post['hda']);
            $volume = pad_to_decimal($input_post['volume']);

            $update_data = array(
                'id' => empty($input_post['id']) ? NULL : $input_post['id'],
                'golongan' => empty($input_post['golongan']) ? NULL : $input_post['golongan'],
                'manfaat_id' => empty($input_post['manfaat_id']) ? NULL : $input_post['manfaat_id'],
                'zona_id' => empty($input_post['zona_id']) ? NULL : $input_post['zona_id'],
                'kelompok_usaha_id' => empty($input_post['kelompok_usaha_id']) ? NULL : $input_post['kelompok_usaha_id'],
                'volume' => empty($volume) ? NULL : $volume,
                'hda' => empty($hda) ? NULL : $hda,
                'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
				//'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				//'enabled' => empty($input_post['enabled']) ? 0 : 1,
                );

            $this->air_hda_model->update($p_id, $update_data);

            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('air_hda'));
        }

        $get = $post_data;
        $select_data = $this->load->model('air_zona_model')->get_select2();
        $options     = array();
        foreach ($select_data as $row) {
         $options[$row->id] = $row->nama;
     }
     $js                        = 'id="id" class="input-xlarge" required ';
     $data['select_air_zona'] = form_dropdown('id', $options, $get->id, $js);

     $data['dt'] = $post_data;		
     $this->load->view('vair_hda_form', $data);
 }

 public function delete()
 {
    if (!$this->module_auth->delete) {
        $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
        redirect(active_module_url('air_hda'));
    }

    $id = $this->uri->segment(4);
    if ($id && $this->air_hda_model->get($id)) {
        $this->air_hda_model->delete($id);
        $this->session->set_flashdata('msg_success', 'Data telah dihapus');
        redirect(active_module_url('air_hda'));
    } else {
        show_404();
    }
}
}
