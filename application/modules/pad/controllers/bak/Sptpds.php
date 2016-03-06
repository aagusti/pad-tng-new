<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class sptpds extends CI_Controller {
	private $module = 'pendataan';
	private $controller = 'sptpd';

    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }

		if ($this->uri->segment(2) == 'skpd') {
			$this->module = 'penetapan';
			$this->controller = 'skpd';
		}

        $this->load->model(array(
            'apps_model', 'sptpd_model', 'skpd_model', 
            'reklame_nilai_strategis_model',
        ));

		$this->load->helper(active_module()); // cm kasih prefix namanya doang :D, ga pake _helper (pad_helper)
    }

	function load_auth() {
        $this->load->library('module_auth', array('module' => $this->module));
    }

    public function index()
    {
		$this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $data['current']    = $this->module;
        $data['controller'] = $this->controller;
        $data['apps']       = $this->apps_model->get_active_only();

		if (!wp_login()) {
			$select_data = $this->load->model('sptpd_type_model')->get_select();
			$options     = array();
			if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->typenm;
			}}
			$options[999] = 'SEMUA DOKUMEN';
			$js                        = 'id="type_id" class="input-small" onChange="void(0);"';
			$select = form_dropdown('type_id', $options, 999, $js);
			$select = preg_replace("/[\r\n]+/", "", $select);;
			$data['select_sptpd_type'] = $select;

			$select_data = $this->load->model('usaha_model')->get_select();
			$options     = array();
			if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}}
			$js                   = 'id="usaha_id" class="input-medium"';
			$options[999] = 'SEMUA PAJAK';
			$select = form_dropdown('usaha_id', $options, 999, $js);
			$select = preg_replace("/[\r\n]+/", "", $select);;
			$data['select_usaha'] = $select;

            $options = array(
                '1' => 'BELUM PROSES',
                '2' => 'SUDAH PROSES',
            );
            $js = 'id="proses_id" class="input-medium"';
            $select = form_dropdown('proses_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);
            $data['select_proses_id'] = $select;
            
			$this->load->view(active_module().'/vsptpd', $data);
		} else {
			$select_data = $this->load->model('sptpd_type_model')->get_select();
			$options     = array();
			if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->typenm;
			}}
			$options[999] = 'SEMUA DOKUMEN';
			$js                        = 'id="type_id" class="input-small" onChange="void(0);"';
			$select = form_dropdown('type_id', $options, 999, $js);
			$select = preg_replace("/[\r\n]+/", "", $select);;
			$data['select_sptpd_type'] = $select;

            $select_data = $this->load->model('usaha_model')->get_select();
			$options     = array();
			if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}}
			$js                   = 'id="usaha_id" class="input-medium"';
			$options[999] = 'SEMUA PAJAK';
			$select = form_dropdown('usaha_id', $options, 999, $js);
			$select = preg_replace("/[\r\n]+/", "", $select);;
			$data['select_usaha'] = $select;

			$this->load->view(active_module().'/wp/vsptpd', $data);
		}
    }

    function grid()
    {
        $type_id   = $this->uri->segment(4); // on sptpd
        $proses_id = $this->uri->segment(4); // on skpd
        $usaha_id  = $this->uri->segment(5);
        $terimatgl = $this->uri->segment(6);
        $terimatgl = empty($terimatgl) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl));
        
        $this->load->library('Datatables');

		if (!wp_login()) {
			if ($this->controller == 'sptpd') {
				$this->datatables->select('s.id, get_sptno(s.id) as sptno, s.terimatgl, st.typenm,
					get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as nama, 
                    p.nama, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.pajak_terhutang as pajak,
					cu.usaha_id, s.type_id', false);
				$this->datatables->from('pad_spt s');
				$this->datatables->join('pad_customer_usaha cu', 's.customer_usaha_id=cu.id', 'left');
				$this->datatables->join('pad_customer c', 'cu.customer_id=c.id', 'left');
				$this->datatables->join('pad_usaha u', 'cu.usaha_id=u.id', 'left');
				$this->datatables->join('pad_jenis_pajak p', 's.pajak_id=p.id', 'left');
				$this->datatables->join('pad_spt_type st', 'st.id=s.type_id', 'left');

                if($type_id <> 999 && !empty($type_id))
                    $this->datatables->filter('s.type_id', $type_id);
                    
				if ($this->input->get('iSortCol_0') == 1) {
					$sort = $this->input->get('sSortDir_0');
					$this->datatables->order_by('s.sptno', $sort);
				}
                
                if($this->input->get('sSearch') == '')
                    $this->datatables->filter('s.terimatgl', $terimatgl);
			}

			if ($this->controller == 'skpd') {
				$this->datatables->select('s.id, get_kohirno(s.id) as kohirno, k.kohirtgl, get_sptno(s.id) as sptno,
					get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as nama, 
                    p.nama, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.pajak_terhutang as pajak,
					cu.usaha_id, s.type_id', false);
				$this->datatables->from('pad_spt s');
				$this->datatables->join('pad_customer_usaha cu', 's.customer_usaha_id=cu.id', 'left');
				$this->datatables->join('pad_customer c', 'cu.customer_id=c.id', 'left');
				$this->datatables->join('pad_usaha u', 'cu.usaha_id=u.id', 'left');
				$this->datatables->join('pad_jenis_pajak p', 's.pajak_id=p.id', 'left');
				$this->datatables->join('pad_spt_type st', 'st.id=s.type_id', 'left');
				$this->datatables->join('pad_kohir k', 'k.spt_id=s.id', 'left');

				$this->datatables->where('upper(st.typenm) like', 'SK%');

				if ($this->input->get('iSortCol_0') == 1) {
					$sort = $this->input->get('sSortDir_0');
					$this->datatables->order_by('k.kohirno', $sort);
				}
                
                if ($proses_id == 2) 
                    $this->datatables->where('k.kohirno is not NULL');
                else
                    $this->datatables->where('k.kohirno is NULL');
                    
                if($this->input->get('sSearch') == '')
                    if ($proses_id == 2) 
                        $this->datatables->filter('k.kohirtgl', $terimatgl);
                    else
                        $this->datatables->filter('s.terimatgl', $terimatgl);
			}

			//$this->datatables->where('s.tahun', pad_tahun_anggaran());
			if($usaha_id <> 999 && !empty($usaha_id))
				$this->datatables->filter('cu.usaha_id', $usaha_id);
            
			$this->datatables->rupiah_column('8,9');
			$this->datatables->date_column('2');
			
		} else {
			if ($this->controller == 'sptpd') {
				$this->datatables->select('s.id, (s.tahun||\'.\'||s.sptno::text) as sptno, s.terimatgl,
					get_nopd(cu.id, true) as npwpd, c.nama, s.masadari, s.masasd,
                    s.dasar, s.pajak_terhutang as pajak,
					case when tl.id > 0 then 1 else 0 end tid, 
					cu.usaha_id, s.type_id', false);
				$this->datatables->from('pad_spt s');
				$this->datatables->join('pad_customer_usaha cu', 's.customer_usaha_id=cu.id', 'left');
				$this->datatables->join('pad_customer c', 'cu.customer_id=c.id', 'left');
				$this->datatables->join('pad_usaha u', 'cu.usaha_id=u.id', 'left');
				$this->datatables->join('pad_jenis_pajak p', 's.pajak_id=p.id', 'left');
				$this->datatables->join('pad_spt_type st', 'st.id=s.type_id', 'left');
				$this->datatables->join('pad_terima_line tl', 's.id=tl.spt_id', 'left');

				if ($this->input->get('iSortCol_0') == 1) {
					$sort = $this->input->get('sSortDir_0');
					$this->datatables->order_by('s.sptno', $sort);
				}
			}
			
			//$this->datatables->where('s.tahun', pad_tahun_anggaran());
			$this->datatables->where('c.id', wp_id());
			
			if($type_id <> 999 && !empty($type_id))
				$this->datatables->where('s.type_id', $type_id);
			if($usaha_id <> 999 && !empty($usaha_id))
				$this->datatables->where('cu.usaha_id', $usaha_id);

			$this->datatables->checkbox_column('9');
			$this->datatables->rupiah_column('8,9');
			$this->datatables->date_column('2');
		}
		

        echo $this->datatables->generate();
    }

    //admin
    private function fvalidation($jenis_usaha = null)
    {
        //spt global
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('tahun', 'tahun', 'required|numeric');
        $this->form_validation->set_rules('terimatgl', 'terimatgl', 'required');
        $this->form_validation->set_rules('customer_id', 'customer_id', 'required|numeric');
        $this->form_validation->set_rules('customer_usaha_id', 'customer_usaha_id', 'required|numeric');
        $this->form_validation->set_rules('pajak_id', 'pajak_id', 'required|numeric');
        $this->form_validation->set_rules('rekening_id', 'rekening_id', 'required|numeric');
        $this->form_validation->set_rules('so', 'so', 'required|trim');
        $this->form_validation->set_rules('type_id', 'type_id', 'required|numeric');
        $this->form_validation->set_rules('masadari', 'masadari', 'required');
        $this->form_validation->set_rules('masasd', 'masasd', 'required');
        $this->form_validation->set_rules('jatuhtempotgl', 'jatuhtempotgl', 'required');
        $this->form_validation->set_rules('r_bayarid', 'r_bayarid', 'required|numeric');

        //air
        if ($jenis_usaha == pad_air_tanah_id()) {
            $this->form_validation->set_rules('air_manfaat_id', 'air_manfaat_id', 'required|numeric');
            $this->form_validation->set_rules('air_zona_id', 'air_zona_id', 'required|numeric');
            $this->form_validation->set_rules('meteran_awal', 'meteran_awal', 'required|numeric');
            $this->form_validation->set_rules('meteran_akhir', 'meteran_akhir', 'required|numeric');
            $this->form_validation->set_rules('volume', 'volume', 'required|numeric');
            $this->form_validation->set_rules('satuan', 'satuan', 'required|trim');
        }

        //reklame
        if ($jenis_usaha == pad_reklame_id()) {
            $this->form_validation->set_rules('r_nsl_kecamatan_id', 'r_nsl_kecamatan_id', 'required|numeric');
            $this->form_validation->set_rules('r_nsl_type_id', 'r_nsl_type_id', 'required|numeric');
            $this->form_validation->set_rules('r_nsl_nilai', 'r_nsl_nilai', 'required|numeric');
            $this->form_validation->set_rules('r_kelurahan_id', 'r_kelurahan_id', 'required|numeric');
            $this->form_validation->set_rules('r_nsr', 'r_nsr', 'required|numeric');
            $this->form_validation->set_rules('r_nsrno', 'r_nsrno', 'required|trim');
            $this->form_validation->set_rules('r_nsrtgl', 'r_nsrtgl', 'required');
            $this->form_validation->set_rules('r_tarifid', 'r_tarifid', 'required|numeric');
            $this->form_validation->set_rules('r_kontrak', 'r_kontrak', 'required|numeric');
            $this->form_validation->set_rules('r_lama', 'r_lama', 'required|numeric');
            $this->form_validation->set_rules('r_jalanklas_id', 'r_jalanklas_id', 'required|numeric');
            $this->form_validation->set_rules('r_jalan_id', 'r_jalan_id', 'required|numeric');
            $this->form_validation->set_rules('r_lokasi', 'r_lokasi', 'required|trim');
            $this->form_validation->set_rules('r_judul', 'r_judul', 'required|trim');
            $this->form_validation->set_rules('r_panjang', 'r_panjang', 'required|numeric');
            $this->form_validation->set_rules('r_lebar', 'r_lebar', 'required|numeric');
            $this->form_validation->set_rules('r_muka', 'r_muka', 'required|numeric');
            $this->form_validation->set_rules('r_banyak', 'r_banyak', 'required|numeric');
            $this->form_validation->set_rules('r_luas', 'r_luas', 'required|numeric');
            $this->form_validation->set_rules('r_lokasi_id', 'r_lokasi_id', 'required|numeric');
            $this->form_validation->set_rules('r_calculated', 'r_calculated', 'required|numeric');
            $this->form_validation->set_rules('r_status', 'r_status', 'trim');
        }
    }

    private function fpost($jenis_usaha = NULL)
    { 
        //spt global
        $data['nopd']              = $this->input->post('nopd');

        $data['id']                = $this->input->post('id');
        $data['sptno']             = $this->input->post('sptno');
        $data['tahun']             = $this->input->post('tahun');
        $data['terimatgl']         = $this->input->post('terimatgl');
        $data['customer_id']       = $this->input->post('customer_id');
        $data['customer_usaha_id'] = $this->input->post('customer_usaha_id');
        $data['pajak_id']          = $this->input->post('pajak_id');
        $data['rekening_id']       = $this->input->post('rekening_id');
        $data['so']                = $this->input->post('so');
        $data['type_id']           = $this->input->post('type_id');
        $data['masadari']          = $this->input->post('masadari');
        $data['masasd']            = $this->input->post('masasd');
        $data['jatuhtempotgl']     = $this->input->post('jatuhtempotgl');

        $data['minimal_omset']   = $this->input->post('minimal_omset');
        $data['dasar']      = pad_to_decimal($this->input->post('dasar'));
        $data['tarif']      = pad_to_decimal($this->input->post('tarif'));
        $data['denda']      = pad_to_decimal($this->input->post('denda'));
        $data['bunga']      = pad_to_decimal($this->input->post('bunga'));
        $data['setoran']    = pad_to_decimal($this->input->post('setoran'));
        $data['kenaikan']   = pad_to_decimal($this->input->post('kenaikan'));
        $data['kompensasi'] = pad_to_decimal($this->input->post('kompensasi'));
        $data['lain2']      = pad_to_decimal($this->input->post('lain2'));
		
        $data['r_nsr']     = pad_to_decimal($this->input->post('r_nsr'));
        $data['r_bayarid'] = $this->input->post('r_bayarid');

        $data['kirimtgl']     = $this->input->post('kirimtgl');
        $data['notes']        = $this->input->post('notes');
        $data['no_skpd_lama'] = $this->input->post('no_skpd_lama');
        $data['unit_id']      = $this->input->post('unit_id');
        $data['enabled']      = $this->input->post('enabled');
        $data['terimanip']    = $this->input->post('terimanip');
        $data['isprint_dc']   = $this->input->post('isprint_dc');

        $data['created'] = $this->input->post('created');
        $data['create_uid']  = $this->input->post('create_uid');
        $data['updated']  = $this->input->post('updated');
        $data['update_uid']   = $this->input->post('update_uid');
        
        $data['nama']   = $this->input->post('nama'); // kota bogor

        //air
        if ($jenis_usaha == pad_air_tanah_id()) {
            $data['air_manfaat_id'] = $this->input->post('air_manfaat_id');
            $data['air_zona_id']    = $this->input->post('air_zona_id');
            $data['meteran_awal']   = pad_to_decimal($this->input->post('meteran_awal'));
            $data['meteran_akhir']  = pad_to_decimal($this->input->post('meteran_akhir'));
            $data['volume']         = pad_to_decimal($this->input->post('volume'));
            $data['satuan']         = $this->input->post('satuan');
        }

        //reklame
        if ($jenis_usaha == pad_reklame_id()) {
            $data['r_nsrno']  = $this->input->post('r_nsrno');
            $data['r_nsrtgl'] = $this->input->post('r_nsrtgl');

            $data['r_jalanklas_id'] = $this->input->post('r_jalanklas_id');
            $data['r_jalan_id']     = $this->input->post('r_jalan_id');
            $data['r_lokasi']       = $this->input->post('r_lokasi');
            $data['r_judul']        = $this->input->post('r_judul');

            $data['r_panjang'] = pad_to_decimal($this->input->post('r_panjang'), 1);
            $data['r_lebar']   = pad_to_decimal($this->input->post('r_lebar'), 1);
            $data['r_muka']    = pad_to_decimal($this->input->post('r_muka'), 1);
            $data['r_banyak']  = pad_to_decimal($this->input->post('r_banyak'), 1);
            $data['r_luas']    = pad_to_decimal($this->input->post('r_luas'), 1);

            $data['r_tarifid']    = $this->input->post('r_tarifid');
            $data['r_nsl_nilai']  = pad_to_decimal($this->input->post('r_nsl_nilai'));
            $data['r_lama']       = pad_to_decimal($this->input->post('r_lama'));
            $data['r_kontrak']    = pad_to_decimal($this->input->post('r_kontrak'));
            $data['r_calculated'] = pad_to_decimal($this->input->post('r_calculated'));

            $data['r_nsl_kecamatan_id'] = $this->input->post('r_nsl_kecamatan_id');
            $data['r_nsl_type_id']      = $this->input->post('r_nsl_type_id');
            $data['r_lokasi_id']        = $this->input->post('r_lokasi_id');
            $data['r_kelurahan_id']     = $this->input->post('r_kelurahan_id');
            
            //new
            $data['r_nsr_id']     = $this->input->post('r_nsr_id');
            $data['r_lokasi_pasang_id']     = $this->input->post('r_lokasi_pasang_id');
            $data['r_lokasi_pasang_val']     = $this->input->post('r_lokasi_pasang_val');
            $data['r_jalanklas_val']     = $this->input->post('r_jalanklas_val');
            $data['r_sudut_pandang_id']     = $this->input->post('r_sudut_pandang_id');
            $data['r_sudut_pandang_val']     = $this->input->post('r_sudut_pandang_val');
            $data['r_tinggi']     = pad_to_decimal($this->input->post('r_tinggi'),0);
            $data['r_njop']     = pad_to_decimal($this->input->post('r_njop'),1);
            $data['r_status']     = $this->input->post('r_status');
            
        }
		
        $data['pajak'] = pad_to_decimal($this->input->post('pajak'));
        $data['rekeningkd'] = $this->input->post('rekeningkd');
        $data['jatuhtempo'] = $this->input->post('jatuhtempo');
        return $data;
    }

    public function add()
    {
		$this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
		
		// cek apakah wp memiliki jenis pajak ini ->  $this->uri->segment(4)
        if (wp_login()) {
			$model = $this->load->model('pad_model');
			if (!$model->check_cu(wp_id() , $this->uri->segment(4))) {
				$this->session->set_flashdata('msg_error', 'Anda tidak memiliki hak akses menambah data untuk jenis pajak ini.');
				redirect(active_module_url());
			}
        }

        $p_usaha_id = $this->uri->segment(4);
        $post_data  = $this->fpost($p_usaha_id);

        $data['current'] = $this->module;
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("{$this->controller}/add/{$p_usaha_id}");
        $data['dt']      = $post_data;


        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            // $sptno      = $this->sptpd_model->generate_sptno(pad_tahun_anggaran());
            $sptno      = $this->sptpd_model->generate_sptno(date('Y', strtotime($input_post['terimatgl'])));

            //untuk spanduk dan semuaaaaaaaaaaaaa
            $wp_data = $this->load->model('subjek_pajak_model')->get($input_post['customer_id']);
            $wp_nama = $wp_data->nama;
            if($wp_nama != $input_post['nama']) $wp_nama = $input_post['nama'];
            $wp_alamat = $wp_data->alamat;
            
            
            /*
            //cek nama wp - kalo beda bikin baru
            if($wp_nama != $input_post['nama']) {
                $ret = $this->load->model('subjek_pajak_model')->duplicate_wp(
                    $cid,
                    $cuid,
                    $wp_data->rp, 
                    $wp_data->pb,  
                    $wp_data->kecamatan_id,
                    $wp_data->kelurahan_id,
                    $input_post['nama']
                );
                
                $cid  = $ret->cid; 
                $cuid = $ret->cuid;
            }
            */
            
            $cid  = $input_post['customer_id'];
            $cuid = $input_post['customer_usaha_id'];
            
            $update_data = array(                
                'sptno' => $sptno,

                'customer_id' => $cid,
                'customer_usaha_id' => $cuid,
                'pajak_id' => $input_post['pajak_id'],
                'tahun' => date('Y', strtotime($input_post['terimatgl'])), //$input_post['tahun'],
                'terimatgl' => date('Y-m-d', strtotime($input_post['terimatgl'])),
                'type_id' => $input_post['type_id'],
                'so' => $input_post['so'],
                'jatuhtempotgl' => date('Y-m-d', strtotime($input_post['jatuhtempotgl'])),
                'masadari' => date('Y-m-d', strtotime($input_post['masadari'])),
                'masasd' => date('Y-m-d', strtotime($input_post['masasd'])),

                'minimal_omset' => $input_post['minimal_omset'],
                'dasar' => $input_post['dasar'],
                'tarif' => $input_post['tarif'],
                'denda' => $input_post['denda'],
                'bunga' => $input_post['bunga'],
                'setoran' => $input_post['setoran'],
                'kenaikan' => $input_post['kenaikan'],
                'kompensasi' => $input_post['kompensasi'],
                'lain2' => $input_post['lain2'],
				
                'pajak_terhutang' => $input_post['pajak'],

                'r_bayarid' => $input_post['r_bayarid'],
                'r_nsr' => $input_post['r_nsr'],
                'rekening_id' => $input_post['rekening_id'],

                'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
                'terimanip' => sipkd_user_id(),
                'unit_id' => pad_pemda_unitid(),

                'enabled' => 1,
                'satuan' => NULL,
                
                /*
                'updated' => date('Y-m-d', strtotime($input_post['updated'])),
                'update_uid' => $input_post['update_uid'],
                'kirimtgl' => date('Y-m-d', strtotime($input_post['kirimtgl'])),
                'isprint_dc' => $input_post['isprint_dc'],
                'notes' => $input_post['notes'],
                'no_skpd_lama' => $input_post['no_skpd_lama'],
                */
            );

            $reklame_data = array();
            if ($p_usaha_id == pad_reklame_id()) {
                $input_post   = $post_data;
                $reklame_data = array(
                    'r_nsrno' => $input_post['r_nsrno'],
                    'r_nsrtgl' => empty($input_post['r_nsrtgl']) ? NULL : date('Y-m-d', strtotime($input_post['r_nsrtgl'])),
                    'r_tarifid' => $input_post['r_tarifid'],
                    'r_kontrak' => $input_post['r_kontrak'],
                    'r_lama' => $input_post['r_lama'],
                    'r_jalanklas_id' => $input_post['r_jalanklas_id'],
                    'r_jalan_id' => $input_post['r_jalan_id'],
                    'r_lokasi' => $input_post['r_lokasi'],
                    'r_judul' => $input_post['r_judul'],
                    'r_panjang' => $input_post['r_panjang'],
                    'r_lebar' => $input_post['r_lebar'],
                    'r_muka' => $input_post['r_muka'],
                    'r_banyak' => $input_post['r_banyak'],
                    'r_luas' => $input_post['r_luas'],
                    'r_lokasi_id' => $input_post['r_lokasi_id'],
                    'r_calculated' => $input_post['r_calculated'],
                    // 'r_nsl_kecamatan_id' => $input_post['r_nsl_kecamatan_id'],
                    // 'r_nsl_type_id' => $input_post['r_nsl_type_id'],
                    // 'r_nsl_nilai' => $input_post['r_nsl_nilai'],
                    // 'r_kelurahan_id' => $input_post['r_kelurahan_id'],
                    
                    'r_nsr_id' => $input_post['r_nsr_id'],
                    'r_lokasi_pasang_id' => $input_post['r_lokasi_pasang_id'],
                    'r_lokasi_pasang_val' => $input_post['r_lokasi_pasang_val'],
                    'r_jalanklas_val' => $input_post['r_jalanklas_val'],
                    'r_sudut_pandang_id' => $input_post['r_sudut_pandang_id'],
                    'r_sudut_pandang_val' => $input_post['r_sudut_pandang_val'],
                    'r_tinggi' => $input_post['r_tinggi'],
                    'r_njop' => $input_post['r_njop'],
                    'r_status' => $input_post['r_status'],
                    
                    'r_nama' => $wp_nama,
                    'r_alamat' => $wp_alamat,
                
                );
            }

            $air_tanah_data = array();
            if ($p_usaha_id == pad_air_tanah_id()) {
                $input_post     = $post_data;
                $air_tanah_data = array(
                    // 'air_manfaat_id' => $input_post['air_manfaat_id'],
                    // 'air_zona_id' => $input_post['air_zona_id'],
                    // 'meteran_awal' => $input_post['meteran_awal'],
                    // 'meteran_akhir' => $input_post['meteran_akhir'],
                    'volume' => $input_post['volume'],
                    'satuan' => 'M3'
                );
            }

            $update_data = array_merge($update_data, $reklame_data, $air_tanah_data);

            $spt_id = $this->sptpd_model->save($update_data);

            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url($this->controller));
        }

        $data['dt'] = $post_data;
        $get        = (object) $post_data;

        $data['dt']['tahun']      = pad_tahun_anggaran();
        $data['dt']['terimatgl']  = date('d-m-Y');
        $data['dt']['dasar']      = 0;
        $data['dt']['tarif']      = 0;
        $data['dt']['denda']      = 0;
        $data['dt']['bunga']      = 0;
        $data['dt']['setoran']    = 0;
        $data['dt']['lain2']      = 0;
        $data['dt']['kenaikan']   = 0;
        $data['dt']['kompensasi'] = 0;

        $options              = array();
        $js                   = 'id="customer_usaha_id" class="input-xlarge" ';
        $data['select_usaha'] = form_dropdown('customer_usaha_id', $options, null, $js);

        $js                   = 'id="pajak_id" class="input-xxlarge"';
        $data['select_pajak'] = form_dropdown('pajak_id', $options, null, $js);

        $select_data = $this->load->model('sptpd_type_model')->get_select();
        $options     = array();
		if($select_data) {
        foreach ($select_data as $rows) {
            $options[$rows->id] = $rows->typenm;
        }}
        $js                        = 'id="type_id" class="input-small" onChange="void(0);"';
		$type_id = ($p_usaha_id == pad_reklame_id() || ($p_usaha_id == pad_air_tanah_id())) ? pad_dok_office_id() : $get->type_id;
        $data['select_sptpd_type'] = form_dropdown('type_id', $options, $type_id, $js);

		$data['dt']['rekeningkd']  = '';
		$data['dt']['jatuhtempo']  = '';

        if ($p_usaha_id == pad_reklame_id()) {
            $select_data = $this->load->model('jalan_kelas_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->kelasnm;
            }}
            $js                        = 'id="r_jalanklas_id" class="input-xlarge" required ';
            $data['select_jalan_kelas'] = form_dropdown('r_jalanklas_id', $options, $get->r_jalanklas_id, $js);

            $select_data = $this->load->model('jalan_model')->get_select();
            $options     = array();
			if($select_data) {
            $options[] = "# KOSONG #";
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                   = 'id="r_jalan_id" class="input-large combobox" ';
            $data['select_jalan'] = form_dropdown('r_jalan_id', $options, $get->r_jalan_id, $js);

            /*
            $select_data = $this->load->model('kecamatan_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->kecamatannm;
            }}
            $js                    = 'id="r_lokasi_id" class="input-medium" required ';
            $data['select_lokasi'] = form_dropdown('r_lokasi_id', $options, $get->r_lokasi_id, $js);
            */

            $options              = array(
                1 => 'Tidak ada',
                2 => 'Produk Rokok +25%',
                3 => 'Reklame Pendidikan -25%',
                4 => 'Kenaikan 25% & Pengurangan 25%'
            );
            $js                   = 'id="r_tarifid" class="input-large" required ';
            $data['select_tarif'] = form_dropdown('r_tarifid', $options, $get->r_tarifid, $js);

            //-new
            $options              = array(
                'Pasang Baru' => 'Pasang Baru',
                'Perpanjangan' => 'Perpanjangan',
            );
            $js                   = 'id="r_status" class="input-large" required ';
            $data['select_status'] = form_dropdown('r_status', $options, $get->r_status, $js);
            
            $select_data = $this->load->model('reklame_nilai_strategis_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                        = 'id="r_nsr_id" class="input-xlarge" required ';
            $data['select_nsr'] = form_dropdown('r_nsr_id', $options, $get->r_nsr_id, $js);
            
            $select_data = $this->load->model('reklame_lokasi_pasang_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                        = 'id="r_lokasi_pasang_id" class="input-xlarge" required ';
            $data['select_lokasi_pasang'] = form_dropdown('r_lokasi_pasang_id', $options, $get->r_lokasi_pasang_id, $js);
            
            $select_data = $this->load->model('reklame_sudut_pandang_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                        = 'id="r_sudut_pandang_id" class="input-xlarge" required ';
            $data['select_sudut_pandang'] = form_dropdown('r_sudut_pandang_id', $options, $get->r_sudut_pandang_id, $js);
            //-end-new
            
			if (!wp_login())
				$this->load->view('vsptpd_form_reklame', $data);
			else {
				$data['dt']['customer_id'] = wp_id();
				$this->load->view('wp/vsptpd_form_reklame', $data);
			}
				
        } else if ($p_usaha_id == pad_air_tanah_id()) {
            $select_data = $this->load->model('air_zona_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                  = 'id="air_zona_id" class="input-medium" required ';
            $data['select_zona'] = form_dropdown('air_zona_id', $options, $get->air_zona_id, $js);

            $select_data = $this->load->model('air_manfaat_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                     = 'id="air_manfaat_id" class="input-medium" required ';
            $data['select_manfaat'] = form_dropdown('air_manfaat_id', $options, $get->air_manfaat_id, $js);
			
			if (!wp_login())
				$this->load->view('vsptpd_form_at', $data);
			else {
				$data['dt']['customer_id'] = wp_id();
				$this->load->view('wp/vsptpd_form_at', $data);
			}
			
        } else {
			if (!wp_login())
				$this->load->view('vsptpd_form', $data);
			else {
				$data['dt']['customer_id'] = wp_id();
				$this->load->view('wp/vsptpd_form', $data);
			}
		}
    }

    public function edit()
    {
		$this->load_auth();
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
		
        $p_usaha_id = $this->uri->segment(4);
        $p_type_id  = $this->uri->segment(5);
        $p_id       = $this->uri->segment(6);

		//cek kohir
        if ($this->sptpd_model->is_kohir_ok($p_id) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
		
		// cek pmb
        // kalau user sa boleh edit. 17-10-2014 (AA)
        if (($this->sptpd_model->is_sspd_ok($p_id) || $this->sptpd_model->is_bayar_ok($p_id)) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
        
        /*
        $data['editable'] = 1;
        if ($this->sptpd_model->is_kohir_ok($p_id) || $this->sptpd_model->is_sspd_ok($p_id) || $this->sptpd_model->is_bayar_ok($p_id)) {
            $data['editable'] = 0;
        }
        */
		
        $data['current'] = $this->module;
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("{$this->controller}/update/{$p_usaha_id}/{$p_type_id}/{$p_id}");

        if ($p_id && $get = $this->sptpd_model->get($p_id)) {
            $data['dt']['id']                 = $get->id;
            $data['dt']['customer_usaha_id']  = $get->customer_usaha_id;
            $data['dt']['pajak_id']           = $get->pajak_id;
            $data['dt']['tahun']              = $get->tahun;
            $data['dt']['sptno']              = $get->sptno;
            $data['dt']['terimanip']          = $get->terimanip;
            $data['dt']['terimatgl']          = date('d-m-Y', strtotime($get->terimatgl));
            $data['dt']['kirimtgl']           = date('d-m-Y', strtotime($get->kirimtgl));
            $data['dt']['jatuhtempotgl']      = date('d-m-Y', strtotime($get->jatuhtempotgl));
            $data['dt']['type_id']            = $get->type_id;
            $data['dt']['so']                 = $get->so;
            $data['dt']['masadari']           = date('d-m-Y', strtotime($get->masadari));
            $data['dt']['masasd']             = date('d-m-Y', strtotime($get->masasd));
            $data['dt']['minimal_omset']           = $get->minimal_omset;
            $data['dt']['dasar']              = $get->dasar;
            $data['dt']['tarif']              = $get->tarif;
            $data['dt']['denda']              = $get->denda;
            $data['dt']['bunga']              = $get->bunga;
            $data['dt']['setoran']            = $get->setoran;
            $data['dt']['kenaikan']           = $get->kenaikan;
            $data['dt']['kompensasi']         = $get->kompensasi;
            $data['dt']['lain2']              = $get->lain2;
            $data['dt']['air_manfaat_id']     = $get->air_manfaat_id;
            $data['dt']['air_zona_id']        = $get->air_zona_id;
            $data['dt']['meteran_awal']       = $get->meteran_awal;
            $data['dt']['meteran_akhir']      = $get->meteran_akhir;
            $data['dt']['volume']             = $get->volume;
            $data['dt']['satuan']             = $get->satuan;
            $data['dt']['r_nsr']              = $get->r_nsr;
            $data['dt']['r_nsrno']            = $get->r_nsrno;
            $data['dt']['r_nsrtgl']           = empty($get->r_nsrtgl) ? NULL : date('d-m-Y', strtotime($get->r_nsrtgl));
            $data['dt']['r_bayarid']          = $get->r_bayarid;
            $data['dt']['r_tarifid']          = $get->r_tarifid;
            $data['dt']['r_kontrak']          = $get->r_kontrak;
            $data['dt']['r_lama']             = $get->r_lama;
            $data['dt']['r_jalanklas_id']     = $get->r_jalanklas_id;
            $data['dt']['r_jalan_id']         = $get->r_jalan_id;
            $data['dt']['r_lokasi']           = $get->r_lokasi;
            $data['dt']['r_judul']            = $get->r_judul;
            $data['dt']['r_panjang']          = $get->r_panjang;
            $data['dt']['r_lebar']            = $get->r_lebar;
            $data['dt']['r_muka']             = $get->r_muka;
            $data['dt']['r_banyak']           = $get->r_banyak;
            $data['dt']['r_luas']             = $get->r_luas;
            $data['dt']['enabled']            = $get->enabled;
            $data['dt']['unit_id']            = $get->unit_id;
            $data['dt']['created']        = date('d-m-Y', strtotime($get->created));
            $data['dt']['create_uid']         = $get->create_uid;
            $data['dt']['updated']         = date('d-m-Y', strtotime($get->updated));
            $data['dt']['update_uid']          = $get->update_uid;
            $data['dt']['customer_id']        = $get->customer_id;
            $data['dt']['r_nsl_kecamatan_id'] = $get->r_nsl_kecamatan_id;
            $data['dt']['r_nsl_type_id']      = $get->r_nsl_type_id;
            $data['dt']['r_nsl_nilai']        = $get->r_nsl_nilai;
            $data['dt']['r_kelurahan_id']     = $get->r_kelurahan_id;
            $data['dt']['isprint_dc']         = $get->isprint_dc;
            $data['dt']['notes']              = $get->notes;
            $data['dt']['r_lokasi_id']        = $get->r_lokasi_id;
            $data['dt']['rekening_id']        = $get->rekening_id;
            $data['dt']['r_calculated']       = $get->r_calculated;
            
            $data['dt']['r_njop']       = $get->r_njop;
            $data['dt']['r_jalanklas_val']       = $get->r_jalanklas_val;
            $data['dt']['r_lokasi_pasang_val']       = $get->r_lokasi_pasang_val;
            $data['dt']['r_sudut_pandang_val']       = $get->r_sudut_pandang_val;
            $data['dt']['r_tinggi']       = $get->r_tinggi;
            $data['dt']['r_status']       = $get->r_status;
            
            $data['dt']['nama']       = $get->r_nama;
            
			$data['dt']['nopd'] = $this->load->model('objek_pajak_model')->get_nopd($get->customer_usaha_id, false);

            $options              = array();
            $js                   = 'id="customer_usaha_id" class="input-xlarge" ';
            $data['select_usaha'] = form_dropdown('customer_usaha_id', $options, null, $js);

            $select_data = $this->load->model('pad_model')->sptpd_get_pajak($p_usaha_id);
            $options     = array();
			if($select_data) {
            foreach ($select_data as $rows) {
                if($p_usaha_id == pad_reklame_id())
                    $options[$rows->id] = $rows->nama." = ".number_format($rows->reklame ,0,',','.');
                else
                    $options[$rows->id] = $rows->nama;
            }}
            $js                   = 'id="pajak_id" class="input-xxlarge"';
            $data['select_pajak'] = form_dropdown('pajak_id', $options, $get->pajak_id, $js);

            $select_data = $this->load->model('sptpd_type_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $rows) {
                $options[$rows->id] = $rows->typenm;
            }}
            $js                        = 'id="type_id" class="input-small" onChange="void(0);"';
            $data['select_sptpd_type'] = form_dropdown('type_id', $options, $get->type_id, $js);

            $pajak_detail = $this->load->model('pad_model');
            if ($row = $pajak_detail->sptpd_get_pajak_detail($get->pajak_id, $get->terimatgl)) {
                $data['dt']['rekening_id'] = $row->rekening_id;
                $data['dt']['rekeningkd']  = $row->rekeningkd;
                $data['dt']['jatuhtempo']  = $row->jatuhtempo;
            } else {
                $data['dt']['rekeningkd']  = '';
                $data['dt']['jatuhtempo']  = '';
            }

            if ($p_usaha_id == pad_reklame_id() && $p_type_id == pad_dok_office_id()) {
                //-new
                $select_data = $this->load->model('reklame_nilai_strategis_model')->get_select();
                $options     = array();
                if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }}
                $js                        = 'id="r_nsr_id" class="input-xlarge" required ';
                $data['select_nsr'] = form_dropdown('r_nsr_id', $options, $get->r_nsr_id, $js);
                
                $select_data = $this->load->model('reklame_lokasi_pasang_model')->get_select();
                $options     = array();
                if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }}
                $js                        = 'id="r_lokasi_pasang_id" class="input-xlarge" required ';
                $data['select_lokasi_pasang'] = form_dropdown('r_lokasi_pasang_id', $options, $get->r_lokasi_pasang_id, $js);
                
                $select_data = $this->load->model('reklame_sudut_pandang_model')->get_select();
                $options     = array();
                if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }}
                $js                        = 'id="r_sudut_pandang_id" class="input-xlarge" required ';
                $data['select_sudut_pandang'] = form_dropdown('r_sudut_pandang_id', $options, $get->r_sudut_pandang_id, $js);
                
                $options = array(
                    'Pasang Baru' => 'Pasang Baru',
                    'Perpanjangan' => 'Perpanjangan',
                );
                $js = 'id="r_status" class="input-large" required ';
                $data['select_status'] = form_dropdown('r_status', $options, $get->r_status, $js);
                //-end-new
                
                $select_data = $this->load->model('jalan_kelas_model')->get_select();
                $options     = array();
				if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->kelasnm;
                }}
                $js                        = 'id="r_jalanklas_id" class="input-xlarge" required ';
                $data['select_jalan_kelas'] = form_dropdown('r_jalanklas_id', $options, $get->r_jalanklas_id, $js);

                $select_data = $this->load->model('jalan_model')->get_select();
                $options     = array();
				if($select_data) {
                $options[] = "# KOSONG #";
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }}
                $js                   = 'id="r_jalan_id" class="input-large combobox" ';
                $data['select_jalan'] = form_dropdown('r_jalan_id', $options, $get->r_jalan_id, $js);

                $select_data = $this->load->model('kecamatan_model')->get_select();
                $options     = array();
				if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->kecamatannm;
                }}				
                $js                    = 'id="r_lokasi_id" class="input-medium" required ';
                $data['select_lokasi'] = form_dropdown('r_lokasi_id', $options, $get->r_lokasi_id, $js);

                $options              = array(
                    1 => 'Tidak ada',
                    2 => 'Produk Rokok +25%',
                    3 => 'Reklame Pendidikan -25%',
                    4 => 'Kenaikan 25% & Pengurangan 25%'
                );
				$js                   = 'id="r_tarifid" class="input-large" required ';
                $data['select_tarif'] = form_dropdown('r_tarifid', $options, $get->r_tarifid, $js);

				if (!wp_login())
					$this->load->view('vsptpd_form_reklame', $data);
				else {
					$data['dt']['customer_id'] = wp_id();
					$this->load->view('wp/vsptpd_form_reklame', $data);
				}
            } else if ($p_usaha_id == pad_air_tanah_id() && $p_type_id == pad_dok_office_id()) {
                $select_data = $this->load->model('air_zona_model')->get_select();
                $options     = array();
				if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }}
                $js                  = 'id="air_zona_id" class="input-medium" required ';
                $data['select_zona'] = form_dropdown('air_zona_id', $options, $get->air_zona_id, $js);

                $select_data = $this->load->model('air_manfaat_model')->get_select();
                $options     = array();
				if($select_data) {
                foreach ($select_data as $row) {
                    $options[$row->id] = $row->nama;
                }}
                $js                     = 'id="air_manfaat_id" class="input-medium" required ';
                $data['select_manfaat'] = form_dropdown('air_manfaat_id', $options, $get->air_manfaat_id, $js);

				if (!wp_login())
					$this->load->view('vsptpd_form_at', $data);
				else {
					$data['dt']['customer_id'] = wp_id();
					$this->load->view('wp/vsptpd_form_at', $data);
				}
            } else {
				if (!wp_login())
					$this->load->view('vsptpd_form', $data);
				else {
					$data['dt']['customer_id'] = wp_id();
					$this->load->view('wp/vsptpd_form', $data);
				}
			}
        } else {
            show_404();
        }
    }

    public function update()
    {
		$this->load_auth();
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
		
        $p_usaha_id = $this->uri->segment(4);
        $p_type_id  = $this->uri->segment(5);
        $p_id       = $this->uri->segment(6);

		//cek kohir
        if ($this->sptpd_model->is_kohir_ok($p_id) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
		
		// cek pmb
        // kalau user sa boleh edit. 17-10-2014 (AA)
        if (($this->sptpd_model->is_sspd_ok($p_id) || $this->sptpd_model->is_bayar_ok($p_id)) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
		
        $data['current'] = $this->module;
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("{$this->controller}/update/{$p_usaha_id}/{$p_type_id}/{$p_id}");

        $post_data = $this->fpost($p_usaha_id);

        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            
            //cek nama wp - kalo beda update nama
            /*
            $wp_data = $this->load->model('subjek_pajak_model')->get($input_post['customer_id']);
            $wp_nama = $wp_data->nama;
            $cid  = $input_post['customer_id'];
            if($wp_nama != $input_post['nama']) {
                $this->load->model('subjek_pajak_model')->rename_wp($cid, $input_post['nama']);
            }
            */
            
            $update_data = array(
                'customer_id' => $input_post['customer_id'],
                'customer_usaha_id' => $input_post['customer_usaha_id'],
                'pajak_id' => $input_post['pajak_id'],
                'tahun' => date('Y', strtotime($input_post['terimatgl'])), //$input_post['tahun'],
                'terimatgl' => date('Y-m-d', strtotime($input_post['terimatgl'])),
                'type_id' => $input_post['type_id'],
                'so' => $input_post['so'],
                'jatuhtempotgl' => date('Y-m-d', strtotime($input_post['jatuhtempotgl'])),
                'masadari' => date('Y-m-d', strtotime($input_post['masadari'])),
                'masasd' => date('Y-m-d', strtotime($input_post['masasd'])),

                'minimal_omset' => $input_post['minimal_omset'],
                'dasar' => $input_post['dasar'],
                'tarif' => $input_post['tarif'],
                'denda' => $input_post['denda'],
                'bunga' => $input_post['bunga'],
                'setoran' => $input_post['setoran'],
                'kenaikan' => $input_post['kenaikan'],
                'kompensasi' => $input_post['kompensasi'],
                'lain2' => $input_post['lain2'],
				
				'pajak_terhutang' => $input_post['pajak'],

                'r_bayarid' => $input_post['r_bayarid'],
                'r_nsr' => $input_post['r_nsr'],
                'rekening_id' => $input_post['rekening_id'],

                'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),

                /*
                'terimanip' => sipkd_user_id()
                'unit_id' => pad_pemda_unitid(),
                'enabled' => 1,
                'updated' => date('Y-m-d', strtotime($input_post['updated'])),
                'update_uid' => $input_post['update_uid'],
                'kirimtgl' => date('Y-m-d', strtotime($input_post['kirimtgl'])),
                'isprint_dc' => $input_post['isprint_dc'],
                'notes' => $input_post['notes'],
                'no_skpd_lama' => $input_post['no_skpd_lama'],
                */
            );

            $reklame_data = array();
            if ($p_usaha_id == pad_reklame_id()) {
                $input_post   = $post_data;
                $reklame_data = array(
                    'r_nsrno' => $input_post['r_nsrno'],
                    'r_nsrtgl' => empty($input_post['r_nsrtgl']) ? NULL : date('Y-m-d', strtotime($input_post['r_nsrtgl'])),
                    'r_tarifid' => $input_post['r_tarifid'],
                    'r_kontrak' => $input_post['r_kontrak'],
                    'r_lama' => $input_post['r_lama'],
                    'r_jalanklas_id' => $input_post['r_jalanklas_id'],
                    'r_jalan_id' => $input_post['r_jalan_id'],
                    'r_lokasi' => $input_post['r_lokasi'],
                    'r_judul' => $input_post['r_judul'],
                    'r_panjang' => $input_post['r_panjang'],
                    'r_lebar' => $input_post['r_lebar'],
                    'r_muka' => $input_post['r_muka'],
                    'r_banyak' => $input_post['r_banyak'],
                    'r_luas' => $input_post['r_luas'],
                    'r_lokasi_id' => $input_post['r_lokasi_id'],
                    'r_calculated' => $input_post['r_calculated'],
                    // 'r_nsl_kecamatan_id' => $input_post['r_nsl_kecamatan_id'],
                    // 'r_nsl_type_id' => $input_post['r_nsl_type_id'],
                    // 'r_nsl_nilai' => $input_post['r_nsl_nilai'],
                    // 'r_kelurahan_id' => $input_post['r_kelurahan_id'],
                    
                    'r_nsr_id' => $input_post['r_nsr_id'],
                    'r_lokasi_pasang_id' => $input_post['r_lokasi_pasang_id'],
                    'r_lokasi_pasang_val' => $input_post['r_lokasi_pasang_val'],
                    'r_jalanklas_val' => $input_post['r_jalanklas_val'],
                    'r_sudut_pandang_id' => $input_post['r_sudut_pandang_id'],
                    'r_sudut_pandang_val' => $input_post['r_sudut_pandang_val'],
                    'r_tinggi' => $input_post['r_tinggi'],
                    'r_njop' => $input_post['r_njop'],
                    'r_status' => $input_post['r_status'],
                    
                    'r_nama' => $input_post['nama'],
                );
            }

            $air_tanah_data = array();
            if ($p_usaha_id == pad_air_tanah_id()) {
                $input_post     = $post_data;
                $air_tanah_data = array(
                    // 'air_manfaat_id' => $input_post['air_manfaat_id'],
                    // 'air_zona_id' => $input_post['air_zona_id'],
                    // 'meteran_awal' => $input_post['meteran_awal'],
                    // 'meteran_akhir' => $input_post['meteran_akhir'],
                    'volume' => $input_post['volume'],
                    'satuan' => 'M3'
                );
            }

            $update_data = array_merge($update_data, $reklame_data, $air_tanah_data);

            $this->sptpd_model->update($p_id, $update_data);

            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url($this->controller));
        }

        $data['dt'] = $post_data;
        $get        = (object) $post_data;

        $options              = array();
        $js                   = 'id="customer_usaha_id" class="input-xlarge"';
        $data['select_usaha'] = form_dropdown('customer_usaha_id', $options, null, $js);

        $select_data = $this->load->model('pad_model')->sptpd_get_pajak($p_usaha_id);
        $options     = array();
		if($select_data) {
        foreach ($select_data as $rows) {
            if($p_usaha_id == pad_reklame_id())
                $options[$rows->id] = $rows->nama." = ".number_format($rows->reklame ,0,',','.');
            else
                $options[$rows->id] = $rows->nama;
        }}
        $js                   = 'id="pajak_id" class="input-xxlarge"';
        $data['select_pajak'] = form_dropdown('pajak_id', $options, $get->pajak_id, $js);

        $select_data = $this->load->model('sptpd_type_model')->get_select();
        $options     = array();
		if($select_data) {
        foreach ($select_data as $rows) {
            $options[$rows->id] = $rows->typenm;
        }}
        $js                        = 'id="type_id" class="input-small" onChange="void(0);"';
        $data['select_sptpd_type'] = form_dropdown('type_id', $options, $get->type_id, $js);

        $pajak_detail = $this->load->model('pad_model');
        if ($row = $pajak_detail->sptpd_get_pajak_detail($get->pajak_id, $get->terimatgl)) {
            $data['dt']['rekening_id'] = $row->rekening_id;
            $data['dt']['rekeningkd']  = $row->rekeningkd;
            $data['dt']['jatuhtempo']  = $row->jatuhtempo;
        } else {
            $data['dt']['rekeningkd']  = '';
            $data['dt']['jatuhtempo']  = '';
        }

        if ($p_usaha_id == pad_reklame_id()) {
            //-new
            $select_data = $this->load->model('reklame_nilai_strategis_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                        = 'id="r_nsr_id" class="input-xlarge" required ';
            $data['select_nsr'] = form_dropdown('r_nsr_id', $options, $get->r_nsr_id, $js);
            
            $select_data = $this->load->model('reklame_lokasi_pasang_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                        = 'id="r_lokasi_pasang_id" class="input-xlarge" required ';
            $data['select_lokasi_pasang'] = form_dropdown('r_lokasi_pasang_id', $options, $get->r_lokasi_pasang_id, $js);
            
            $select_data = $this->load->model('reklame_sudut_pandang_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                        = 'id="r_sudut_pandang_id" class="input-xlarge" required ';
            $data['select_sudut_pandang'] = form_dropdown('r_sudut_pandang_id', $options, $get->r_sudut_pandang_id, $js);
            
            $options = array(
                'Pasang Baru' => 'Pasang Baru',
                'Perpanjangan' => 'Perpanjangan',
            );
            $js = 'id="r_status" class="input-large" required ';
            $data['select_status'] = form_dropdown('r_status', $options, $get->r_status, $js);
            //-end-new
            
            $select_data = $this->load->model('jalan_kelas_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->kelasnm;
            }}
            $js                        = 'id="r_jalanklas_id" class="input-xlarge" required ';
            $data['select_jalan_kelas'] = form_dropdown('r_jalanklas_id', $options, $get->r_jalanklas_id, $js);

            $select_data = $this->load->model('jalan_model')->get_select();
            $options     = array();
			if($select_data) {
            $options[] = "# KOSONG #";
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                   = 'id="r_jalan_id" class="input-large combobox" ';
            $data['select_jalan'] = form_dropdown('r_jalan_id', $options, $get->r_jalan_id, $js);

            $select_data = $this->load->model('kecamatan_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->kecamatannm;
            }}
            $js                    = 'id="r_lokasi_id" class="input-medium" required ';
            $data['select_lokasi'] = form_dropdown('r_lokasi_id', $options, $get->r_lokasi_id, $js);

            $options              = array(
                1 => 'Tidak ada',
                2 => 'Produk Rokok +25%',
                3 => 'Reklame Pendidikan -25%',
                4 => 'Kenaikan 25% & Pengurangan 25%'
            );
            $js                   = 'id="r_tarifid" class="input-large" required ';
            $data['select_tarif'] = form_dropdown('r_tarifid', $options, $get->r_tarifid, $js);

			if (!wp_login())
				$this->load->view('vsptpd_form_reklame', $data);
			else {
				$data['dt']['customer_id'] = wp_id();
				$this->load->view('wp/vsptpd_form_reklame', $data);
			}
        } else if ($p_usaha_id == pad_air_tanah_id()) {
            $select_data = $this->load->model('air_zona_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                  = 'id="air_zona_id" class="input-medium" required ';
            $data['select_zona'] = form_dropdown('air_zona_id', $options, $get->air_zona_id, $js);

            $select_data = $this->load->model('air_manfaat_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                     = 'id="air_manfaat_id" class="input-medium" required ';
            $data['select_manfaat'] = form_dropdown('air_manfaat_id', $options, $get->air_manfaat_id, $js);

			if (!wp_login())
				$this->load->view('vsptpd_form_at', $data);
			else {
				$data['dt']['customer_id'] = wp_id();
				$this->load->view('wp/vsptpd_form_at', $data);
			}
        } else {
			if (!wp_login())
				$this->load->view('vsptpd_form', $data);
			else {
				$data['dt']['customer_id'] = wp_id();
				$this->load->view('wp/vsptpd_form', $data);
			}
		}
    }

    public function delete()
    {
		$this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }

        $id = $this->uri->segment(4);
		
		//cek kohir
        if ($this->sptpd_model->is_kohir_ok($id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
		
		// cek pmb
        if ($this->sptpd_model->is_sspd_ok($id) || $this->sptpd_model->is_bayar_ok($id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
		
        if ($id && $this->sptpd_model->get($id)) {
            $this->sptpd_model->delete($id);
            $this->skpd_model->delete_by_spt($id);
			
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url($this->controller));
        } else {
            show_404();
        }
    }

	// SK
	public function proses_skpd()
	{
		$this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->controller));
        }
		
        $usaha_id = $this->uri->segment(4);
        $spt_id   = $this->uri->segment(5);
        $tgl_proses = $this->uri->segment(6);
        if ($usaha_id && $spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {
			$kohirno = $this->skpd_model->generate_kohirno(pad_tahun_anggaran(), $usaha_id);
			$update_data = array (
				'spt_id' => $spt_id,
				'kohirno' => $kohirno,
				'usaha_id' => $usaha_id,
				'tahun' => pad_tahun_anggaran(),
				'kohirtgl' => date('Y-m-d', strtotime($tgl_proses)),
				// 'kohirtgl' => date('Y-m-d', strtotime($sptpd->terimatgl)),

				'enabled' => 1,
				'created' => date('Y-m-d'),
				'create_uid' => sipkd_user_id(),
			);
			$this->skpd_model->save($update_data);
			
            //update jth tempo 
			// $tgl_proses = new DateTime(Date('Y-m-d'));
			$tgl_proses = new DateTime(Date('Y-m-d', strtotime($tgl_proses)));
			$tgl_jtempo = new DateTime(Date('Y-m-d', strtotime($sptpd->jatuhtempotgl)));
            $masadari = new DateTime(Date('Y-m-d', strtotime($sptpd->masadari)));
			$new_tgl_jtempo = $tgl_jtempo;
            
            $jth_tempo = $this->load->model('pajak_model')->get_jatuhtempo($usaha_id);
            
            if($jth_tempo > 0) {
                $new_tgl_jtempo = $tgl_proses->modify("+30 day");
            } else {
                $xbln = 2;
                $new_tgl_jtempo = $tgl_proses->modify("+{$xbln} month");
                $new_tgl_jtempo = $new_tgl_jtempo->modify("-1 day");
            }
            
            //update jattuh tempo lagi u/ reklame
            $rek = $this->load->model('rekening_model')->get($sptpd->rekening_id);
            $insiden = $rek->insidentil;
            if(substr($rek->rekeningkd,0,5)=='41104') {
                if($insiden==1) {
                    // $new_tgl_jtempo = $masadari;
                    // berdasarkan tgl penetapan cc. om sisco
                    $tgl_proses = new DateTime(Date('Y-m-d'));
                    $new_tgl_jtempo = $tgl_proses;
                }else{
                    $new_tgl_jtempo = $masadari->modify("+30 day");
                }
            
                //lagi
                //28-08-2014 BERUBAH LAGI !! ANJRIT
                /*
                if($sptpd->r_status=='Pasang Baru') {
                    $tgl_proses = new DateTime(Date('Y-m-d'));
                    $new_tgl_jtempo = $tgl_proses->modify("+30 day");
                }
                */
            }
            
            if(substr($rek->rekeningkd,0,5)=='41108') {
                // dimanualkan saja, karena hitung hari kerja
                /*
                $tgl_proses = new DateTime(Date('Y-m-d'));
                $new_tgl_jtempo = $tgl_proses->modify("+20 day");
                */
                $tgl_jtempo = new DateTime(Date('Y-m-d', strtotime($sptpd->jatuhtempotgl)));
                $new_tgl_jtempo = $tgl_jtempo;
            }
            
            $this->sptpd_model->update($spt_id, array('jatuhtempotgl'=>date_format($new_tgl_jtempo, 'Y-m-d')));
            
			echo "ok";
		} else echo "hmm";
	}
	
	public function recall_skpd()
	{
		$this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
		
        $usaha_id = $this->uri->segment(4);
        $spt_id   = $this->uri->segment(5);
		
		// cek pmb
        if ($this->sptpd_model->is_sspd_ok($spt_id) || $this->sptpd_model->is_bayar_ok($spt_id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url($this->controller));
        }
		
        if ($usaha_id && $spt_id && $get = $this->sptpd_model->get($spt_id)) {
            $this->skpd_model->delete_by_spt($spt_id);			
			echo "ok";
		} else echo "hmm";
	}
	
    // -- //

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
            $cu_data->nama        = $rows[0]->nama;
            $cu_data->npwpd             = $rows[0]->npwpd;
            $cu_data->so                = $rows[0]->so;
            $cu_data->konterid          = $rows[0]->konterid;
            $cu_data->air_zona_id       = $rows[0]->air_zona_id;
            $cu_data->air_manfaat_id    = $rows[0]->air_manfaat_id;
            
            $cu_data->wpnama             = $rows[0]->wpnama;
            $cu_data->wpalamat           = $rows[0]->wpalamat;

			$selected = "selected";
            foreach ($rows as $row) {
				if ($cu_id == $row->customer_usaha_id) {
					$usaha .= "<option value={$row->customer_usaha_id} selected>{$row->nama}</option>";
				} else {
					$usaha .= "<option value={$row->customer_usaha_id}>{$row->nama}</option>";
				}
            }
            $cu_data->usaha = $usaha;

            echo json_encode($cu_data);
        }
    }

    function get_pajak()
    {
        $cu_id    = $this->uri->segment(4);
        $model    = $this->load->model('pad_model');
		$usaha_id = $model->sptpd_get_usaha_id($cu_id);

        if ($rows = $model->sptpd_get_pajak($usaha_id)) {
			$pajak = "";
            foreach ($rows as $row) {
                if($row->usaha_id == pad_reklame_id())
                    $pajak .= "<option value={$row->id}>{$row->nama} = ".number_format($row->reklame ,0,',','.')."</option>";
                else
                    $pajak .= "<option value={$row->id}>{$row->nama}</option>";
            }
            echo $pajak;
        }
    }

    function get_nsr()
    {
        $id    = $this->uri->segment(4);
        $model    = $this->load->model('reklame_nilai_strategis_model');

        if ($rows = $model->get($id)) 
            echo $rows->nilai;
        else 
            echo 0;
    }

    function get_jalan()
    {
        // $jalan_kelas_id = $this->uri->segment(4);
        $model         = $this->load->model('jalan_model');

        // if ($rows = $model->get_select($jalan_kelas_id)) {
        if ($rows = $model->get_select()) {
            $jalan = "";
            $jalan .= "<option value=''>#Kosong</option>";
            foreach ($rows as $row) {
                $jalan .= "<option value={$row->id}>{$row->nama}</option>";
            }
            echo $jalan;
        }
    }
    
    function get_klas_jalan()
    {
        $jalan_id = $this->uri->segment(4);
        $model = $this->load->model('jalan_kelas_model');

        if ($rows = $model->get_select($jalan_id)) {
            $jalan = "";
            foreach ($rows as $row) {
                $jalan .= "<option value={$row->id}>{$row->kelasnm}</option>";
            }
            echo $jalan;
        }
    }
    
    function get_jalan_val()
    {
        $jalan_kelas_id = $this->uri->segment(4);
        $model         = $this->load->model('jalan_kelas_model');

        if ($rows = $model->get($jalan_kelas_id)) 
            echo $rows->nilai;
        else 
            echo 0;
    }
    
    function get_lokasi_pasang_val()
    {
        $lokasi_id = $this->uri->segment(4);
        $model         = $this->load->model('reklame_lokasi_pasang_model');

        if ($rows = $model->get($lokasi_id)) 
            echo $rows->nilai;
        else 
            echo 0;
    }
    
    function get_sudut_pandang_val()
    {
        $sudut_id = $this->uri->segment(4);
        $model         = $this->load->model('reklame_sudut_pandang_model');

        if ($rows = $model->get($sudut_id)) 
            echo $rows->nilai;
        else 
            echo 0;
    }

    function get_pajak_detail()
    {
        $pajak_id  = $this->uri->segment(4);
        $pajak_tmt = date('Y-m-d', strtotime($this->uri->segment(5)));
        $model     = $this->load->model('pad_model');

        if ($row = $model->sptpd_get_pajak_detail($pajak_id, $pajak_tmt)) {
            echo json_encode($row);
        }
    }

    function get_hda()
    {
        $zona    = $this->uri->segment(4);
        $manfaat = $this->uri->segment(5);
        $volume  = $this->uri->segment(6);

        $model = $this->load->model('air_hda_model');
        if ($rows = $model->get_hda($zona, $manfaat, $volume)) {
            $dasar = 0;
            $v     = $volume;
            foreach ($rows as $row) {
                $dasar = $dasar + ($v - intval($row->volume_id) + 1) * $row->hda;
                $v     = $row->volume_id - 1;
            }
            echo $dasar;
        } else
            echo 0;
    }
	
	function get_nopd() { // on wp_login only
        $cu_id  = $this->uri->segment(4);
        $model = $this->load->model('objek_pajak_model');
		if ($nopd =  $model->get_nopd($cu_id))
			echo $nopd;
		else
			echo "";
	}
    
    
	/* report */
	function show_rpt() {		
		$cls_mtd_html = $this->router->fetch_class()."/cetak/html";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf";
		$data['rpt_html'] = active_module_url($cls_mtd_html). '?' . $_SERVER['QUERY_STRING'] ;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf) . '?' . $_SERVER['QUERY_STRING'] ;
        $this->load->view('vjasper_viewer', $data);
	}

	function cetak() {		
		$type = $this->uri->segment(4);

		$qs   = urldecode ($_SERVER['QUERY_STRING']);
		parse_str($qs, $qs_data);
		
		$params = array();
		// foreach ($qs_data as $key => $val)
			// $params[$key] = $val;
					
		$sptpd  = $this->load->model('sptpd_model')->get($this->input->get('id'));
		$cu = $this->load->model('objek_pajak_model')->get($sptpd->customer_usaha_id);
        $report = $this->load->model('report_model')->get_report($cu->usaha_id, $sptpd->type_id);
        
		switch ($this->input->get('rpt')) {
			case 'reklame_ipr':
                $rpt = $this->input->get('rpt');
				$params = array(
					'spt_id' => intval($sptpd->id),
				);
				break;
                
			case 'skpd':
                $rpt = $report->sknm;
				$params = array(
					'spt_id' => intval($sptpd->id),
				);
				break;
				
			case 'nota':			
                $rpt = $report->nhnm;		
				$params = array(
					'spt_id' => intval($sptpd->id),
				);
				break;
                
			case 'sptpd':			
                $rpt = $report->sptnm;		
				$params = array(
					'spt_id' => intval($sptpd->id),
				);
				break;
		}
        
        $params = array_merge($params, array(
            "alamat" => pad_pemda_alamat(),
            "telp" => pad_pemda_telp(),
            "fax" => pad_pemda_fax(),
            "website" => pad_pemda_website(),
            "email" => pad_pemda_email(),
            "alamat_lengkap" => pad_pemda_alamat_lengkap(),
            "terbilang" => strtoupper(terbilang($sptpd->pajak_terhutang)),
            
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),            
            "logo" => base_url('assets/img/logorpt__.jpg'),
        ));
        
		$rpt = $rpt;
		// $rpt = $this->module.'/'.$rpt;
		
		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, false);
	}
}
