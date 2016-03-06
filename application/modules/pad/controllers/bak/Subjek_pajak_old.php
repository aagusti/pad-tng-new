<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class subjek_pajak extends CI_Controller {
    
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
            'apps_model', 'subjek_pajak_model'
        ));
		
		$this->load->helper(active_module('pad_helper'));
    }
    
    public function index() {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vsubjek_pajak', $data);
    }
    
    function grid() {
        $this->load->library('Datatables');
        $this->datatables->select('c.id, get_npwpd(c.id, true) as npwpd, c.nama, 
			(case when c.wpnama=\'\' then c.pnama else c.wpnama end) as p_nama, c.alamat, 
			(select count(*) as jml from pad_customer_usaha where customer_id = c.id) as jml_op', false);
        $this->datatables->from('pad_customer c');
        $this->datatables->where('c.rp', 'P');
        $this->datatables->numeric_column('5');
		
        echo $this->datatables->generate();
    }
	
    function grid_for_spt() {
        $this->load->library('Datatables');
        $this->datatables->select('c.id, get_npwpd(c.id, true) as npwpd, c.nama, 
			(case when c.wpnama=\'\' then c.pnama else c.wpnama end) as p_nama, c.alamat, cu.nama as cu_nama, cu.id as cu_id', false);
        $this->datatables->from('pad_customer c');
        $this->datatables->join('(select cu.id, cu.usaha_id, u.nama, cu.customer_id 
			from pad_customer_usaha cu 
			inner join pad_usaha u on cu.usaha_id = u.id) as cu', 'cu.customer_id = c.id');
        $this->datatables->where('c.rp', 'P');
		
		if ($this->uri->segment(4) <> '')
			$this->datatables->where('cu.usaha_id', $this->uri->segment(4));
			
        echo $this->datatables->generate();
    }
	
	function grid_objek_pajak() {
        $cid = $this->uri->segment(4);
		
        $this->load->library('Datatables');
        $this->datatables->select('id, konterid, notes');
        $this->datatables->from('pad_customer_usaha');
        $this->datatables->where('customer_id', $cid);
        echo $this->datatables->generate();
	}
    
    // grid kartu data hotel
    function grid_kd() 
    {
        $id = $this->uri->segment(4);

        $this->load->library('Datatables',$this->load->database('pad', TRUE));
        $this->datatables->select('notes, tarif, kamar, volume', false);
        $this->datatables->from('pad_customer_detail');			
        $this->datatables->where('customer_id', $id);

        $this->datatables->add_column('batal', '<a class="delete" href="">Hapus</a>');
        
        echo $this->datatables->generate();
    }
    
    //admin
    private function fvalidation() {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        
        $this->form_validation->set_rules('pb', 'pb', 'required|numeric');
        $this->form_validation->set_rules('rp','rp','required|trim');
        $this->form_validation->set_rules('reg_date', 'reg_date', 'required');
        $this->form_validation->set_rules('nama', 'nama', 'required|trim');
        $this->form_validation->set_rules('alamat', 'alamat', 'required|trim');
        $this->form_validation->set_rules('kecamatan_id', 'kecamatan_id', 'required|numeric');
        $this->form_validation->set_rules('kelurahan_id', 'kelurahan_id', 'required|numeric');
        $this->form_validation->set_rules('kabupaten', 'kabupaten', 'required|trim');
        
        $this->form_validation->set_rules('wpnama', 'wpnama', 'required|trim');
        $this->form_validation->set_rules('wpalamat', 'wpalamat', 'required|trim');
        $this->form_validation->set_rules('wpkelurahan', 'wpkelurahan', 'required|trim');
        $this->form_validation->set_rules('wpkecamatan', 'wpkecamatan', 'required|trim');
        $this->form_validation->set_rules('wpkabupaten', 'wpkabupaten', 'required|trim');
        
        /*
        $this->form_validation->set_rules('npwpd','npwpd','required|trim');
        $this->form_validation->set_rules('formno', 'formno', 'required|numeric');
		
        $this->form_validation->set_rules('kodepos','kodepos','required|trim');
        $this->form_validation->set_rules('telphone','telphone','required|trim');
        $this->form_validation->set_rules('wptelp','wptelp','required|trim');
        $this->form_validation->set_rules('reg_kelurahan_id','reg_kelurahan_id','required|numeric');
        $this->form_validation->set_rules('customer_status_id','customer_status_id','required|numeric');
        $this->form_validation->set_rules('kirimtgl','kirimtgl','required');
        $this->form_validation->set_rules('batastgl','batastgl','required');
        $this->form_validation->set_rules('penerimanm','penerimanm','required|trim');
        $this->form_validation->set_rules('penerimaalamat','penerimaalamat','required|trim');
        $this->form_validation->set_rules('penerimatgl','penerimatgl','required');
        $this->form_validation->set_rules('wpkodepos','wpkodepos','required|trim');
        $this->form_validation->set_rules('kembalitgl','kembalitgl','required');
        $this->form_validation->set_rules('kembalioleh','kembalioleh','required|trim');
        $this->form_validation->set_rules('kukuhno','kukuhno','required|trim');
        $this->form_validation->set_rules('kukuhtgl','kukuhtgl','required');
        $this->form_validation->set_rules('kukuhprinted','kukuhprinted','required|numeric');
        $this->form_validation->set_rules('kartuprinted','kartuprinted','required|numeric');
        $this->form_validation->set_rules('id','id','required|numeric');
        $this->form_validation->set_rules('tmt','tmt','required');
        $this->form_validation->set_rules('enabled','enabled','required|numeric');
        $this->form_validation->set_rules('created','created','required');
        $this->form_validation->set_rules('create_uid','create_uid','required|numeric');
        $this->form_validation->set_rules('updated','updated','required');
        $this->form_validation->set_rules('update_uid','update_uid','required|numeric');
        $this->form_validation->set_rules('kukuhnip','kukuhnip','required|numeric');
        $this->form_validation->set_rules('kembalinip','kembalinip','required|numeric');
        $this->form_validation->set_rules('catatnip','catatnip','required|numeric');
        $this->form_validation->set_rules('kukuh_jabat_id','kukuh_jabat_id','required|numeric');
        $this->form_validation->set_rules('petugas_jabat_id','petugas_jabat_id','required|numeric');
        $this->form_validation->set_rules('pencatat_jabat_id','pencatat_jabat_id','required|numeric');
        $this->form_validation->set_rules('pnama','pnama','required|trim');
        $this->form_validation->set_rules('palamat','palamat','required|trim');
        $this->form_validation->set_rules('pkelurahan','pkelurahan','required|trim');
        $this->form_validation->set_rules('pkecamatan','pkecamatan','required|trim');
        $this->form_validation->set_rules('pkabupaten','pkabupaten','required|trim');
        $this->form_validation->set_rules('ptelp','ptelp','required|trim');
        $this->form_validation->set_rules('pkodepos','pkodepos','required|trim');
        $this->form_validation->set_rules('parent','parent','required|numeric');
        
        $this->form_validation->set_rules('ijin1','ijin1','required|trim');
        $this->form_validation->set_rules('ijin1no','ijin1no','required|trim');
        $this->form_validation->set_rules('ijin1tgl','ijin1tgl','required');
        $this->form_validation->set_rules('ijin1tglakhir','ijin1tglakhir','required');
        $this->form_validation->set_rules('ijin2','ijin2','required|trim');
        $this->form_validation->set_rules('ijin2no','ijin2no','required|trim');
        $this->form_validation->set_rules('ijin2tgl','ijin2tgl','required');
        $this->form_validation->set_rules('ijin2tglakhir','ijin2tglakhir','required');
        $this->form_validation->set_rules('ijin3','ijin3','required|trim');
        $this->form_validation->set_rules('ijin3no','ijin3no','required|trim');
        $this->form_validation->set_rules('ijin3tgl','ijin3tgl','required');
        $this->form_validation->set_rules('ijin3tglakhir','ijin3tglakhir','required');
        $this->form_validation->set_rules('ijin4','ijin4','required|trim');
        $this->form_validation->set_rules('ijin4no','ijin4no','required|trim');
        $this->form_validation->set_rules('ijin4tgl','ijin4tgl','required');
        $this->form_validation->set_rules('ijin4tglakhir','ijin4tglakhir','required');
        */
    }
    
    private function fpost() {
        $data['rp']                 = $this->input->post('rp');
        $data['pb']                 = $this->input->post('pb');
        $data['formno']             = $this->input->post('formno');
        $data['reg_date']           = $this->input->post('reg_date');
        $data['reg_kelurahan_id']   = $this->input->post('reg_kelurahan_id');
        $data['nama']         = $this->input->post('nama');
        $data['alamat']             = $this->input->post('alamat');
        $data['kelurahan_id']       = $this->input->post('kelurahan_id');
        $data['kabupaten']          = $this->input->post('kabupaten');
        $data['telphone']           = $this->input->post('telphone');
        $data['kodepos']            = $this->input->post('kodepos');
        $data['ijin1']              = $this->input->post('ijin1');
        $data['ijin1no']            = $this->input->post('ijin1no');
        $data['ijin1tgl']           = $this->input->post('ijin1tgl');
        $data['ijin2']              = $this->input->post('ijin2');
        $data['ijin2no']            = $this->input->post('ijin2no');
        $data['ijin2tgl']           = $this->input->post('ijin2tgl');
        $data['ijin3']              = $this->input->post('ijin3');
        $data['ijin3no']            = $this->input->post('ijin3no');
        $data['ijin3tgl']           = $this->input->post('ijin3tgl');
        $data['ijin4']              = $this->input->post('ijin4');
        $data['ijin4no']            = $this->input->post('ijin4no');
        $data['ijin4tgl']           = $this->input->post('ijin4tgl');
        $data['customer_status_id'] = $this->input->post('customer_status_id');
        $data['kirimtgl']           = $this->input->post('kirimtgl');
        $data['batastgl']           = $this->input->post('batastgl');
        $data['penerimanm']         = $this->input->post('penerimanm');
        $data['penerimaalamat']     = $this->input->post('penerimaalamat');
        $data['penerimatgl']        = $this->input->post('penerimatgl');
        $data['wpnama']             = $this->input->post('wpnama');
        $data['wpalamat']           = $this->input->post('wpalamat');
        $data['wpkelurahan']        = $this->input->post('wpkelurahan');
        $data['wpkecamatan']        = $this->input->post('wpkecamatan');
        $data['wpkabupaten']        = $this->input->post('wpkabupaten');
        $data['wptelp']             = $this->input->post('wptelp');
        $data['wpkodepos']          = $this->input->post('wpkodepos');
        $data['kembalitgl']         = $this->input->post('kembalitgl');
        $data['kembalioleh']        = $this->input->post('kembalioleh');
        $data['kukuhno']            = $this->input->post('kukuhno');
        $data['kukuhtgl']           = $this->input->post('kukuhtgl');
        $data['kukuhprinted']       = $this->input->post('kukuhprinted');
        $data['kartuprinted']       = $this->input->post('kartuprinted');
        $data['id']                 = $this->input->post('id');
        $data['tmt']                = $this->input->post('tmt');
        $data['enabled']            = $this->input->post('enabled');
        $data['created']        = $this->input->post('created');
        $data['create_uid']         = $this->input->post('create_uid');
        $data['updated']         = $this->input->post('updated');
        $data['update_uid']          = $this->input->post('update_uid');
        $data['kukuhnip']           = $this->input->post('kukuhnip');
        $data['kembalinip']         = $this->input->post('kembalinip');
        $data['catatnip']           = $this->input->post('catatnip');
        $data['kukuh_jabat_id']     = $this->input->post('kukuh_jabat_id');
        $data['petugas_jabat_id']   = $this->input->post('petugas_jabat_id');
        $data['pencatat_jabat_id']  = $this->input->post('pencatat_jabat_id');
        $data['pnama']              = $this->input->post('pnama');
        $data['palamat']            = $this->input->post('palamat');
        $data['pkelurahan']         = $this->input->post('pkelurahan');
        $data['pkecamatan']         = $this->input->post('pkecamatan');
        $data['pkabupaten']         = $this->input->post('pkabupaten');
        $data['ptelp']              = $this->input->post('ptelp');
        $data['pkodepos']           = $this->input->post('pkodepos');
        $data['parent']             = $this->input->post('parent');
        $data['npwpd']              = $this->input->post('npwpd');
        $data['kecamatan_id']       = $this->input->post('kecamatan_id');
        $data['ijin1tglakhir']      = $this->input->post('ijin1tglakhir');
        $data['ijin2tglakhir']      = $this->input->post('ijin2tglakhir');
        $data['ijin3tglakhir']      = $this->input->post('ijin3tglakhir');
        $data['ijin4tglakhir']      = $this->input->post('ijin4tglakhir');
        
		//tambahan
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
        $data['kd_waletvolume'] = pad_to_decimal($this->input->post('kd_waletvolume'));
        
        return $data;
    }
    
    public function add() {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('subjek_pajak'));
        }
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('subjek_pajak/add');
        $data['faction2'] = active_module_url('subjek_pajak/add_op');
        $data['dt']      = $this->fpost();
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $this->fpost();
			
			$gen_data = $this->subjek_pajak_model->generate_npwpd($input_post['rp'], $input_post['pb'], $input_post['kecamatan_id'], $input_post['kelurahan_id']);
			$formno = $gen_data['formno'];
			$npwpd  = $gen_data['npwpd'];
			
            $post_data  = array(
				'npwpd' => $npwpd,
                'formno' => $formno,
                'rp' => $input_post['rp'],
                'pb' => $input_post['pb'],
                'reg_date' => date('Y-m-d', strtotime($input_post['reg_date'])),
                'nama' => $input_post['nama'],
                'alamat' => $input_post['alamat'],
                'kecamatan_id' => $input_post['kecamatan_id'],
                'kelurahan_id' => $input_post['kelurahan_id'],
                'kabupaten' => $input_post['kabupaten'],
                'telphone' => $input_post['telphone'],
                'kodepos' => $input_post['kodepos'],
                
                'wpnama' => $input_post['wpnama'],
                'wpalamat' => $input_post['wpalamat'],
                'wpkelurahan' => $input_post['wpkelurahan'],
                'wpkecamatan' => $input_post['wpkecamatan'],
                'wpkabupaten' => $input_post['wpkabupaten'],
                'wptelp' => $input_post['wptelp'],
                'wpkodepos' => $input_post['wpkodepos'],
                
                'pnama' => $input_post['pnama'],
                'palamat' => $input_post['palamat'],
                'pkelurahan' => $input_post['pkelurahan'],
                'pkecamatan' => $input_post['pkecamatan'],
                'pkabupaten' => $input_post['pkabupaten'],
                'ptelp' => $input_post['ptelp'],
                'pkodepos' => $input_post['pkodepos'],
                
                'kukuhno' => $input_post['kukuhno'],
                'kukuhtgl' => empty($input_post['kukuhtgl']) ? NULL : date('Y-m-d', strtotime($input_post['kukuhtgl'])),
                'kukuh_jabat_id' => $input_post['kukuh_jabat_id'] == 999 ? NULL : $input_post['kukuh_jabat_id'],
                'kukuhnip' => $input_post['kukuhnip'],
                'tmt' => date('Y-m-d', strtotime($input_post['tmt'])),
                'enabled' => $input_post['enabled'],
                
                'ijin1' => $input_post['ijin1'],
                'ijin1no' => $input_post['ijin1no'],
                'ijin1tgl' => $input_post['ijin1tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin1tgl'])),
                'ijin1tglakhir' => $input_post['ijin1tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin1tglakhir'])),
                'ijin2' => $input_post['ijin2'],
                'ijin2no' => $input_post['ijin2no'],
                'ijin2tgl' => $input_post['ijin2tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin2tgl'])),
                'ijin2tglakhir' => $input_post['ijin2tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin2tglakhir'])),
                'ijin3' => $input_post['ijin3'],
                'ijin3no' => $input_post['ijin3no'],
                'ijin3tgl' => $input_post['ijin3tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin3tgl'])),
                'ijin3tglakhir' => $input_post['ijin3tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin3tglakhir'])),
                'ijin4' => $input_post['ijin4'],
                'ijin4no' => $input_post['ijin4no'],
                'ijin4tgl' => $input_post['ijin4tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin4tgl'])),
                'ijin4tglakhir' => $input_post['ijin4tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin4tglakhir'])),
                
                'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
                
                // data tambahan
                'kd_restojmlmeja' => $input_post['kd_restojmlmeja'],
                'kd_restojmlkursi' => $input_post['kd_restojmlkursi'],
                'kd_restojmltamu' => $input_post['kd_restojmltamu'],
                'kd_filmkursi' => $input_post['kd_filmkursi'],
                'kd_filmpertunjukan' => $input_post['kd_filmpertunjukan'],
                'kd_filmtarif' => $input_post['kd_filmtarif'],
                'kd_bilyarmeja' => $input_post['kd_bilyarmeja'],
                'kd_bilyartarif' => $input_post['kd_bilyartarif'],
                'kd_bilyarkegiatan' => $input_post['kd_bilyarkegiatan'],
                'kd_diskopengunjung' => $input_post['kd_diskopengunjung'],
                'kd_diskotarif' => $input_post['kd_diskotarif'],
                'kd_waletvolume' => $input_post['kd_waletvolume'],
                
                /*
                'id' => $input_post['id'],
                'rp' => $input_post['rp'],
                'npwpd' => $input_post['npwpd'],
                'parent' => $input_post['parent'],
                'customer_status_id' => $input_post['customer_status_id'],
                'petugas_jabat_id' => $input_post['petugas_jabat_id'],
                'pencatat_jabat_id' => $input_post['pencatat_jabat_id'],
                'kembalinip' => $input_post['kembalinip'],
                'catatnip' => $input_post['catatnip'],
                'created' => date('Y-m-d', strtotime($input_post['created'])),
                'create_uid' => $input_post['create_uid'],
                'kukuhprinted' => $input_post['kukuhprinted'],
                'kartuprinted' => $input_post['kartuprinted'],
                'reg_kelurahan_id' => $input_post['reg_kelurahan_id'],
                'kirimtgl' => date('Y-m-d', strtotime($input_post['kirimtgl'])),
                'batastgl' => date('Y-m-d', strtotime($input_post['batastgl'])),
                'penerimanm' => $input_post['penerimanm'],
                'penerimaalamat' => $input_post['penerimaalamat'],
                'penerimatgl' => date('Y-m-d', strtotime($input_post['penerimatgl'])),
                'kembalitgl' => date('Y-m-d', strtotime($input_post['kembalitgl'])),
                'kembalioleh' => $input_post['kembalioleh'],
                */
            );
            
			if( $new_id = $this->subjek_pajak_model->save($post_data)) {
            
                // data tambahan / detail
				$dtKD = $this->input->post('dtKD');
				$tambahan_data2 = array();

                if(isset($dtKD)) {
                    $i = 1;
                    $dtKD = json_decode($dtKD, true);
                    
                    if(count($dtKD['dtKD']) > 0){
                        $cid = $new_id;
                        $rd_row = array();
                        foreach($dtKD['dtKD'] as $rows) {
                            $rd_row = array (
                                'customer_id' => $cid,
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
                        $this->db->delete('pad_customer_detail', array('customer_id' => $cid)); 
                        $this->db->insert_batch('pad_customer_detail', $tambahan_data2);
                    }
                }
                
                // uplod dokeumen
                // $this->unggah($cid);
                // -- END data tambahan / detail
                
				$this->session->set_flashdata('msg_success', 'Data telah disimpan, silahkan lengkapi data Objek Pajak.');
				//redirect(active_module_url('subjek_pajak'));
				redirect(active_module_url('subjek_pajak/edit/'.$new_id));
			}
            
        }
		
		$data['dt']['rp'] = 'P';
		$data['dt']['reg_date'] = date('d-m-Y');
		
		$options = array(
			'1' => 'PRIBADI',
			'2' => 'BADAN'
		);
		$js                = 'id="pb" class="input-medium" required ';
		$data['select_pb'] = form_dropdown('pb', $options, '', $js);
		
		$select_data  = $this->load->model('kecamatan_model')->get_select();
		$options      = array();
		$kec_id = '';
		if($select_data) {
		foreach ($select_data as $row) {
			if($kec_id == '') $kec_id = $row->id;
			$options[$row->id] = $row->nama;
		}}
		$js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" required ';
		$data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, '', $js);

		$select_data = $this->load->model('kelurahan_model')->get_select($kec_id);
		$options     = array();
		if($select_data) {
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}}
		$js                       = 'id="kelurahan_id" class="input-large" required ';
		$data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, '', $js);

		//op
		$options = array(
			'1' => 'AKTIF',
			'2' => 'TUTUP',
			'3' => 'TUTUP SEMENTARA',
		);
		$js = 'id="pb" class="input-medium" required ';
		$data['select_op_status'] = form_dropdown('op_status', $options, '', $js);
		
		$select_data = $this->load->model('pegawai_model')->get_select();
		$options     = array();
		if($select_data) {
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}}
		$options[999] = 'TIDAK ADA';
		$js                       = 'id="kukuh_jabat_id" class="input-large" ';
		$data['select_kukuh_jabat'] = form_dropdown('kukuh_jabat_id', $options, 999, $js);

        $this->load->view('vsubjek_pajak_form', $data);
    }
    
    public function edit() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('subjek_pajak'));
        }
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('subjek_pajak/update');
        
        $id = $this->uri->segment(4);
        if ($id && $get = $this->subjek_pajak_model->get($id)) {
            $data['dt']['rp']                 = $get->rp;
            $data['dt']['pb']                 = $get->pb;
            $data['dt']['formno']             = $get->formno;
            $data['dt']['reg_date']           = date('d-m-Y', strtotime($get->reg_date));
            // $data['dt']['reg_kelurahan_id']   = $get->reg_kelurahan_id;
            $data['dt']['nama']         = $get->nama;
            $data['dt']['alamat']             = $get->alamat;
            $data['dt']['kelurahan_id']       = $get->kelurahan_id;
            $data['dt']['kabupaten']          = $get->kabupaten;
            $data['dt']['telphone']           = $get->telphone;
            $data['dt']['kodepos']            = $get->kodepos;
            $data['dt']['ijin1']              = $get->ijin1;
            $data['dt']['ijin1no']            = $get->ijin1no;
            $data['dt']['ijin1tgl']           = $get->ijin1tgl == null ? '' : date('d-m-Y', strtotime($get->ijin1tgl));
            $data['dt']['ijin2']              = $get->ijin2;
            $data['dt']['ijin2no']            = $get->ijin2no;
            $data['dt']['ijin2tgl']           = $get->ijin2tgl == null ? '' : date('d-m-Y', strtotime($get->ijin2tgl));
            $data['dt']['ijin3']              = $get->ijin3;
            $data['dt']['ijin3no']            = $get->ijin3no;
            $data['dt']['ijin3tgl']           = $get->ijin3tgl == null ? '' : date('d-m-Y', strtotime($get->ijin3tgl));
            $data['dt']['ijin4']              = $get->ijin4;
            $data['dt']['ijin4no']            = $get->ijin4no;
            $data['dt']['ijin4tgl']           = $get->ijin4tgl == null ? '' : date('d-m-Y', strtotime($get->ijin4tgl));
            $data['dt']['customer_status_id'] = $get->customer_status_id;
            $data['dt']['kirimtgl']           = date('d-m-Y', strtotime($get->kirimtgl));
            $data['dt']['batastgl']           = date('d-m-Y', strtotime($get->batastgl));
            $data['dt']['penerimanm']         = $get->penerimanm;
            $data['dt']['penerimaalamat']     = $get->penerimaalamat;
            $data['dt']['penerimatgl']        = date('d-m-Y', strtotime($get->penerimatgl));
            $data['dt']['wpnama']             = $get->wpnama;
            $data['dt']['wpalamat']           = $get->wpalamat;
            $data['dt']['wpkelurahan']        = $get->wpkelurahan;
            $data['dt']['wpkecamatan']        = $get->wpkecamatan;
            $data['dt']['wpkabupaten']        = $get->wpkabupaten;
            $data['dt']['wptelp']             = $get->wptelp;
            $data['dt']['wpkodepos']          = $get->wpkodepos;
            $data['dt']['kembalitgl']         = date('d-m-Y', strtotime($get->kembalitgl));
            $data['dt']['kembalioleh']        = $get->kembalioleh;
            $data['dt']['kukuhno']            = $get->kukuhno;
            $data['dt']['kukuhtgl']           = empty($get->kukuhtgl) ? NULL : date('d-m-Y', strtotime($get->kukuhtgl));
            $data['dt']['kukuhprinted']       = $get->kukuhprinted;
            $data['dt']['kartuprinted']       = $get->kartuprinted;
            $data['dt']['id']                 = $get->id;
            $data['dt']['tmt']                = date('d-m-Y', strtotime($get->tmt));
            $data['dt']['enabled']            = $get->enabled;
            $data['dt']['created']        = date('d-m-Y', strtotime($get->created));
            $data['dt']['create_uid']         = $get->create_uid;
            $data['dt']['updated']         = date('d-m-Y', strtotime($get->updated));
            $data['dt']['update_uid']          = $get->update_uid;
            $data['dt']['kukuhnip']           = $get->kukuhnip;
            $data['dt']['kembalinip']         = $get->kembalinip;
            $data['dt']['catatnip']           = $get->catatnip;
            $data['dt']['kukuh_jabat_id']     = $get->kukuh_jabat_id;
            $data['dt']['petugas_jabat_id']   = $get->petugas_jabat_id;
            $data['dt']['pencatat_jabat_id']  = $get->pencatat_jabat_id;
            $data['dt']['pnama']              = $get->pnama;
            $data['dt']['palamat']            = $get->palamat;
            $data['dt']['pkelurahan']         = $get->pkelurahan;
            $data['dt']['pkecamatan']         = $get->pkecamatan;
            $data['dt']['pkabupaten']         = $get->pkabupaten;
            $data['dt']['ptelp']              = $get->ptelp;
            $data['dt']['pkodepos']           = $get->pkodepos;
            $data['dt']['parent']             = $get->parent;
			
            //$data['dt']['npwpd']              = pad_format_npwpd($get->npwpd);
            $data['dt']['npwpd']              = $this->subjek_pajak_model->get_npwpd($get->id);
            
			$data['dt']['kecamatan_id']       = $get->kecamatan_id;
            $data['dt']['ijin1tglakhir']      = $get->ijin1tglakhir == null ? '' : date('d-m-Y', strtotime($get->ijin1tglakhir));
            $data['dt']['ijin2tglakhir']      = $get->ijin2tglakhir == null ? '' : date('d-m-Y', strtotime($get->ijin2tglakhir));
            $data['dt']['ijin3tglakhir']      = $get->ijin3tglakhir == null ? '' : date('d-m-Y', strtotime($get->ijin3tglakhir));
            $data['dt']['ijin4tglakhir']      = $get->ijin4tglakhir == null ? '' : date('d-m-Y', strtotime($get->ijin4tglakhir));
            		
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
            $data['dt']['kd_waletvolume'] = $get->kd_waletvolume;

  
            $options           = array(
                '1' => 'PRIBADI',
                '2' => 'BADAN'
            );
            $js                = 'id="pb" class="input-medium" required ';
            $data['select_pb'] = form_dropdown('pb', $options, $get->pb, $js);
            
            $select_data = $this->load->model('kecamatan_model')->get_select();
            $options     = array();
            if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}}
            $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" required ';
            $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, $get->kecamatan_id, $js);
            
            $select_data = $this->load->model('kelurahan_model')->get_select($get->kecamatan_id);
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                       = 'id="kelurahan_id" class="input-large" required ';
            $data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, $get->kelurahan_id, $js);
				
			$select_data = $this->load->model('pegawai_model')->get_select();
			$options     = array();
            if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}}
			$options[999] = 'TIDAK ADA';
			$js                       = 'id="kukuh_jabat_id" class="input-large" ';
			$data['select_kukuh_jabat'] = form_dropdown('kukuh_jabat_id', $options, $get->kukuh_jabat_id, $js);

            //
            $select_data = $this->load->model('usaha_model')->get_select();
			$options = array();
            $options[] = "# PILIH #";
            $op_usaha_id = '';
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
			$js = 'id="op_usaha_id" class="input" required onChange="get_op_pajak(this.value);"';
			$data['select_op_usaha'] = form_dropdown('op_usaha_id', $options, '', $js);
            //
            $options = array();
            $options[] = "# AUTO #";
			$js = 'id="op_pajak_id" class="input" required ';
			$data['select_op_pajak'] = form_dropdown('op_pajak_id', $options, '', $js);
			

            $select_data  = $this->load->model('kecamatan_model')->get_select();
            $options      = array();
            $kec_id = '';
            if($select_data) {
            foreach ($select_data as $row) {
                if($kec_id == '') $kec_id = $row->id;
                $options[$row->id] = $row->nama;
            }}
            $js                       = 'id="op_kecamatan_id" class="input-medium" onChange="get_op_kelurahan(this.value);" required ';
            $data['select_op_kecamatan'] = form_dropdown('op_kecamatan_id', $options, '', $js);

            $select_data = $this->load->model('kelurahan_model')->get_select($kec_id);
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                       = 'id="op_kelurahan_id" class="input-large" required ';
            $data['select_op_kelurahan'] = form_dropdown('op_kelurahan_id', $options, '', $js);

            $options = array(
            999 => 'SEMUA STATUS',
            '1' => 'AKTIF',
            '2' => 'TUTUP',
            '3' => 'TUTUP SEMENTARA',
            );
            $js = 'id="op_status" class="input" required ';
            $select = form_dropdown('customer_status_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_op_status'] = form_dropdown('op_status', $options, '', $js);
				
            $this->load->view('vsubjek_pajak_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('subjek_pajak'));
        }
        $data['current'] = 'pendaftaran';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('subjek_pajak/update');
        
        $post_data  = $this->fpost();
        $data['dt'] = $post_data;
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            
			$gen_data = $this->subjek_pajak_model->generate_npwpd($input_post['rp'], $input_post['pb'], $input_post['kecamatan_id'], $input_post['kelurahan_id'], $input_post['formno']);
			$npwpd  = $gen_data['npwpd'];
			
            $post_data  = array(
				'npwpd' => $npwpd,
                
                'rp' => $input_post['rp'],
                'pb' => $input_post['pb'],
                'formno' => $input_post['formno'],
                'reg_date' => date('Y-m-d', strtotime($input_post['reg_date'])),
                'nama' => $input_post['nama'],
                'alamat' => $input_post['alamat'],
                'kecamatan_id' => $input_post['kecamatan_id'],
                'kelurahan_id' => $input_post['kelurahan_id'],
                'kabupaten' => $input_post['kabupaten'],
                'telphone' => $input_post['telphone'],
                'kodepos' => $input_post['kodepos'],
                
                'wpnama' => $input_post['wpnama'],
                'wpalamat' => $input_post['wpalamat'],
                'wpkelurahan' => $input_post['wpkelurahan'],
                'wpkecamatan' => $input_post['wpkecamatan'],
                'wpkabupaten' => $input_post['wpkabupaten'],
                'wptelp' => $input_post['wptelp'],
                'wpkodepos' => $input_post['wpkodepos'],
                
                'pnama' => $input_post['pnama'],
                'palamat' => $input_post['palamat'],
                'pkelurahan' => $input_post['pkelurahan'],
                'pkecamatan' => $input_post['pkecamatan'],
                'pkabupaten' => $input_post['pkabupaten'],
                'ptelp' => $input_post['ptelp'],
                'pkodepos' => $input_post['pkodepos'],
                
                'kukuhno' => $input_post['kukuhno'],
                'kukuhtgl' => empty($input_post->kukuhtgl) ? NULL : date('Y-m-d', strtotime($input_post['kukuhtgl'])),
                'kukuh_jabat_id' => $input_post['kukuh_jabat_id'],
                'kukuhnip' => $input_post['kukuhnip'],
                'tmt' => date('Y-m-d', strtotime($input_post['tmt'])),
                'enabled' => $input_post['enabled'],
                
                'ijin1' => $input_post['ijin1'],
                'ijin1no' => $input_post['ijin1no'],
                'ijin1tgl' => $input_post['ijin1tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin1tgl'])),
                'ijin1tglakhir' => $input_post['ijin1tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin1tglakhir'])),
                'ijin2' => $input_post['ijin2'],
                'ijin2no' => $input_post['ijin2no'],
                'ijin2tgl' => $input_post['ijin2tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin2tgl'])),
                'ijin2tglakhir' => $input_post['ijin2tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin2tglakhir'])),
                'ijin3' => $input_post['ijin3'],
                'ijin3no' => $input_post['ijin3no'],
                'ijin3tgl' => $input_post['ijin3tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin3tgl'])),
                'ijin3tglakhir' => $input_post['ijin3tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin3tglakhir'])),
                'ijin4' => $input_post['ijin4'],
                'ijin4no' => $input_post['ijin4no'],
                'ijin4tgl' => $input_post['ijin4tgl'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin4tgl'])),
                'ijin4tglakhir' => $input_post['ijin4tglakhir'] == '' ? NULL : date('Y-m-d', strtotime($input_post['ijin4tglakhir'])),
                
                'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),
                
                // data tambahan
                'kd_restojmlmeja' => $input_post['kd_restojmlmeja'],
                'kd_restojmlkursi' => $input_post['kd_restojmlkursi'],
                'kd_restojmltamu' => $input_post['kd_restojmltamu'],
                'kd_filmkursi' => $input_post['kd_filmkursi'],
                'kd_filmpertunjukan' => $input_post['kd_filmpertunjukan'],
                'kd_filmtarif' => $input_post['kd_filmtarif'],
                'kd_bilyarmeja' => $input_post['kd_bilyarmeja'],
                'kd_bilyartarif' => $input_post['kd_bilyartarif'],
                'kd_bilyarkegiatan' => $input_post['kd_bilyarkegiatan'],
                'kd_diskopengunjung' => $input_post['kd_diskopengunjung'],
                'kd_diskotarif' => $input_post['kd_diskotarif'],
                'kd_waletvolume' => $input_post['kd_waletvolume'],
                
                /*
                'id' => $input_post['id'],
                'rp' => $input_post['rp'],
                'npwpd' => $input_post['npwpd'],
                'parent' => $input_post['parent'],
                'customer_status_id' => $input_post['customer_status_id'],
                'petugas_jabat_id' => $input_post['petugas_jabat_id'],
                'pencatat_jabat_id' => $input_post['pencatat_jabat_id'],
                'kembalinip' => $input_post['kembalinip'],
                'catatnip' => $input_post['catatnip'],
                'created' => date('Y-m-d', strtotime($input_post['created'])),
                'create_uid' => $input_post['create_uid'],
                'kukuhprinted' => $input_post['kukuhprinted'],
                'kartuprinted' => $input_post['kartuprinted'],
                'reg_kelurahan_id' => $input_post['reg_kelurahan_id'],
                'kirimtgl' => date('Y-m-d', strtotime($input_post['kirimtgl'])),
                'batastgl' => date('Y-m-d', strtotime($input_post['batastgl'])),
                'penerimanm' => $input_post['penerimanm'],
                'penerimaalamat' => $input_post['penerimaalamat'],
                'penerimatgl' => date('Y-m-d', strtotime($input_post['penerimatgl'])),
                'kembalitgl' => date('Y-m-d', strtotime($input_post['kembalitgl'])),
                'kembalioleh' => $input_post['kembalioleh'],
                */
            );
            $this->subjek_pajak_model->update($this->input->post('id'), $post_data);
            
            // data tambahan / detail
            $dtKD = $this->input->post('dtKD');
            $tambahan_data2 = array();

            if(isset($dtKD)) {
                $i = 1;
                $dtKD = json_decode($dtKD, true);
                
                if(count($dtKD['dtKD']) > 0){
                    $cid = $this->input->post('id');
                    $rd_row = array();
                    foreach($dtKD['dtKD'] as $rows) {
                        $rd_row = array (
                            'customer_id' => $cid,
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
                    $this->db->delete('pad_customer_detail', array('customer_id' => $cid)); 
                    $this->db->insert_batch('pad_customer_detail', $tambahan_data2);
                }
            }
            
            // uplod dokeumen
            // $this->unggah($cid);
            // -- END data tambahan / detail
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('subjek_pajak'));
        }
        
        $get               = (object) $post_data;
        $options           = array(
            '1' => 'PRIBADI',
            '2' => 'BADAN'
        );
        $js                = 'id="pb" class="input-medium" required ';
        $data['select_pb'] = form_dropdown('pb', $options, $get->pb, $js);
        
        $select_data = $this->load->model('kecamatan_model')->get_select();
        $options     = array();
		if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }}
        $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" required ';
        $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, $get->kecamatan_id, $js);
        
        $select_data = $this->load->model('kelurahan_model')->get_select($get->kecamatan_id);
        $options     = array();
		if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }}
        $js                       = 'id="kelurahan_id" class="input-large" required ';
        $data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, $get->kelurahan_id, $js);
        
		$select_data = $this->load->model('pegawai_model')->get_select();
		$options     = array();
		if($select_data) {
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}}
		$options[999] = 'TIDAK ADA';
		$js                       = 'id="kukuh_jabat_id" class="input-large" ';
		$data['select_kukuh_jabat'] = form_dropdown('kukuh_jabat_id', $options, $get->kukuh_jabat_id, $js);

        $this->load->view('vsubjek_pajak_form', $data);
    }
    
    public function delete() {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('subjek_pajak'));
        }
        
        $id = $this->uri->segment(4);
        if ($id && $this->subjek_pajak_model->get($id)) {
            $this->subjek_pajak_model->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('subjek_pajak'));
        } else {
            show_404();
        }
    }
    	
    function get_typeahead_npwpd() {
        $xnpwpd = $this->uri->segment(4);
        $data = $this->load->model('subjek_pajak_model')->get_typeahead_npwpd($xnpwpd);
        echo json_encode($data);
    }
	
    function get_typeahead_csname() {
        $xname = urldecode($this->uri->segment(4));
        $data = $this->load->model('subjek_pajak_model')->get_typeahead_csname($xname);
        echo json_encode($data);
    }
	
    function get_kelurahan() {
        $kec_id    = $this->uri->segment(4);
        $kelurahan = $this->load->model('kelurahan_model')->get_select($kec_id);
        echo json_encode($kelurahan);
    }
	
    function get_op_pajak() {
        $usaha_id    = $this->uri->segment(4);
        $select_data = $this->load->model('pajak_model')->get_select2($usaha_id);
        echo json_encode($select_data);
    }
    
    function get_op() {
        $op_id    = $this->uri->segment(4);
        $op_data = $this->load->model('objek_pajak_model')->get($op_id);
		
		$op_data->tmt = date('d-m-Y', strtotime($op_data->tmt));
		$op_data->nopd = $this->load->model('objek_pajak_model')->get_nopd($op_id);
		
        $select_data = $this->load->model('usaha_model')->get_select();
        $usaha = '';
		if($select_data) {
        foreach ($select_data as $row) {
			if ($row->id == $op_data->usaha_id)
				$usaha .= "<option value={$row->id} selected >{$row->nama}</option>";
			else
				$usaha .= "<option value={$row->id}>{$row->nama}</option>";
        }}
        
        //--
        $select_data = $this->load->model('pajak_model')->get_select2($op_data->usaha_id);
        $pajak = '';
		if($select_data) {
        foreach ($select_data as $row) {
			if ($row->id == $op_data->def_pajak_id)
				$pajak .= "<option value={$row->id} selected >{$row->nama}</option>";
			else
				$pajak .= "<option value={$row->id}>{$row->nama}</option>";
        }}
        
        $select_data = $this->load->model('kecamatan_model')->get_select();
        $kecamatan = '';
		if($select_data) {
        foreach ($select_data as $row) {
			if ($row->id == $op_data->kecamatan_id)
				$kecamatan .= "<option value={$row->id} selected >{$row->nama}</option>";
			else
				$kecamatan .= "<option value={$row->id}>{$row->nama}</option>";
        }}
        
        $select_data = $this->load->model('kelurahan_model')->get_select($op_data->kecamatan_id);
        $kelurahan = '';
		if($select_data) {
        foreach ($select_data as $row) {
			if ($row->id == $op_data->kelurahan_id)
				$kelurahan .= "<option value={$row->id} selected >{$row->nama}</option>";
			else
				$kelurahan .= "<option value={$row->id}>{$row->nama}</option>";
        }}

		//wkkk kode ngarang, baka
        $select_data =  (object) array((object) array('id'=>1, 'status'=>'AKTIF'), (object) array('id'=>2, 'status'=>'TUTUP'), (object) array('id'=>3, 'status'=>'TUTUP SEMENTARA'));
        $status = '';
		if($select_data) {
        foreach ($select_data as $row) {
			if ($row->id == $op_data->enabled)
				$status .= "<option value={$row->id} selected >{$row->status}</option>";
			else
				$status .= "<option value={$row->id}>{$row->status}</option>";
        }}
		
		$op_data->usaha     = $usaha;
		$op_data->pajak     = $pajak;
		$op_data->status    = $status;
		$op_data->kecamatan = $kecamatan;
		$op_data->kelurahan = $kelurahan;
		
        echo json_encode($op_data);
    }
	
    function new_op() {
		$op_data->tmt = date('d-m-Y h:i:s');
        $select_data = $this->load->model('usaha_model')->get_select();
        $usaha = '';
        $usaha_id = '';
		if($select_data) {
        foreach ($select_data as $row) {
            $usaha_id == '' ? $usaha_id = $row->id : $usaha_id;
			$usaha .= "<option value={$row->id}> {$row->nama} </option>";
        }}
        
        $select_data = $this->load->model('pajak_model')->get_select2($usaha_id);
        $pajak = '';
		if($select_data) {
        foreach ($select_data as $row) {
			$pajak .= "<option value=1>Semprul </option>";
        }}
        
        $select_data = $this->load->model('kecamatan_model')->get_select();
        $kecamatan = '';
		if($select_data) {
        foreach ($select_data as $row) {
			$kec_id = empty($kec_id) ? $row->id : $kec_id;
			$kecamatan .= "<option value={$row->id}>{$row->nama}</option>";
        }}
        
        $select_data = $this->load->model('kelurahan_model')->get_select($kec_id);
        $kelurahan = '';
		if($select_data) {
        foreach ($select_data as $row) {
			$kelurahan .= "<option value={$row->id}>{$row->nama}</option>";
        }}

		//wkkk kode ngarang, baka
        $select_data =  (object) array((object) array('id'=>1, 'status'=>'AKTIF'), (object) array('id'=>2, 'status'=>'TUTUP'), (object) array('id'=>3, 'status'=>'TUTUP SEMENTARA'));
        $status = '';
		if($select_data) {
        foreach ($select_data as $row) {
			$status .= "<option value={$row->id}>{$row->status}</option>";
        }}
		
		$op_data->usaha     = $usaha;
		$op_data->pajak     = $pajak;
		$op_data->status    = $status;
		$op_data->kecamatan = $kecamatan;
		$op_data->kelurahan = $kelurahan;
		
        echo json_encode($op_data);
    }
	
	function proces_op() {
        $mode = $this->uri->segment(4);					
		$op_id = $this->input->get_post('op_id');
		
		$usaha_model = $this->load->model('usaha_model');
		$so = $usaha_model->get_so($this->input->get_post('op_usaha_id'));
		
		$update_data = array(
			'customer_id' => $this->input->get_post('customer_id'),
			'usaha_id' => $this->input->get_post('op_usaha_id'),
			'so' => $so,
			'kecamatan_id' => $this->input->get_post('op_kecamatan_id'),
			'kelurahan_id' => $this->input->get_post('op_kelurahan_id'),
			'notes' => $this->input->get_post('op_keterangan'),
			'customer_status_id' => $this->input->get_post('op_status'),
			'enabled' => 1,
			'tmt' => $this->input->get_post('op_tmt') == '' ? NULL : date('Y-m-d', strtotime($this->input->get_post('op_tmt'))),
            
			'opnm' => $this->input->get_post('op_nama'),
			'opalamat' => $this->input->get_post('op_alamat'),
			'latitude' => pad_to_decimal($this->input->get_post('op_latitude')),
			'longitude' => pad_to_decimal($this->input->get_post('op_longitude')),
			'def_pajak_id' => $this->input->get_post('op_pajak_id'),
		);
		
		$op_model = $this->load->model('objek_pajak_model');
		if ($mode == 'add') {
			$konter = $op_model->max_konter($this->input->get_post('customer_id'));
			
			$new_data = array(
				'konterid' => $konter,
				'reg_date' => date('Y-m-d'),
				'created' => date('Y-m-d'),
                'create_uid' => sipkd_user_id(),
			);
			$update_data = array_merge($update_data, $new_data);
			$op_model->save($update_data);
		}
		
		if ($mode == 'edit') {
			$new_data = array(
				'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),
			);
			$update_data = array_merge($update_data, $new_data);
			$op_model->update($op_id, $update_data);
		}	
	}
	
    public function delete_op() {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('subjek_pajak'));
        }
        
        $id = $this->uri->segment(4);
        $model = $this->load->model('objek_pajak_model');
        if ($id && $model->get($id)) {
            $model->delete($id);
			echo "sip";
        } else {
            show_404();
        }
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
					
		$rpt = $this->input->get('rpt');
		switch ($rpt) {
			case 'wpkartu':
			case 'wpkartu_backside':
				$params = array(
					'id' => $this->input->get('id'),
                    'parametercode' => $this->input->get('npwpd'),
                    'img' => base_url('assets/img/kartuwp.jpg'),
                    'ttd' => base_url('assets/img/ttd_cap.gif'),
                    "daerah" => pad_pemda_daerah(),
                    "dinas" => pad_pemda_nama(),
				);
				break;
				
			case 'wpkukuh':					
				$params = array(
					'id' => $this->input->get('id'),
                    "daerah" => pad_pemda_daerah(),
                    "dinas" => pad_pemda_nama(),
				);
				break;
		}
        
		$rpt = $rpt;
		
		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, false);
	}
}
