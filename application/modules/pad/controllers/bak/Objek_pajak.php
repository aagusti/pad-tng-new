<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class objek_pajak extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }
        
        $module = 'pendaftaran';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model', 'subjek_pajak_model', 'objek_pajak_model'
        ));
		
		$this->load->helper(active_module());
    }
    
    public function index() {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $select_data = $this->load->model('usaha_model')->get_select();
        $options     = array();
        if($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                   = 'id="usaha_id" class="input-medium"';
		$options[999] = 'SEMUA PAJAK';
		$select = form_dropdown('usaha_id', $options, 999, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);;
        $data['select_usaha'] = $select;
        
		$options = array(
			999 => 'SEMUA STATUS',
			'1' => 'AKTIF',
			'2' => 'TUTUP',
			'3' => 'TUTUP SEMENTARA',
		);
		$js = 'id="customer_status_id" class="input-medium" ';
		$select = form_dropdown('customer_status_id', $options, 999, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);;
		$data['select_status'] = $select;
        
        $data['current'] = 'pendaftaran';
        $data['controller'] = $this->uri->segment(2);
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vobjek_pajak', $data);
    }
    
    function grid() {
        $usaha_id = $this->uri->segment(4);
        $status_id = $this->uri->segment(5);
        
        $this->load->library('Datatables');
        $this->datatables->select("cu.id, get_nopd(cu.id, true) as nopd, u.nama, get_npwpd(c.id, true) as npwpd, c.nama as customernm, 
            kec.nama as kecamatannm, kel.nama as kelurahannm,
            case when cu.customer_status_id=1 then 'AKTIF' when cu.customer_status_id=2 then 'TUTUP' else 'TUTUP SEMENTARA' end as ket, c.id as cid", false);
        $this->datatables->from('pad_customer_usaha cu');
        $this->datatables->join('pad_customer c', 'c.id=cu.customer_id');
        $this->datatables->join('pad_usaha u', 'u.id=cu.usaha_id');
        $this->datatables->join('pad_kecamatan kec', 'kec.id=cu.kecamatan_id');
        $this->datatables->join('pad_kelurahan kel', 'kel.id=cu.kelurahan_id and kel.kecamatan_id=cu.kecamatan_id', false);
        $this->datatables->where('c.rp', 'P');
        
		if($usaha_id <> 999 && !empty($usaha_id))
			$this->datatables->filter('cu.usaha_id', $usaha_id);
		if($status_id <> 999 && !empty($status_id))
			$this->datatables->filter('cu.customer_status_id', $status_id);
            
        $sort = $this->input->get('sSortDir_0');
        if ($this->input->get('iSortCol_0') == 1) {
            $this->datatables->order_by('nopd', $sort);
        } else
            $this->datatables->order_by('nopd', $sort);
        
        echo $this->datatables->generate();
    }
	
    function grid_for_nopd() {
        $this->load->library('Datatables');
        $this->datatables->select('c.id, get_npwpd(c.id, true) as npwpd, c.nama as customernm, 
			(case when c.wpnama=\'\' then c.pnama else c.wpnama end) as nama, c.alamat', false);
        $this->datatables->from('pad_customer c');
        //$this->datatables->join('pad_customer_usaha cu', 'c.id=cu.customer_id');
        $this->datatables->where('c.rp', 'P');
        $this->datatables->where('c.id in (select customer_id from pad_customer_usaha)');
        //$this->datatables->where('c.enabled', 1);
		
        echo $this->datatables->generate();
    }
	
	// grid kartu data hotel
    function grid_kd() 
    {
        $cu_id    = $this->uri->segment(4);
        $this->session->set_userdata('cu_id', $cu_id );
        $konterid    = $this->uri->segment(5);
        $this->session->set_userdata('konter', $konterid );
        $this->load->library('Datatables');
        $this->datatables->select('notes, tarif, kamar, volume', false);
        $this->datatables->from('pad_customer_detail');			
        $this->datatables->where('customer_id', $cu_id);
        $this->datatables->where('konterid', $konterid);
        $this->datatables->add_column('batal', '<a class="delete" href="">Hapus</a>');
        
        echo $this->datatables->generate();
    }
    //admin
    private function fvalidation() {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        
		// $this->form_validation->set_rules('id','id','required|numeric');
		// $this->form_validation->set_rules('konterid','konterid','required|numeric');
		$this->form_validation->set_rules('customer_id','customer_id','required|numeric');
		$this->form_validation->set_rules('usaha_id','usaha_id','required|numeric');
		// $this->form_validation->set_rules('so','so','required|trim');
		$this->form_validation->set_rules('kecamatan_id','kecamatan_id','required|numeric');
		$this->form_validation->set_rules('kelurahan_id','kelurahan_id','required|numeric');
		// $this->form_validation->set_rules('notes','notes','required|trim');
		// $this->form_validation->set_rules('enabled','enabled','required|numeric');
		// $this->form_validation->set_rules('reg_date','reg_date','required');
		// $this->form_validation->set_rules('tmt','tmt','required');
		
		// $this->form_validation->set_rules('created','created','required');
		// $this->form_validation->set_rules('create_uid','create_uid','required|numeric');
		// $this->form_validation->set_rules('updated','updated','required');
		// $this->form_validation->set_rules('update_uid','update_uid','required|numeric');
		
		// $this->form_validation->set_rules('reg_kelurahan_id','reg_kelurahan_id','required|numeric');
		// $this->form_validation->set_rules('customer_status_id','customer_status_id','required|numeric');
		// $this->form_validation->set_rules('aktifnotes','aktifnotes','required|trim');
		// $this->form_validation->set_rules('air_zona_id','air_zona_id','required|numeric');
		// $this->form_validation->set_rules('air_manfaat_id','air_manfaat_id','required|numeric');
		// $this->form_validation->set_rules('newkelid','newkelid','required|numeric');
		$this->form_validation->set_rules('def_pajak_id','def_pajak_id','numeric');
    }
    
    private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['konterid'] = $this->input->post('konterid');
		$data['customer_id'] = $this->input->post('customer_id');
		$data['usaha_id'] = $this->input->post('usaha_id');
		$data['so'] = $this->input->post('so');
		$data['kecamatan_id'] = $this->input->post('kecamatan_id');
		$data['kelurahan_id'] = $this->input->post('kelurahan_id');
		$data['notes'] = $this->input->post('notes');
		$data['tmt'] = $this->input->post('tmt');
		$data['reg_date'] = $this->input->post('reg_date');
		$data['enabled'] = $this->input->post('enabled');
        
		$data['def_pajak_id'] = $this->input->post('def_pajak_id');
		$data['opnm'] = $this->input->post('opnm');
		$data['opalamat'] = $this->input->post('opalamat');
		$data['latitude'] = pad_to_decimal($this->input->post('latitude'));
		$data['longitude'] = pad_to_decimal($this->input->post('longitude'));
		
		$data['created'] = $this->input->post('created');
		$data['create_uid'] = $this->input->post('create_uid');
		$data['updated'] = $this->input->post('updated');
		$data['update_uid'] = $this->input->post('update_uid');
		
		$data['reg_kelurahan_id'] = $this->input->post('reg_kelurahan_id');
		$data['customer_status_id'] = $this->input->post('customer_status_id');
		$data['aktifnotes'] = $this->input->post('aktifnotes');
		$data['air_zona_id'] = $this->input->post('air_zona_id');
		$data['air_manfaat_id'] = $this->input->post('air_manfaat_id');
		$data['newkelid'] = $this->input->post('newkelid');

	          // data tambahan           
        $data['kd_restojmlmeja'] = pad_to_decimal($this->input->post('kd_restojmlmeja'));
        $data['kd_restojmlkursi'] = pad_to_decimal($this->input->post('kd_restojmlkursi'));
        $data['kd_restojmltamu'] = pad_to_decimal($this->input->post('kd_restojmltamu'));
        $data['kd_filmkursi'] = pad_to_decimal($this->input->post('kd_filmkursi'));
        $data['kd_filmpertunjukan'] = pad_to_decimal($this->input->post('kd_filmpertunjukan'));
        $data['kd_filmtarif'] = pad_to_decimal($this->input->post('kd_filmtarif'));
        $data['kd_bilyarmeja'] = pad_to_decimal($this->input->post('kd_bilyarmeja'));
        $data['kd_bilyartarif'] = pad_to_decimal($this->input->post('kd_bilyartarif'));
        $data['kd_bilyarkegiatan'] = pad_to_decimal($this->input->post('kd_bilyarkegiatan'));
        $data['kd_diskopengunjung'] = pad_to_decimal($this->input->post('kd_diskopengunjung'));
        $data['kd_diskotarif'] = pad_to_decimal($this->input->post('kd_diskotarif'));
        $data['mall_id'] = pad_to_decimal($this->input->post('op_mall_id'));

        
        return $data;
    }
    
    public function add() {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('objek_pajak'));
        }
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('objek_pajak/add');
        $data['dt']      = $this->fpost();
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $this->fpost();
			
			$konter = $this->objek_pajak_model->max_konter($this->input->get_post('customer_id'));
			
			$usaha_model = $this->load->model('usaha_model');
			$so = $usaha_model->get_so($this->input->get_post('op_usaha_id'));
			
            $post_data  = array(
				'konterid' => $konter,
				'customer_id' => empty($input_post['customer_id']) ? NULL : $input_post['customer_id'],
				'usaha_id' => empty($input_post['usaha_id']) ? NULL : $input_post['usaha_id'],
				'so' => $so,
				'kecamatan_id' => empty($input_post['kecamatan_id']) ? NULL : $input_post['kecamatan_id'],
				'kelurahan_id' => empty($input_post['kelurahan_id']) ? NULL : $input_post['kelurahan_id'],
				'notes' => empty($input_post['notes']) ? NULL : $input_post['notes'],
				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'customer_status_id' => empty($input_post['customer_status_id']) ? NULL : $input_post['customer_status_id'],
				'enabled' => 1,
                
				'latitude' => $input_post['latitude'],
				'longitude' => $input_post['longitude'],
				'opnm' => $input_post['opnm'],
				'opalamat' => $input_post['opalamat'],
				'def_pajak_id' => $input_post['def_pajak_id'],

				          // data tambahan           
	            'kd_restojmlmeja' => pad_to_decimal($input_post['kd_restojmlmeja']),
	            'kd_restojmlkursi' => pad_to_decimal($input_post['kd_restojmlkursi']),
	            'kd_restojmltamu' => pad_to_decimal($input_post['kd_restojmltamu']),
	            'kd_filmkursi' => pad_to_decimal($input_post['kd_filmkursi']),
	            'kd_filmpertunjukan' => pad_to_decimal($input_post['kd_filmpertunjukan']),
	            'kd_filmtarif' => pad_to_decimal($input_post['kd_filmtarif']),
	            'kd_bilyarmeja' => pad_to_decimal($input_post['kd_bilyarmeja']),
	            'kd_bilyartarif' => pad_to_decimal($input_post['kd_bilyartarif']),
	            'kd_bilyarkegiatan' => pad_to_decimal($input_post['kd_bilyarkegiatan']),
	            'kd_diskopengunjung' => pad_to_decimal($input_post['kd_diskopengunjung']),
	            'kd_diskotarif' => pad_to_decimal($input_post['kd_diskotarif']),
	            'mall_id' => pad_to_decimal($input_post['op_mall_id']),
				
				'reg_date' => date('Y-m-d'),
                'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
            );
			$this->objek_pajak_model->save($post_data);


			                    // data tambahan / detail
            $dtKD = $this->input->post('dtKD');
            $tambahan_data2 = array();
            
            if(isset($dtKD)) {
                $i = 1;
                $dtKD = json_decode($dtKD, true);
                
                if(count($dtKD['dtKD']) > 0){
                    $cid = $input_post['customer_id'];
                    $op_u_id = $input_post['usaha_id'];
                    $rd_row = array();
                    foreach($dtKD['dtKD'] as $rows) {
                        $rd_row = array (
                            'customer_id' =>$cid,
                            'konterid' => $konter,
                            'usaha_id' => $op_u_id,
                            'nourut' => $i,
                            'notes'  => $rows[0],
                            'tarif'  => pad_to_decimal($rows[1]),
                            'kamar'  => pad_to_decimal($rows[2]),
                            'volume' => pad_to_decimal($rows[3]),
                        );
                        $i++;
                        $tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
                    }
                    
                    //langsung ajah dah - sementara
                    $query= "delete from pad_customer_detail where (customer_id=$cid and konterid=$konter)"; 
                    $this->db->query($query);
                    $this->db->insert_batch('pad_customer_detail', $tambahan_data2);
                }
            }

			$this->session->set_flashdata('msg_success', 'Data telah disimpan.');
			redirect(active_module_url('objek_pajak'));            
        }
		
		$data['dt']['nopd'] = '';
		$data['dt']['tmt'] = date('d-m-Y');
						
		$select_data  = $this->load->model('usaha_model')->get_select();
		$options      = array();
        $usaha_id = '';
		foreach ($select_data as $row) {
            $usaha_id = ($usaha_id == '') ? $row->id : $usaha_id;
			$options[$row->id] = $row->nama;
		}
		$js                       = 'id="usaha_id" class="input-medium" onChange="get_pajak(this.value);" required ';
		$data['select_usaha'] = form_dropdown('usaha_id', $options, '', $js);
        
		$select_data  = $this->load->model('pajak_model')->get_select2($usaha_id);
		$options      = array();
        $pajak_id = '';
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                       = 'id="def_pajak_id" class="input" required ';
		$data['select_pajak'] = form_dropdown('def_pajak_id', $options, '', $js);
				
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
		if($select_data)
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                       = 'id="kelurahan_id" class="input-large" required ';
		$data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, '', $js);

		$select_data = $this->load->model('reklame_mall_model')->get_all();
        $options     = array();
        if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }}
        $js                       = 'id="op_mall_id" class="input-large" required ';
        $data['select_op_mall'] = form_dropdown('op_mall_id', $options, '', $js);

		//read again ? tujuan nanti sih kayanya masuk ke op_status_usaha
		$options = array(
			'1' => 'AKTIF',
			'2' => 'TUTUP',
			'3' => 'TUTUP SEMENTARA',
			'4' => 'NORMAL',
			'5' => 'LIBUR',
		);
		$js = 'id="customer_status_id" class="input-medium" required ';
		$data['select_status'] = form_dropdown('customer_status_id', $options, '', $js);

        $this->load->view('vobjek_pajak_form', $data);
    }
    
    public function edit() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('objek_pajak'));
        }
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('objek_pajak/update');
        
        $id = $this->uri->segment(4);
        if ($id && $get = $this->objek_pajak_model->get($id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['customer_id'] = empty($get->customer_id) ? NULL : $get->customer_id;
			$data['dt']['usaha_id'] = empty($get->usaha_id) ? NULL : $get->usaha_id;
			$data['dt']['so'] = empty($get->so) ? NULL : $get->so;
			$data['dt']['konterid'] = empty($get->konterid) ? NULL : $get->konterid;
			$data['dt']['kecamatan_id'] = empty($get->kecamatan_id) ? NULL : $get->kecamatan_id;
			$data['dt']['kelurahan_id'] = empty($get->kelurahan_id) ? NULL : $get->kelurahan_id;
			$data['dt']['notes'] = empty($get->notes) ? NULL : $get->notes;
			$data['dt']['tmt'] = empty($get->tmt) ? NULL : date('d-m-Y', strtotime($get->tmt));
			$data['dt']['customer_status_id'] = empty($get->customer_status_id) ? NULL : $get->customer_status_id;
			$data['dt']['enabled'] = empty($get->enabled) ? NULL : $get->enabled;
            
			$data['dt']['def_pajak_id'] = empty($get->def_pajak_id) ? NULL : $get->def_pajak_id;
			$data['dt']['opnm'] = empty($get->opnm) ? NULL : $get->opnm;
			$data['dt']['opalamat'] = empty($get->opalamat) ? NULL : $get->opalamat;
			$data['dt']['latitude'] = $get->latitude;
			$data['dt']['longitude'] = $get->longitude;
			
			$data['dt']['reg_date'] = empty($get->reg_date) ? NULL : date('d-m-Y', strtotime($get->reg_date));
			$data['dt']['created'] = empty($get->created) ? NULL : date('d-m-Y', strtotime($get->created));
			$data['dt']['create_uid'] = empty($get->create_uid) ? NULL : $get->create_uid;
			$data['dt']['updated'] = empty($get->updated) ? NULL : date('d-m-Y', strtotime($get->updated));
			$data['dt']['update_uid'] = empty($get->update_uid) ? NULL : $get->update_uid;
			
			$data['dt']['aktifnotes'] = empty($get->aktifnotes) ? NULL : $get->aktifnotes;
			$data['dt']['reg_kelurahan_id'] = empty($get->reg_kelurahan_id) ? NULL : $get->reg_kelurahan_id;
			$data['dt']['air_zona_id'] = empty($get->air_zona_id) ? NULL : $get->air_zona_id;
			$data['dt']['air_manfaat_id'] = empty($get->air_manfaat_id) ? NULL : $get->air_manfaat_id;
			$data['dt']['newkelid'] = empty($get->newkelid) ? NULL : $get->newkelid;
			$data['dt']['def_pajak_id'] = empty($get->def_pajak_id) ? NULL : $get->def_pajak_id;

			          // data tambahan           
            $data['dt']['kd_restojmlmeja'] = $get->kd_restojmlmeja;
            $data['dt']['kd_restojmlkursi'] = $get->kd_restojmlkursi;
            $data['dt']['kd_restojmltamu'] = $get->kd_restojmltamu;
            $data['dt']['kd_filmkursi'] = $get->kd_filmkursi;
            $data['dt']['kd_filmpertunjukan'] = $get->kd_filmpertunjukan;
            $data['dt']['kd_filmtarif'] = $get->kd_filmtarif;
            $data['dt']['kd_bilyarmeja'] = $get->kd_bilyarmeja;
            $data['dt']['kd_bilyartarif'] = $get->kd_bilyartarif;
            $data['dt']['kd_bilyarkegiatan'] = $get->kd_bilyarkegiatan;
            $data['dt']['kd_diskopengunjung'] = $get->kd_diskopengunjung;
            $data['dt']['kd_diskotarif'] = $get->kd_diskotarif;
            $data['dt']['mall_id'] = $get->mall_id;

			$data['dt']['nopd'] = $this->objek_pajak_model->get_nopd($get->id);
		
			$select_data  = $this->load->model('usaha_model')->get_select();
			$options      = array();
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}
			$js                       = 'id="usaha_id" class="input-medium" onChange="get_pajak(this.value);" required ';
			$data['select_usaha'] = form_dropdown('usaha_id', $options, $get->usaha_id, $js);
            
            $select_data  = $this->load->model('pajak_model')->get_select2($get->usaha_id);
            $options      = array();
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }
            $js                       = 'id="def_pajak_id" class="input" required ';
            $data['select_pajak'] = form_dropdown('def_pajak_id', $options, '', $js);
	
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

			$select_data = $this->load->model('reklame_mall_model')->get_all();
	        $options     = array();
	        if($select_data) {
	        foreach ($select_data as $row) {
	            $options[$row->id] = $row->nama;
	        }}
	        $js                       = 'id="op_mall_id" class="input-large" required ';
	        $data['select_op_mall'] = form_dropdown('op_mall_id', $options, $get->id, $js);



			//read again ? tujuan nanti sih kayanya masuk ke op_status_usaha
			$options = array(
				'1' => 'AKTIF',
				'2' => 'TUTUP',
				'3' => 'TUTUP SEMENTARA',
				'4' => 'NORMAL',
				'5' => 'LIBUR',

			);
			$js = 'id="customer_status_id" class="input-medium" required ';
			$data['select_status'] = form_dropdown('customer_status_id', $options, $get->customer_status_id, $js);
			
            $this->load->view('vobjek_pajak_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('objek_pajak'));
        }
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('objek_pajak/update');
        
        $post_data  = $this->fpost();
        $data['dt'] = $post_data;
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
			
			$usaha_model = $this->load->model('usaha_model');
			$so = $usaha_model->get_so($this->input->get_post('op_usaha_id'));
			
            $post_data  = array(
				'customer_id' => empty($input_post['customer_id']) ? NULL : $input_post['customer_id'],
				'usaha_id' => empty($input_post['usaha_id']) ? NULL : $input_post['usaha_id'],
				'so' => $so,
				'kecamatan_id' => empty($input_post['kecamatan_id']) ? NULL : $input_post['kecamatan_id'],
				'kelurahan_id' => empty($input_post['kelurahan_id']) ? NULL : $input_post['kelurahan_id'],
				'notes' => empty($input_post['notes']) ? NULL : $input_post['notes'],
				'aktifnotes' => empty($input_post['aktifnotes']) ? NULL : $input_post['aktifnotes'],

				'tmt' => empty($input_post['tmt']) ? NULL : date('Y-m-d', strtotime($input_post['tmt'])),
				'customer_status_id' => empty($input_post['customer_status_id']) ? NULL : $input_post['customer_status_id'],
				
				'latitude' => $input_post['latitude'],
				'longitude' => $input_post['longitude'],
				'opnm' => $input_post['opnm'],
				'opalamat' => $input_post['opalamat'],
				'def_pajak_id' => $input_post['def_pajak_id'],

                
                          // data tambahan           
	            'kd_restojmlmeja' => pad_to_decimal($input_post['kd_restojmlmeja']),
	            'kd_restojmlkursi' => pad_to_decimal($input_post['kd_restojmlkursi']),
	            'kd_restojmltamu' => pad_to_decimal($input_post['kd_restojmltamu']),
	            'kd_filmkursi' => pad_to_decimal($input_post['kd_filmkursi']),
	            'kd_filmpertunjukan' => pad_to_decimal($input_post['kd_filmpertunjukan']),
	            'kd_filmtarif' => pad_to_decimal($input_post['kd_filmtarif']),
	            'kd_bilyarmeja' => pad_to_decimal($input_post['kd_bilyarmeja']),
	            'kd_bilyartarif' => pad_to_decimal($input_post['kd_bilyartarif']),
	            'kd_bilyarkegiatan' => pad_to_decimal($input_post['kd_bilyarkegiatan']),
	            'kd_diskopengunjung' => pad_to_decimal($input_post['kd_diskopengunjung']),
	            'kd_diskotarif' => pad_to_decimal($input_post['kd_diskotarif']),
	            'mall_id' => pad_to_decimal($input_post['op_mall_id']),

				'updated' => date('Y-m-d'),
				'update_uid' => sipkd_user_id(),
            );
            $this->objek_pajak_model->update($this->input->post('id'), $post_data);

             if(isset($dtKD)) {
                $i = 1;
                $dtKD = json_decode($dtKD, true);
                
                if(count($dtKD['dtKD']) > 0){
                    $cid = $input_post['customer_id'];
                    $op_u_id = $input_post['usaha_id'];
                    $rd_row = array();
                    foreach($dtKD['dtKD'] as $rows) {
                        $rd_row = array (
                            'customer_id' =>$cid,
                            'konterid' => $konter,
                            'usaha_id' => $op_u_id,
                            'nourut' => $i,
                            'notes'  => $rows[0],
                            'tarif'  => pad_to_decimal($rows[1]),
                            'kamar'  => pad_to_decimal($rows[2]),
                            'volume' => pad_to_decimal($rows[3]),
                        );
                        $i++;
                        $tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
                    }
                    
                    //langsung ajah dah - sementara
                    $query= "delete from pad_customer_detail where (customer_id=$cid and konterid=$konter)"; 
                    $this->db->query($query);
                    $this->db->insert_batch('pad_customer_detail', $tambahan_data2);
                }
            }


            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('objek_pajak'));
        }
        
        $get               = (object) $post_data;
		
		$data['dt']['nopd'] = $this->objek_pajak_model->get_nopd($get->id);
        
		$select_data  = $this->load->model('usaha_model')->get_select();
		$options      = array();
        $usaha_id = '';
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js                       = 'id="usaha_id" class="input-medium" onChange="get_pajak(this.value);" required ';
		$data['select_usaha'] = form_dropdown('usaha_id', $options, $get->usaha_id, $js);
				
        $select_data  = $this->load->model('pajak_model')->get_select2($get->usaha_id);
        $options      = array();
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }
        $js                       = 'id="def_pajak_id" class="input-medium" required ';
        $data['select_pajak'] = form_dropdown('def_pajak_id', $options, '', $js);
        
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

		//read again ? tujuan nanti sih kayanya masuk ke op_status_usaha
		$options = array(
			'1' => 'AKTIF',
			'2' => 'TUTUP',
			'3' => 'TUTUP SEMENTARA',
			'4' => 'NORMAL',
			'5' => 'LIBUR',

		);
		$js = 'id="customer_status_id" class="input-medium" required ';
		$data['select_status'] = form_dropdown('customer_status_id', $options, $get->customer_status_id, $js);
		
        $this->load->view('vobjek_pajak_form', $data);
    }
    
    public function delete() {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('objek_pajak'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->objek_pajak_model->get($id)) {
            $this->objek_pajak_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('objek_pajak'));
        } else {
            show_404();
        }
    }
	
    // ==
    
    function get_cu()
    {
        $c_id  = $this->uri->segment(4);
        $cu_id = $this->uri->segment(5);
        $u_id  = $this->uri->segment(6); // param untuk wp_login (opsional di menu default)
		
        $model = $this->load->model('pad_model');
		if (!wp_login())
			$rows = $model->sptpd_get_cu($c_id);
		else {
			$rows = $model->sptpd_get_cu($c_id, $u_id);
		}
		
        $usaha   = '';
        $cu_data = new stdClass();
        if ($rows) {
            $cu_data->customer_id       = $rows[0]->customer_id;
            $cu_data->customernm        = $rows[0]->customernm;
            $cu_data->npwpd             = $rows[0]->npwpd2;
            $cu_data->so                = $rows[0]->so;
            $cu_data->konterid          = $rows[0]->konterid;
            $cu_data->air_zona_id       = $rows[0]->air_zona_id;
            $cu_data->air_manfaat_id    = $rows[0]->air_manfaat_id;

			$selected = "selected";
            foreach ($rows as $row) {
				if ($cu_id == $row->customer_usaha_id) {
					$usaha .= "<option value={$row->customer_usaha_id} selected>{$row->usahanm}</option>";
				} else {
					$usaha .= "<option value={$row->customer_usaha_id}>{$row->usahanm}</option>";
				}
            }
            $cu_data->usaha = $usaha;

            echo json_encode($cu_data);
        }
    }

    function get_c_id()
    {
        $c_id  = $this->uri->segment(4);
		
        $model = $this->load->model('pad_model');
		$rows = $model->sptpd_get_c_id($c_id);
		
        $c_data = new stdClass();
        if ($rows) {
            $c_data->customer_id       = $rows[0]->customer_id;
            $c_data->customernm        = $rows[0]->customernm;
            $c_data->npwpd             = $rows[0]->npwpd2;

            echo json_encode($c_data);
        }
    }


    function get_typeahead_npwpd() {
        $xnpwpd = $this->uri->segment(4);
        $data = $this->load->model('subjek_pajak_model')->get_typeahead_npwpd($xnpwpd, false);
        echo json_encode($data);
    }
	
    function get_typeahead_csname() {
        $xname = urldecode($this->uri->segment(4));
        $data = $this->load->model('subjek_pajak_model')->get_typeahead_csname($xname);
        echo json_encode($data);
    }
	
    function get_typeahead_nopd() {
        $usaha_id = $this->uri->segment(4) != 0 ? $this->uri->segment(4) : FALSE;
        $xnopd = $this->uri->segment(5);
        $data = $this->load->model('objek_pajak_model')->get_typeahead_nopd($xnopd, $usaha_id, false);
        echo json_encode($data);
    }
}
