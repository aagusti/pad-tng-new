<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class sptpd extends CI_Controller {
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
            'apps_model', 'sptpd_model', 'skpd_model', 'invoice_model',
            'reklame_nilai_strategis_model', 'customer_usaha_air_tanah_model',
        ));

		$this->load->helper(active_module()); // cm kasih prefix namanya doang :D, ga pake _helper (pad_helper)
    }

	function load_auth() {
        $this->load->library('module_auth', array('module' => $this->module));
    }

    public function index($id)
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
            
            $options = array(
                '1' => 'BELUM PROSES',
                '2' => 'SUDAH PROSES',
            );
            $js = 'id="proses_id" class="input-medium"';
            $select = form_dropdown('proses_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);
            $data['select_proses_id'] = $select;
            if($id != 'default')
            $select_data = $this->load->model('usaha_model')->get_select_one($id);
            if ($id == 'default')
            $select_data = $this->load->model('usaha_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            if($id != 'default')
            $js = 'id="usaha_id" class="input-medium" readonly';

            if ($id == 'default')
            $js = 'id="usaha_id" class="input-medium"';
            if($id){
            $selected = $row->id; //selected id (filter langsung)
            $select = form_dropdown('usaha_id', $options, $selected, $js);
            if($id != 'default'){
            $this->session->unset_userdata('usaha_nama');
            $this->session->set_userdata('usaha_nama', $options);
            }
            if ($id == 'default')
            {
            $array = array('SEMUA USAHA');
            $this->session->unset_userdata('usaha_nama');
            $this->session->set_userdata('usaha_nama', $array);
            }
            $data['add_id'] = $id;
            }
            else{
            $options[999] = 'SEMUA PAJAK';
            $select = form_dropdown('usaha_id', $options, 999, $js);
            }
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_usaha'] = $select;


            $options     = array();
            $options[999] = 'SEMUA';
            $options[0] = 'BELUM BAYAR';
            $options[1] = 'SUDAH BAYAR';
            $js                   = 'id="bayar_id" class="form-group"';
            $select = form_dropdown('bayar_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_bayar'] = $select;

            $options     = array();
            $options[999] = 'SEMUA';
            $options[1] = 'BELUM VALIDASI';
            $options[2] = 'SUDAH VALIDASI';
            $js                   = 'id="validasi_id" class="form-group"';
            $select = form_dropdown('validasi_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_validasi'] = $select;

            $select_data = $this->load->model('pegawai_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $options[999] = 'TIDAK ADA';
            $js                       = 'id="petugas_id" class="input-large" style="width:180px"';
            $data['select_petugas'] = form_dropdown('petugas_id', $options, 999, $js);

            $js                       = 'id="pejabat_id" class="input-large" style="width:180px"';
            $data['select_pejabat'] = form_dropdown('pejabat_id', $options, 999, $js);
            
            $this->session->set_userdata('skpd_tipe', '');
			$this->load->view(active_module().'/vsptpd', $data);
		    } 

            else 

            {
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

            $options     = array();
            $options[0] = 'BELUM BAYAR';
            $options[1] = 'SUDAH BAYAR';
            $js                   = 'id="bayar_id" class="form-group"';
            $select = form_dropdown('bayar_id', $options, 0, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_bayar'] = $select;

            $options     = array();
            $options[1] = 'BELUM VALIDASI';
            $options[2] = 'SUDAH VALIDASI';
            $js                   = 'id="validasi_id" class="form-group"';
            $select = form_dropdown('validasi_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_validasi'] = $select;

            $this->session->set_userdata('skpd_tipe', '');
			$this->load->view(active_module().'/wp/vsptpd', $data);
		}
    }

    function grid()
    {
        $type_id   = $this->uri->segment(4); // on sptpd
        $proses_id = $this->uri->segment(4); // on skpd
        $usaha_id  = $this->uri->segment(5);
        $terimatgl = $this->uri->segment(6);
        $terimatgl2 = $this->uri->segment(7);
        $status_pembayaran = $this->uri->segment(8);
        $status_validasi= $this->uri->segment(9);


        $terimatgl = empty($terimatgl) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl));
        $terimatgl2 = empty($terimatgl2) ? date('Y-m-d') : date('Y-m-d', strtotime($terimatgl2));
        
        $this->load->library('Datatables');

		if (!wp_login()) {
			if ($this->controller == 'sptpd') {
                if($usaha_id == 4){
				$this->datatables->select('s.id, s.ijin_no, s.terimatgl, st.typenm,
					get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as customernm, 
                    p.nama, cu.opnm, (s.tarif*100) as tarif, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.pajak_terhutang as pajak,
					cu.usaha_id, s.type_id, cu.def_pajak_id, cu.id as cuid', false);
                }else{
                $this->datatables->select('s.id, get_sptno(s.id) as sptno, s.terimatgl, st.typenm,
                    get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as customernm, 
                    p.nama, cu.opnm, (s.tarif*100) as tarif, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.pajak_terhutang as pajak,
                    cu.usaha_id, s.type_id, cu.def_pajak_id, cu.id as cuid', false);
                }


				$this->datatables->from('pad_spt s');
				$this->datatables->join('pad_customer_usaha cu', 's.customer_usaha_id=cu.id', 'left');
				$this->datatables->join('pad_customer c', 'cu.customer_id=c.id', 'left');
				$this->datatables->join('pad_usaha u', 'cu.usaha_id=u.id', 'left');
				$this->datatables->join('pad_jenis_pajak p', 's.pajak_id=p.id', 'left');
				$this->datatables->join('pad_spt_type st', 'st.id=s.type_id', 'left');
                $this->datatables->join('pad_invoice inv', "s.id=inv.source_id and inv.source_nama='pad_spt'", 'left');

                if($type_id <> 999 && !empty($type_id))
                    $this->datatables->filter('s.type_id', $type_id);
                    
				if ($this->input->get('iSortCol_0') == 1) {
					$sort = $this->input->get('sSortDir_0');
					$this->datatables->order_by('s.sptno', $sort);
				}
                
                if($this->input->get('sSearch') == '')
                    //$this->datatables->filter('s.terimatgl', $terimatgl);
                    $this->datatables->where("s.terimatgl BETWEEN '$terimatgl' AND '$terimatgl2'");
			}

			if ($this->controller == 'skpd') {

                if ($proses_id == 1 && $usaha_id==pad_reklame_id()){
				$this->datatables->select("s.id, ijin_no as kohirno, k.kohirtgl, get_bayarno(s.id,'pad_spt') as sptno,
					get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as customernm, 
                    p.nama, cu.opnm, (s.tarif*100) as tarif, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.pajak_terhutang as pajak,
					cu.usaha_id, s.type_id", false);
                }else if ($proses_id == 1 && $usaha_id==pad_air_tanah_id()){
                $this->datatables->select("s.id, get_kohirno(s.id) as kohirno, k.kohirtgl, get_bayarno(s.id,'pad_spt') as sptno,
                    get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as customernm, 
                    p.nama, cu.opnm, (s.tarif*100) as tarif, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.dasar*s.tarif as pajak,
                    cu.usaha_id, s.type_id", false);
                }
                else {
                $this->datatables->select("s.id, get_kohirno(s.id) as kohirno, k.kohirtgl, get_bayarno(s.id,'pad_spt') as sptno,
                    get_nopd(cu.id, true) as npwpd, case when s.r_nama is not null then r_nama else c.nama end as customernm, 
                    p.nama, cu.opnm, (s.tarif*100) as tarif, get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa, 
                    s.dasar, s.pajak_terhutang as pajak,
                    cu.usaha_id, s.type_id", false);
                }

				$this->datatables->from('pad_spt s');
				$this->datatables->join('pad_customer_usaha cu', 's.customer_usaha_id=cu.id', 'left');
				$this->datatables->join('pad_customer c', 'cu.customer_id=c.id', 'left');
				$this->datatables->join('pad_usaha u', 'cu.usaha_id=u.id', 'left');
				$this->datatables->join('pad_jenis_pajak p', 's.pajak_id=p.id', 'left');
				$this->datatables->join('pad_spt_type st', 'st.id=s.type_id', 'left');
				$this->datatables->join('pad_kohir k', 'k.spt_id=s.id', 'left');
                $this->datatables->join('pad_invoice inv', "s.id=inv.source_id and inv.source_nama='pad_spt'", 'left');

                if( $this->session->userdata('skpd_tipe') == 'SKPDKB') {
                $this->datatables->where('upper(st.typenm) =', 'SKPDKB');
                }else if( $this->session->userdata('skpd_tipe') == 'SKPDKBT'){
                $this->datatables->where('upper(st.typenm) =', 'SKPDKB T');
                }else{
                $this->datatables->where('upper(st.typenm) like', 'SKPD');
                }

				if ($this->input->get('iSortCol_0') == 1) {
					$sort = $this->input->get('sSortDir_0');
					$this->datatables->order_by('k.kohirno', $sort);
				}
                
               if ($proses_id == 2) {
                    $this->datatables->where('k.kohirno is not NULL');
               }else{
                    if ($usaha_id==pad_reklame_id()){
                    $this->datatables->where('k.kohirtgl is NULL');  
                    }else{
                    $this->datatables->where('k.kohirno is NULL');
                    }
                }
                if($this->input->get('sSearch') == '')
                    if ($proses_id == 2) 
                        //$this->datatables->filter('k.kohirtgl', $terimatgl);
                        $this->datatables->where("k.kohirtgl BETWEEN '$terimatgl' AND '$terimatgl2'");
                    else
                        //$this->datatables->filter('s.terimatgl', $terimatgl);
                        $this->datatables->where("s.terimatgl BETWEEN '$terimatgl' AND '$terimatgl2'");
			}

			// $this->datatables->where('s.tahun', pad_tahun_anggaran());
                if($usaha_id != 999 && $usaha_id != null)
                    $this->datatables->filter('cu.usaha_id', $usaha_id);


             if($status_pembayaran != 999 && $status_pembayaran != null){
                $this->datatables->where('inv.status_bayar', $status_pembayaran);
            }



             if($status_validasi != 999 && $status_validasi!=null )
                $this->datatables->where('s.proses', $status_validasi);
            
			$this->datatables->rupiah_column('10,11');
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
                $this->datatables->join('pad_invoice inv', "s.id=inv.source_id and inv.source_nama='pad_spt'", 'left');

				if ($this->input->get('iSortCol_0') == 1) {
					$sort = $this->input->get('sSortDir_0');
					$this->datatables->order_by('s.sptno', $sort);
				}
			}
			
			// $this->datatables->where('s.tahun', pad_tahun_anggaran());
			$this->datatables->where('c.id', wp_id());
			
			if($type_id <> 999 && !empty($type_id))
				$this->datatables->where('s.type_id', $type_id);
			if($usaha_id <> 999 && !empty($usaha_id))
				$this->datatables->where('cu.usaha_id', $usaha_id);
            if($status_pembayaran <> 999 && !empty($status_pembayaran))
                $this->datatables->where('inv.status_bayar', $status_pembayaran);
            if($status_validasi <> 999 && !empty($status_validasi))
                $this->datatables->where('s.proses', $status_validasi);

			$this->datatables->checkbox_column('9');
			$this->datatables->rupiah_column('8,9');
			$this->datatables->date_column('2');
		}
		

        echo $this->datatables->generate();
    }

    function grid_media($spt_id_media) 
    {
        $this->load->library('Datatables');
        $this->datatables->select('media_id, media, kelas_jalan_id, k.nama, panjang, lebar, tinggi, 
                                    (panjang* lebar*tinggi) as plt, 
                                    sisi, banyak, lama, nsr, tambahan, 
                                    ROUND((panjang* lebar*tinggi*sisi*banyak*nsr*lama) + tambahan) as jumlah, alamat, 
                                    ROUND((panjang* lebar*tinggi*sisi*banyak*nsr*lama) + tambahan) as jumlah2, status_baliho, jalan_id');
        $this->datatables->from('pad_spt_reklame s');   
        $this->datatables->join('pad_reklame_media m', 's.media_id = m.id');
        $this->datatables->join('pad_reklame_kelas_jalan k', 's.kelas_jalan_id = k.id');
        $this->datatables->where('spt_id', $spt_id_media );
        $this->datatables->add_column('batal', '<a class="edit" href=""><i class="fa fa-edit"></i></a> | <a class="delete" href=""><i class="fa fa-trash"></i>X</a>');

        $this->datatables->rupiah_column('11,12,13');
        echo $this->datatables->generate();
    }


    function grid_air_tanah_history($customer_id) 
    {
        $this->load->library('Datatables');
        $this->datatables->select('s.id , get_bulan(cast(extract(month from s.masadari) as integer), 
            true)||\'.\'||cast(extract(year from s.masadari) as integer) masa,                                                                                  
            pat.sumur_ke, pat.sipa_no, pat.awal, pat.akhir, pat.volume', false);
        $this->datatables->from("pad_spt s");                                                                                  
        $this->datatables->join("pad_air_tanah pat", "s.id = pat.spt_id", "inner");                                                                                  
        $this->datatables->where('s.customer_id', $customer_id);  
        $this->datatables->order_by("s.masadari", "desc"); 
        $this->datatables->decimal3_column('4,5,6');
        echo $this->datatables->generate();
    }

    //SKPD KB
    public function kb($id)
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
            
            $options = array(
                '1' => 'BELUM PROSES',
                '2' => 'SUDAH PROSES',
            );
            $js = 'id="proses_id" class="input-medium"';
            $select = form_dropdown('proses_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);
            $data['select_proses_id'] = $select;

            $select_data = $this->load->model('usaha_model')->get_select();
            $options     = array();
            $options[999] = 'SEMUA USAHA';
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js = 'id="usaha_id_sel" class="input-medium"';
            $select = form_dropdown('usaha_id_sel', $options, $id, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);

            $data['select_usaha_sel'] = $select;

            if($id != 999){
            $select_data = $this->load->model('usaha_model')->get_select_one($id);
            $js = 'id="usaha_id" class="input-medium" readonly';
            }
            if ($id == 999){
            $select_data = $this->load->model('usaha_model')->get_select();
            $js = 'id="usaha_id" class="input-medium"';
            }

            if($id){
            $selected = $row->id; //selected id (filter langsung)
            $select = form_dropdown('usaha_id', $options, $selected, $js);
            if($id != 'default'){
            $this->session->unset_userdata('usaha_nama');
            $this->session->set_userdata('usaha_nama', $options);
            }
            if ($id == 'default')
            {
            $array = array('SEMUA USAHA');
            $this->session->unset_userdata('usaha_nama');
            $this->session->set_userdata('usaha_nama', $array);
            }
            $data['add_id'] = $id;
            }
            else{
            $options[999] = 'SEMUA PAJAK';
            $select = form_dropdown('usaha_id', $options, 999, $js);
            }
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_usaha'] = $select;


            $options     = array();
            $options[999] = 'SEMUA';
            $options[0] = 'BELUM BAYAR';
            $options[1] = 'SUDAH BAYAR';
            $js                   = 'id="bayar_id" class="form-group"';
            $select = form_dropdown('bayar_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_bayar'] = $select;

            $options     = array();
            $options[999] = 'SEMUA';
            $options[1] = 'BELUM VALIDASI';
            $options[2] = 'SUDAH VALIDASI';
            $js                   = 'id="validasi_id" class="form-group"';
            $select = form_dropdown('validasi_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_validasi'] = $select;

            $select_data = $this->load->model('pegawai_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $options[999] = 'TIDAK ADA';
            $js                       = 'id="petugas_id" class="input-large" style="width:180px"';
            $data['select_petugas'] = form_dropdown('petugas_id', $options, 999, $js);

            $js                       = 'id="pejabat_id" class="input-large" style="width:180px"';
            $data['select_pejabat'] = form_dropdown('pejabat_id', $options, 999, $js);
            $this->session->set_userdata('skpd_tipe', 'SKPDKB');
            $this->load->view(active_module().'/vsptpd', $data);
            } 

            else 

            {
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

            $options     = array();
            $options[0] = 'BELUM BAYAR';
            $options[1] = 'SUDAH BAYAR';
            $js                   = 'id="bayar_id" class="form-group"';
            $select = form_dropdown('bayar_id', $options, 0, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_bayar'] = $select;

            $options     = array();
            $options[1] = 'BELUM VALIDASI';
            $options[2] = 'SUDAH VALIDASI';
            $js                   = 'id="validasi_id" class="form-group"';
            $select = form_dropdown('validasi_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_validasi'] = $select;

            $this->session->set_userdata('skpd_tipe', 'SKPDKB');
            $this->load->view(active_module().'/wp/vsptpd', $data);
        }
    }

        //SKPD KBT
    public function kbt($id)
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
            
            $options = array(
                '1' => 'BELUM PROSES',
                '2' => 'SUDAH PROSES',
            );
            $js = 'id="proses_id" class="input-medium"';
            $select = form_dropdown('proses_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);
            $data['select_proses_id'] = $select;


            $select_data = $this->load->model('usaha_model')->get_select();
            $options     = array();
            $options[999] = 'SEMUA USAHA';
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js = 'id="usaha_id_sel" class="input-medium"';
            $select = form_dropdown('usaha_id_sel', $options, $id, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);

            $data['select_usaha_sel'] = $select;

            if($id != 999){
            $select_data = $this->load->model('usaha_model')->get_select_one($id);
            $js = 'id="usaha_id" class="input-medium" readonly';
            }
            if ($id == 999){
            $select_data = $this->load->model('usaha_model')->get_select();
            $js = 'id="usaha_id" class="input-medium"';
            }
            if($id){
            $selected = $row->id; //selected id (filter langsung)
            $select = form_dropdown('usaha_id', $options, $selected, $js);
            if($id != 'default'){
            $this->session->unset_userdata('usaha_nama');
            $this->session->set_userdata('usaha_nama', $options);
            }
            if ($id == 'default')
            {
            $array = array('SEMUA USAHA');
            $this->session->unset_userdata('usaha_nama');
            $this->session->set_userdata('usaha_nama', $array);
            }
            $data['add_id'] = $id;
            }
            else{
            $options[999] = 'SEMUA PAJAK';
            $select = form_dropdown('usaha_id', $options, 999, $js);
            }
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_usaha'] = $select;


            $options     = array();
            $options[999] = 'SEMUA';
            $options[0] = 'BELUM BAYAR';
            $options[1] = 'SUDAH BAYAR';
            $js                   = 'id="bayar_id" class="form-group"';
            $select = form_dropdown('bayar_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_bayar'] = $select;

            $options     = array();
            $options[999] = 'SEMUA';
            $options[1] = 'BELUM VALIDASI';
            $options[2] = 'SUDAH VALIDASI';
            $js                   = 'id="validasi_id" class="form-group"';
            $select = form_dropdown('validasi_id', $options, 999, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_validasi'] = $select;

            $select_data = $this->load->model('pegawai_model')->get_select();
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $options[999] = 'TIDAK ADA';
            $js                       = 'id="petugas_id" class="input-large" style="width:180px"';
            $data['select_petugas'] = form_dropdown('petugas_id', $options, 999, $js);

            $js                       = 'id="pejabat_id" class="input-large" style="width:180px"';
            $data['select_pejabat'] = form_dropdown('pejabat_id', $options, 999, $js);
            
            $this->session->set_userdata('skpd_tipe', 'SKPDKBT');
            $this->load->view(active_module().'/vsptpd', $data);
            } 

            else 

            {
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

            $options     = array();
            $options[0] = 'BELUM BAYAR';
            $options[1] = 'SUDAH BAYAR';
            $js                   = 'id="bayar_id" class="form-group"';
            $select = form_dropdown('bayar_id', $options, 0, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_bayar'] = $select;

            $options     = array();
            $options[1] = 'BELUM VALIDASI';
            $options[2] = 'SUDAH VALIDASI';
            $js                   = 'id="validasi_id" class="form-group"';
            $select = form_dropdown('validasi_id', $options, 1, $js);
            $select = preg_replace("/[\r\n]+/", "", $select);;
            $data['select_validasi'] = $select;

            $this->session->set_userdata('skpd_tipe', 'SKPDKBT');
            $this->load->view(active_module().'/wp/vsptpd', $data);
        }
    }

    //admin
    private function fvalidation()
    {
        //spt global
        $jenis_usaha = $this->uri->segment(4);

        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('tahun', 'tahun', 'required|numeric');
        $this->form_validation->set_rules('terimatgl', 'terimatgl', 'required');
        $this->form_validation->set_rules('customer_id', 'customer_id', 'required|numeric');
        $this->form_validation->set_rules('customer_usaha_id', 'customer_usaha_id', 'required|numeric');
        $this->form_validation->set_rules('pajak_id', 'pajak_id', 'required|numeric');
        $this->form_validation->set_rules('rekening_id', 'rekening_id', 'required|numeric');
        $this->form_validation->set_rules('so', 'so', 'required|trim');
        $this->form_validation->set_rules('type_id', 'type_id', 'required|numeric');
        $this->form_validation->set_rules('masapajak_bulan', 'masapajak_bulan', 'required');
        //$this->form_validation->set_rules('masasd', 'masasd', 'required');
        $this->form_validation->set_rules('jatuhtempotgl', 'jatuhtempotgl', 'required');
        $this->form_validation->set_rules('r_bayarid', 'r_bayarid', 'required|numeric');

        //air
        if ($jenis_usaha == pad_air_tanah_id()) {
            if (pad_to_decimal($this->input->post('dasar')) <= 0 )
            $this->form_validation->set_rules('dasar', 'NPA', 'required|is_natural_no_zero');
            if (pad_to_decimal($this->input->post('volume')) <= 0 )
            $this->form_validation->set_rules('volume', 'volume', 'required|is_natural_no_zero');
        }

        //reklame
        if ($jenis_usaha == pad_reklame_id()) {
            //$this->form_validation->set_rules('r_nsl_kecamatan_id', 'r_nsl_kecamatan_id', 'required|numeric');
            //$this->form_validation->set_rules('r_nsl_type_id', 'r_nsl_type_id', 'required|numeric');
            //$this->form_validation->set_rules('r_nsl_nilai', 'r_nsl_nilai', 'required|numeric');
            //$this->form_validation->set_rules('r_kelurahan_id', 'r_kelurahan_id', 'required|numeric');
            //$this->form_validation->set_rules('r_nsr', 'r_nsr', 'required|numeric');
            // $this->form_validation->set_rules('r_nsrno', 'r_nsrno', 'required|trim');
            //$this->form_validation->set_rules('r_nsrtgl', 'r_nsrtgl', 'required');
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
        $data['multiple']        = $this->input->post('multiple');
         $data['masapajak_bulan']   = $this->input->post('masapajak_bulan');

        $data['minimal_omset']   = $this->input->post('minimal_omset');
        $data['dasar']      = pad_to_decimal($this->input->post('dasar'));
        $data['tarif']      = pad_to_decimal($this->input->post('tarif'));

        if ($jenis_usaha==pad_air_tanah_id() || $jenis_usaha==pad_reklame_id()){
            $data['denda']      = pad_to_decimal($this->input->post('denda'));
            $data['bunga']      = pad_to_decimal($this->input->post('bunga'));
        }else{
            $data['denda']      = 0;
            $data['bunga']      = 0;
        }


        $data['setoran']    = pad_to_decimal($this->input->post('setoran'));
        $data['kenaikan']   = pad_to_decimal($this->input->post('kenaikan'));
        $data['kompensasi'] = pad_to_decimal($this->input->post('kompensasi'));
        $data['lain2']      = pad_to_decimal($this->input->post('lain2'));
		
        $data['r_nsr']     = pad_to_decimal($this->input->post('r_nsr'));
        $data['r_bayarid'] = $this->input->post('r_bayarid');

        $data['doc_no'] = $this->input->post('doc_no');
        $data['cara_bayar'] = $this->input->post('cara_bayar');


        $data['kirimtgl']     = $this->input->post('kirimtgl');
        $data['notes']        = $this->input->post('notes');
        $data['unit_id']      = $this->input->post('unit_id');
        $data['enabled']      = $this->input->post('enabled');
        $data['terimanip']    = $this->input->post('terimanip');
        $data['isprint_dc']   = $this->input->post('isprint_dc');
        
        $data['max_hit']   = $this->input->post('max_hit');
        for($i=1; $i<=$data['max_hit']; $i++){
        $data['hit_vol'.$i]   = $this->input->post('hit_vol'.$i);
        $data['hit_hda'.$i]   = $this->input->post('hit_hda'.$i);
        $data['hit_val'.$i]   = $this->input->post('hit_val'.$i);
        }

        $data['created'] = date('Y-m-d h:i:s');
        $data['create_uid']  = sipkd_user_id();
        $data['updated']  = date('Y-m-d h:i:s');
        $data['update_uid']   = sipkd_user_id();
        
        //omset 31 Hari
        for($n=1; $n<=32; $n++){
        $omset = 'omset'.$n;
        $keterangan = 'keterangan'.$n;

        $data[$omset]      = pad_to_decimal($this->input->post($omset));
        $data[$keterangan]      = $this->input->post($keterangan);
        }

        $data['nama']   = $this->input->post('nama'); // kota Tangerang

        //air
        if ($jenis_usaha == pad_air_tanah_id()) {
            $data['air_manfaat_id'] = $this->input->post('air_manfaat_id');
            $data['air_zona_id']    = $this->input->post('air_zona_id');
            $data['meteran_awal']   = pad_to_decimal($this->input->post('meteran_awal'));
            $data['meteran_akhir']  = pad_to_decimal($this->input->post('meteran_akhir'));
            $data['volume']         = pad_to_decimal($this->input->post('volume'));
            $data['satuan']         = $this->input->post('satuan');
            $data['end_loop_sumur']         = $this->input->post('end_loop_sumur');

            $end = $this->input->post('end_loop_sumur');
            for($i=1; $i<=$end; $i++){
                    $m_sumur = 'sumur_ke'.$i;
                    $m_awal = 'meteran_awal'.$i;
                    $m_akhir = 'meteran_akhir'.$i;
                    $m_volume = 'volume'.$i;
                    $m_sipa = 'sipa_no'.$i;

                    $data[$m_sumur] = pad_to_decimal($this->input->post($m_sumur));
                    $data[$m_awal]= pad_to_decimal($this->input->post($m_awal));
                    $data[$m_akhir] = pad_to_decimal($this->input->post($m_akhir));
                    $data[$m_volume] = pad_to_decimal($this->input->post($m_volume));
                    $data[$m_sipa] = pad_to_decimal($this->input->post($m_sipa));


            }
        }

        //reklame
        if ($jenis_usaha == pad_reklame_id()) {
            $data['r_nsrno']  = $this->input->post('r_nsrno');
            $data['r_nsrtgl'] = $this->input->post('r_nsrtgl');


            $data['r_jalanklas_id'] = $this->input->post('r_jalanklas_id');
            $data['r_jalan_id']     = $this->input->post('r_jalan_id');
            $data['r_lokasi']       = $this->input->post('r_lokasi');
            $data['r_judul']        = $this->input->post('r_judul');
            $data['ijin_no']        = $this->input->post('ijin_no');
            $data['jenis_masa']        = $this->input->post('jenis_masa');


            $data['r_panjang'] = pad_to_decimal($this->input->post('r_panjang'), 1);
            $data['r_lebar']   = pad_to_decimal($this->input->post('r_lebar'), 1);
            $data['r_muka']    = pad_to_decimal($this->input->post('r_muka'), 1);
            $data['r_banyak']  = pad_to_decimal($this->input->post('r_banyak'), 1);
            $data['r_luas']    = pad_to_decimal($this->input->post('r_luas'), 1);

            $data['r_tarifid']    = $this->input->post('r_tarifid');
            $data['r_nsl_nilai']  = pad_to_decimal($this->input->post('r_nsl_nilai'));
            $data['r_lama']       = $this->input->post('r_lama');
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

            $data['r_lokasi'] = $this->input->post('r_lokasi');
            $data['no_telp'] = $this->input->post('no_telp');
            $data['skpd_old']    = $this->input->post('skpd_old');
        }
		
        $data['pajak'] = pad_to_decimal($this->input->post('pajak'));
        $data['kode'] = $this->input->post('kode');
        $data['jatuhtempo'] = $this->input->post('jatuhtempo');
        return $data;
    }

    public function add()
    {
		$this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
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
            // $sptno      = $this->sptpd_model->generate_sptno(date('Y', strtotime($input_post['terimatgl'])), date('m', strtotime($input_post['terimatgl'])));


                $query = $this->db->query("select get_nospt($p_usaha_id) as nospt;");
                foreach ($query->result() as $row){ $sptno = $row->nospt;} ;

            //untuk spanduk dan semuaaaaaaaaaaaaa
                /*
            $wp_data = $this->load->model('subjek_pajak_model')->get($input_post['customer_id']);
            $wp_nama = $wp_data->nama;
            if($wp_nama != $input_post['nama']) $wp_nama = $input_post['nama'];
            $wp_alamat = $wp_data->alamat;
            */
            
            
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
                'bulan' => date('m', strtotime($input_post['terimatgl'])), //$input_post['tahun'],
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

                'doc_no' => $input_post['doc_no'],
                'cara_bayar' => $input_post['cara_bayar'],

                'usaha_id' => $p_usaha_id,
                'multiple' => $input_post['multiple'],
                /*Omset 31 Hari*/
                'omset1' => $input_post['omset1'], 'omset6' => $input_post['omset6'],
                'omset2' => $input_post['omset2'], 'omset7' => $input_post['omset7'],
                'omset3' => $input_post['omset3'], 'omset8' => $input_post['omset8'],
                'omset4' => $input_post['omset4'], 'omset9' => $input_post['omset9'],
                'omset5' => $input_post['omset5'], 'omset10' => $input_post['omset10'],
                
                'omset11' => $input_post['omset11'], 'omset16' => $input_post['omset16'],
                'omset12' => $input_post['omset12'], 'omset17' => $input_post['omset17'],
                'omset13' => $input_post['omset13'], 'omset18' => $input_post['omset18'],
                'omset14' => $input_post['omset14'], 'omset19' => $input_post['omset19'],
                'omset15' => $input_post['omset15'], 'omset20' => $input_post['omset20'],

                'omset21' => $input_post['omset21'], 'omset26' => $input_post['omset26'],
                'omset22' => $input_post['omset22'], 'omset27' => $input_post['omset27'],
                'omset23' => $input_post['omset23'], 'omset28' => $input_post['omset28'],
                'omset24' => $input_post['omset24'], 'omset29' => $input_post['omset29'],
                'omset25' => $input_post['omset25'], 'omset30' => $input_post['omset30'],
                'omset31' => $input_post['omset31'],  'omset_lain' => $input_post['omset32'],

                // Keterangan

                'keterangan1' => $input_post['keterangan1'], 'keterangan6' => $input_post['keterangan6'],
                'keterangan2' => $input_post['keterangan2'], 'keterangan7' => $input_post['keterangan7'],
                'keterangan3' => $input_post['keterangan3'], 'keterangan8' => $input_post['keterangan8'],
                'keterangan4' => $input_post['keterangan4'], 'keterangan9' => $input_post['keterangan9'],
                'keterangan5' => $input_post['keterangan5'], 'keterangan10' => $input_post['keterangan10'],
                
                'keterangan11' => $input_post['keterangan11'], 'keterangan16' => $input_post['keterangan16'],
                'keterangan12' => $input_post['keterangan12'], 'keterangan17' => $input_post['keterangan17'],
                'keterangan13' => $input_post['keterangan13'], 'keterangan18' => $input_post['keterangan18'],
                'keterangan14' => $input_post['keterangan14'], 'keterangan19' => $input_post['keterangan19'],
                'keterangan15' => $input_post['keterangan15'], 'keterangan20' => $input_post['keterangan20'],

                'keterangan21' => $input_post['keterangan21'], 'keterangan26' => $input_post['keterangan26'],
                'keterangan22' => $input_post['keterangan22'], 'keterangan27' => $input_post['keterangan27'],
                'keterangan23' => $input_post['keterangan23'], 'keterangan28' => $input_post['keterangan28'],
                'keterangan24' => $input_post['keterangan24'], 'keterangan29' => $input_post['keterangan29'],
                'keterangan25' => $input_post['keterangan25'], 'keterangan30' => $input_post['keterangan30'],

                'keterangan31' => $input_post['keterangan31'], 'keterangan_lain' => $input_post['keterangan32'],

                'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
                'terimanip' => sipkd_user_id(),
                'unit_id' => pad_pemda_unitid(),

                'enabled' => 1,
                'satuan' => NULL,
                'notes' => $input_post['notes'],
                
                /*
                'updated' => date('Y-m-d', strtotime($input_post['updated'])),
                'update_uid' => $input_post['update_uid'],
                'kirimtgl' => date('Y-m-d', strtotime($input_post['kirimtgl'])),
                'isprint_dc' => $input_post['isprint_dc'],
                
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
                    'r_judul' => $input_post['r_judul'],
                    'r_calculated' => $input_post['r_calculated'],
                    'r_sudut_pandang_id' => $input_post['r_sudut_pandang_id'],
                    'r_sudut_pandang_val' => $input_post['r_sudut_pandang_val'],
                    'r_njop' => $input_post['r_njop'],
                    'r_status' => $input_post['r_status'],
                    'ijin_no' => $input_post['ijin_no'],
                    'jenis_masa' => $input_post['jenis_masa'],
                    //'r_nama' => $wp_nama,
                    //'r_alamat' => $wp_alamat,
                    'r_lokasi' => $input_post['r_lokasi'],
                    'no_telp' => $input_post['no_telp'],
                    'skpd_lama' => $input_post['skpd_old'],
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


        if($this->session->userdata("mode")=="add"){
        if( $input_post['multiple']==0){
            $lastinput = date('Y-m-d', strtotime($input_post['masadari']));
            $customer_usaha_id = $input_post['customer_usaha_id'];
            $pajak_id = $input_post['pajak_id'];
            $masapajak_bulan = $input_post['masapajak_  bulan'];
            $rekening_id = $input_post['rekening_id'];
            $type_id = $input_post['type_id'];

            $cekduplikat= $this->sptpd_model->is_multiple($customer_usaha_id, $pajak_id, $type_id, $rekening_id, $lastinput, $this->session->userdata("mode"));   
            if ($cekduplikat==true) {
                $this->session->set_flashdata('msg_warning', 'Pajak tersebut dengan Masa Pajak: '. $masapajak_bulan.' Sudah Ada, Harap Cek Data-data Sebelumnya');
                redirect(active_module_url($this->controller.'/add/'.$p_usaha_id));
            }
            else{
                $update_data = array_merge($update_data, $reklame_data, $air_tanah_data);
                $spt_id = $this->sptpd_model->save($update_data);
            }
        }
         else{
            $update_data = array_merge($update_data, $reklame_data, $air_tanah_data);
            $spt_id = $this->sptpd_model->save($update_data);
            }

        }else{
            $this->session->set_flashdata('msg_warning', 'UNKNOWN MODE');
            redirect(active_module_url($this->controller.'/index/'.$p_usaha_id));
            }


            //INITIALIZE LAST SPT PLUS SPT NO sebagai penguat data
            $query = $this->db->query("select max(id) as id from pad_spt where sptno = $sptno");
                foreach ($query->result() as $row)
                {
                   $spt_id = $row->id;
                }
                $create_uid = sipkd_user_id();
                $created = date('Y-m-d h:i:s');

            //SAVE SUMUR
            if ($p_usaha_id == pad_air_tanah_id()) {
                $end = $input_post['end_loop_sumur'];

                for ($i=1; $i<=$end; $i++){
                    $m_sumur = 'sumur_ke'.$i;
                    $m_awal = 'meteran_awal'.$i;
                    $m_akhir = 'meteran_akhir'.$i;
                    $m_volume = 'volume'.$i;
                    $m_sipa = 'sipa_no'.$i;

                    $sumur =  $input_post[$m_sumur];
                    $awal = $input_post[$m_awal];
                    $akhir = $input_post[$m_akhir];
                    $vol = $input_post[$m_volume];
                    $sipa_no = $input_post[$m_sipa];

                    $this->db->query("insert into pad_air_tanah (sumur_ke, spt_id, sipa_no, awal, akhir, volume, create_uid, created) 
                                      values ($sumur, $spt_id, '$sipa_no', $awal, $akhir, $vol,  $create_uid, '$created')");
                }
                   
                    $tarif = $input_post['tarif'];
                    for($i=1; $i<=$input_post['max_hit']; $i++){
                    $volume = $input_post['hit_vol'.$i];
                    $hda = $input_post['hit_hda'.$i];
                    $jumlah = $input_post['hit_val'.$i];
                    $this->db->query("insert into pad_air_tanah_hit 
                                             (spt_id,  vol,      hda,  jumlah,  tarif,  create_uid,   created) 
                                      values ($spt_id, $volume,  $hda, $jumlah, $tarif, $create_uid, '$created')");
                    }
            }

                //SAVE MEDIA REKLAME

               if ($p_usaha_id == pad_reklame_id()) {
                                    // data tambahan / detail
                    $dtKD = $this->input->post('dtKD');
                    $tambahan_data2 = array();
                    
                    if(isset($dtKD)) {
                        $i = 1;
                        $dtKD = json_decode($dtKD, true);
                        
                        if(count($dtKD['dtKD']) > 0){
                            $rd_row = array();
                            foreach($dtKD['dtKD'] as $rows) {
                                $rd_row = array (
                                    'spt_id' => $spt_id,
                                    'media_id'  => $rows[0],
                                    'kelas_jalan_id'  => $rows[2],
                                    'panjang'  => $rows[4],
                                    'lebar' => $rows[5],
                                    'tinggi'  => $rows[6],
                                    'sisi'  => $rows[8],
                                    'banyak' => $rows[9],
                                    'lama' => $rows[10],
                                    'nsr' => pad_to_decimal($rows[11]),
                                    'tambahan' => pad_to_decimal($rows[12]),
                                    'alamat' => $rows[14],
                                    'status_baliho' => $rows[16],
                                    'jalan_id'  => $rows[17],


                                    'create_uid' => $create_uid,
                                    'created' => $created,
                                );
                                $i++;
                                $tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
                            }
                            $this->db->insert_batch('pad_spt_reklame', $tambahan_data2);
                        }
                      }
                   } 

            //update pad_spt set bulan = extract(month from terimatgl);
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
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
        $month_ini = new DateTime("first day of last month");
        $data['dt']['masadari']  = $month_ini->format('d-m-Y'); 
        $data['dt']['masapajak_bulan']  = $month_ini->format('M-Y'); 

        $options              = array();
        $js                   = 'id="customer_usaha_id" class="input-xlarge" readonly ';
        $data['select_usaha'] = form_dropdown('customer_usaha_id', $options, null, $js);

        $js                   = 'id="pajak_id" class="form-control" ';
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

		$data['dt']['kode']  = '';
		$data['dt']['jatuhtempo']  = '';

        if ($p_usaha_id == pad_reklame_id()) {
            $select_data = $this->load->model('jalan_kelas_model')->get_select();
            $options     = array();
			if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
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
                $options[$row->id] = $row->nama;
            }}
            $js                    = 'id="r_lokasi_id" class="input-medium" required ';
            $data['select_lokasi'] = form_dropdown('r_lokasi_id', $options, $get->r_lokasi_id, $js);
            */

            $options              = array(
                1 => 'Tidak ada',
                2 => 'Produk Rokok +25%',
                //3 => 'Reklame Pendidikan -25%',
                //4 => 'Kenaikan 25% & Pengurangan 25%'
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
            
            //$select_data = $this->load->model('reklame_media_model')->get_select();

            $options              = array('0' => '-');
            $js                        = 'id="r_media_id" class="input-xlarge" required ';
            $data['select_media_reklame'] = form_dropdown('r_media_id', $options, '', $js);

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
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
		
        $p_usaha_id = $this->uri->segment(4);
        $p_type_id  = $this->uri->segment(5);
        $p_id       = $this->uri->segment(6);
        $pajak_id   = $this->uri->segment(7);
        $cu_id   = $this->uri->segment(8);


		//cek kohir
        if ($this->sptpd_model->is_kohir_ok($p_id) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
		
		// cek pmb
        // kalau user sa boleh edit. 17-10-2014 (AA)
        /*
        if (($this->sptpd_model->is_bayar($p_id)) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
        */

                // kalau user sa boleh edit. 17-10-2014 (AA)
        if ($this->sptpd_model->is_bayar($p_id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }

                // Validasi
        if ($this->sptpd_model->is_validasi_ok($p_id)) {
            $this->session->set_flashdata('msg_warning', 'Data tidak dapat diubah karena sudah divalidasi, Harap unvalidasi jika ingin mengubah data');
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
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
            $this->session->set_userdata('masadari',date('d-m-Y', strtotime($get->masadari)) );
            $data['dt']['masapajak_bulan']    = date('M-Y', strtotime($get->masadari));
            $data['dt']['masasd']             = date('d-m-Y', strtotime($get->masasd));
            $data['dt']['minimal_omset']           = $get->minimal_omset;
            $data['dt']['dasar']              = $get->dasar;
            $data['dt']['tarif']              = $get->tarif;

            if ($p_usaha_id==pad_air_tanah_id() || $p_usaha_id==pad_reklame_id()){
                $data['dt']['denda']              = $get->denda;
                $data['dt']['bunga']              = $get->bunga;
            }else{
                $data['dt']['denda']              = 0;
                $data['dt']['bunga']              = 0;
            }

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
            $data['dt']['created']            = date('d-m-Y', strtotime($get->created));
            $data['dt']['create_uid']         = $get->create_uid;
            $data['dt']['updated']            = date('d-m-Y', strtotime($get->updated));
            $data['dt']['update_uid']         = $get->update_uid;
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
            $data['dt']['r_lokasi']       = $get->r_lokasi;
            $data['dt']['no_telp']        = $get->no_telp;
            $data['dt']['skpd_old']        = $get->skpd_lama;

            $data['dt']['multiple']        =$get->multiple;

            $data['dt']['ijin_no']       = $get->ijin_no;
            $data['dt']['jenis_masa']       = $get->jenis_masa;


            
            $data['dt']['nama']       = $get->r_nama;
            $data['dt']['doc_no']       = $get->doc_no;
            $data['dt']['cara_bayar']   = $get->cara_bayar;

   
            $data['dt']['omset1'] = $get->omset1; $data['dt']['omset6'] = $get->omset6;
            $data['dt']['omset2'] = $get->omset2; $data['dt']['omset7'] = $get->omset7;
            $data['dt']['omset3'] = $get->omset3; $data['dt']['omset8'] = $get->omset8;
            $data['dt']['omset4'] = $get->omset4; $data['dt']['omset9'] = $get->omset9;
            $data['dt']['omset5'] = $get->omset5; $data['dt']['omset10'] = $get->omset10;
            
            $data['dt']['omset11'] = $get->omset11; $data['dt']['omset16'] = $get->omset16;
            $data['dt']['omset12'] = $get->omset12; $data['dt']['omset17'] = $get->omset17;
            $data['dt']['omset13'] = $get->omset13; $data['dt']['omset18'] = $get->omset18;
            $data['dt']['omset14'] = $get->omset14; $data['dt']['omset19'] = $get->omset19;
            $data['dt']['omset15'] = $get->omset15; $data['dt']['omset20'] = $get->omset20;

            $data['dt']['omset21'] = $get->omset21; $data['dt']['omset26'] = $get->omset26;
            $data['dt']['omset22'] = $get->omset22; $data['dt']['omset27'] = $get->omset27;
            $data['dt']['omset23'] = $get->omset23; $data['dt']['omset28'] = $get->omset28;
            $data['dt']['omset24'] = $get->omset24; $data['dt']['omset29'] = $get->omset29;
            $data['dt']['omset25'] = $get->omset25; $data['dt']['omset30'] = $get->omset30;

            $data['dt']['omset31'] = $get->omset31; $data['dt']['omset32'] = $get->omset_lain;

            $data['dt']['keterangan1'] = $get->keterangan1; $data['dt']['keterangan6'] = $get->keterangan6;
            $data['dt']['keterangan2'] = $get->keterangan2; $data['dt']['keterangan7'] = $get->keterangan7;
            $data['dt']['keterangan3'] = $get->keterangan3; $data['dt']['keterangan8'] = $get->keterangan8;
            $data['dt']['keterangan4'] = $get->keterangan4; $data['dt']['keterangan9'] = $get->keterangan9;
            $data['dt']['keterangan5'] = $get->keterangan5; $data['dt']['keterangan10'] = $get->keterangan10;
            
            $data['dt']['keterangan11'] = $get->keterangan11; $data['dt']['keterangan16'] = $get->keterangan16;
            $data['dt']['keterangan12'] = $get->keterangan12; $data['dt']['keterangan17'] = $get->keterangan17;
            $data['dt']['keterangan13'] = $get->keterangan13; $data['dt']['keterangan18'] = $get->keterangan18;
            $data['dt']['keterangan14'] = $get->keterangan14; $data['dt']['keterangan19'] = $get->keterangan19;
            $data['dt']['keterangan15'] = $get->keterangan15; $data['dt']['keterangan20'] = $get->keterangan20;

            $data['dt']['keterangan21'] = $get->keterangan21; $data['dt']['keterangan26'] = $get->keterangan26;
            $data['dt']['keterangan22'] = $get->keterangan22; $data['dt']['keterangan27'] = $get->keterangan27;
            $data['dt']['keterangan23'] = $get->keterangan23; $data['dt']['keterangan28'] = $get->keterangan28;
            $data['dt']['keterangan24'] = $get->keterangan24; $data['dt']['keterangan29'] = $get->keterangan29;
            $data['dt']['keterangan25'] = $get->keterangan25; $data['dt']['keterangan30'] = $get->keterangan30;
            $data['dt']['keterangan31'] = $get->keterangan31; $data['dt']['keterangan32'] = $get->keterangan_lain;

			$data['dt']['nopd'] = $this->load->model('objek_pajak_model')->get_nopd($get->customer_usaha_id, false);

             $select_data = $this->load->model('pad_model')->sptpd_get_pajak($p_usaha_id);

            $options              = array();
            if($select_data) {
            foreach ($select_data as $rows) {
                if($p_usaha_id == pad_reklame_id())
                    $options[$rows->id] = $rows->nama." = ".number_format($rows->reklame ,0,',','.');
                else
                    $options[$rows->id] = $rows->nama;
            }}
            $js                   = 'id="customer_usaha_id" class="input-xlarge" readonly ';
            $data['select_usaha'] = form_dropdown('customer_usaha_id', $options, null, $js);

            //$select_data = $this->load->model('pad_model')->sptpd_get_pajak($p_usaha_id,$pajak_id);


            $options     = array();
			if($select_data) {
            foreach ($select_data as $rows) {
                if($p_usaha_id == pad_reklame_id())
                    $options[$rows->id] = $rows->nama." = ".number_format($rows->reklame ,0,',','.');
                else
                    $options[$rows->id] = $rows->nama;
            }}
            $js                   = 'id="pajak_id" readonly';
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
            if ($row = $pajak_detail->sptpd_get_pajak_detail($get->pajak_id, $get->terimatgl, 0)) {
                $data['dt']['rekening_id'] = $row->rekening_id;
                $data['dt']['kode']  = $row->kode;
                $data['dt']['jatuhtempo']  = $row->jatuhtempo;
            } else {
                $data['dt']['kode']  = '';
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
                    $options[$row->id] = $row->nama;
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
                    $options[$row->id] = $row->nama;
                }}				
                $js                    = 'id="r_lokasi_id" class="input-medium" required ';
                $data['select_lokasi'] = form_dropdown('r_lokasi_id', $options, $get->r_lokasi_id, $js);

                $options              = array(
                    1 => 'Tidak ada',
                    2 => 'Produk Rokok +25%',
                    //3 => 'Reklame Pendidikan -25%',
                    //4 => 'Kenaikan 25% & Pengurangan 25%'
                );
				$js                   = 'id="r_tarifid" class="input-large" required ';
                $data['select_tarif'] = form_dropdown('r_tarifid', $options, $get->r_tarifid, $js);

                //$select_data = $this->load->model('reklame_media_model')->get_select();
                $options              = array('0' => '-');
                $js                        = 'id="r_media_id" class="input-xlarge" required ';
                $data['select_media_reklame'] = form_dropdown('r_media_id', $options, '', $js);

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
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
		
        $p_usaha_id = $this->uri->segment(4);
        $p_type_id  = $this->uri->segment(5);
        $p_id       = $this->uri->segment(6);

		//cek kohir
        if ($this->sptpd_model->is_kohir_ok($p_id) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
		
		// cek pmb
        // kalau user sa boleh edit. 17-10-2014 (AA)
        if (($this->sptpd_model->is_bayar($p_id)) && !is_super_admin()) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
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

            
            //HANDLE JIKA TANGGAL TERIMA BACKDATE
            $sptno = $input_post['sptno'];
            $query = $this->db->query("select terimatgl from pad_spt where id=$p_id");
                foreach ($query->result() as $row){ $trmtgl_old = $row->terimatgl;} ;

            $trmtgl_old = (new DateTime($trmtgl_old))->format('Y-m');
            $trmtgl_new = (new DateTime($input_post['terimatgl']))->format('Y-m');

            $bln = date('m', strtotime($input_post['terimatgl']));
            $thn = date('Y', strtotime($input_post['terimatgl']));

            if(strtotime($trmtgl_new) < strtotime($trmtgl_old)){
                $query = $this->db->query("select sptno from pad_spt where usaha_id=$p_usaha_id  
                                            AND EXTRACT(MONTH FROM terimatgl) = $bln  
                                            AND EXTRACT(YEAR FROM terimatgl) = $thn 
                                            order by sptno desc limit 1;");
                foreach ($query->result() as $row){ $sptno = $row->sptno + 1 ;} ;
                if($query->num_rows()<1){
                   $sptno =1;  
                }
            }else{
                $sptno = $input_post['sptno'];
            }

            // END HANDLE


            $update_data = array(
                'sptno' => $sptno,
                'customer_id' => $input_post['customer_id'],
                'customer_usaha_id' => $input_post['customer_usaha_id'],
                'pajak_id' => $input_post['pajak_id'],
                'tahun' => date('Y', strtotime($input_post['terimatgl'])), //$input_post['tahun'],
                'terimatgl' => date('Y-m-d', strtotime($input_post['terimatgl'])),
                'bulan' => date('m', strtotime($input_post['terimatgl'])), //$input_post['tahun'],
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

                'doc_no' => $input_post['doc_no'],
                'cara_bayar' => $input_post['cara_bayar'],
                'usaha_id' => $p_usaha_id,
                'multiple' => $input_post['multiple'],

                 /*Omset 30 Hari*/
                'omset1' => $input_post['omset1'], 'omset6' => $input_post['omset6'],
                'omset2' => $input_post['omset2'], 'omset7' => $input_post['omset7'],
                'omset3' => $input_post['omset3'], 'omset8' => $input_post['omset8'],
                'omset4' => $input_post['omset4'], 'omset9' => $input_post['omset9'],
                'omset5' => $input_post['omset5'], 'omset10' => $input_post['omset10'],
                
                'omset11' => $input_post['omset11'], 'omset16' => $input_post['omset16'],
                'omset12' => $input_post['omset12'], 'omset17' => $input_post['omset17'],
                'omset13' => $input_post['omset13'], 'omset18' => $input_post['omset18'],
                'omset14' => $input_post['omset14'], 'omset19' => $input_post['omset19'],
                'omset15' => $input_post['omset15'], 'omset20' => $input_post['omset20'],

                'omset21' => $input_post['omset21'], 'omset26' => $input_post['omset26'],
                'omset22' => $input_post['omset22'], 'omset27' => $input_post['omset27'],
                'omset23' => $input_post['omset23'], 'omset28' => $input_post['omset28'],
                'omset24' => $input_post['omset24'], 'omset29' => $input_post['omset29'],
                'omset25' => $input_post['omset25'], 'omset30' => $input_post['omset30'],

                'omset31' => $input_post['omset31'], 'omset_lain' => $input_post['omset32'],

                // Keterangan

                'keterangan1' => $input_post['keterangan1'], 'keterangan6' => $input_post['keterangan6'],
                'keterangan2' => $input_post['keterangan2'], 'keterangan7' => $input_post['keterangan7'],
                'keterangan3' => $input_post['keterangan3'], 'keterangan8' => $input_post['keterangan8'],
                'keterangan4' => $input_post['keterangan4'], 'keterangan9' => $input_post['keterangan9'],
                'keterangan5' => $input_post['keterangan5'], 'keterangan10' => $input_post['keterangan10'],
                
                'keterangan11' => $input_post['keterangan11'], 'keterangan16' => $input_post['keterangan16'],
                'keterangan12' => $input_post['keterangan12'], 'keterangan17' => $input_post['keterangan17'],
                'keterangan13' => $input_post['keterangan13'], 'keterangan18' => $input_post['keterangan18'],
                'keterangan14' => $input_post['keterangan14'], 'keterangan19' => $input_post['keterangan19'],
                'keterangan15' => $input_post['keterangan15'], 'keterangan20' => $input_post['keterangan20'],

                'keterangan21' => $input_post['keterangan21'], 'keterangan26' => $input_post['keterangan26'],
                'keterangan22' => $input_post['keterangan22'], 'keterangan27' => $input_post['keterangan27'],
                'keterangan23' => $input_post['keterangan23'], 'keterangan28' => $input_post['keterangan28'],
                'keterangan24' => $input_post['keterangan24'], 'keterangan29' => $input_post['keterangan29'],
                'keterangan25' => $input_post['keterangan25'], 'keterangan30' => $input_post['keterangan30'],

                'keterangan31' => $input_post['keterangan31'], 'keterangan_lain' => $input_post['keterangan32'],
                'updated' => date('Y-m-d'),
                'update_uid' => sipkd_user_id(),
                'notes' => $input_post['notes'],
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
                    'r_judul' => $input_post['r_judul'],
                    'r_calculated' => $input_post['r_calculated'],
                    'r_sudut_pandang_id' => $input_post['r_sudut_pandang_id'],
                    'r_sudut_pandang_val' => $input_post['r_sudut_pandang_val'],
                    'r_njop' => $input_post['r_njop'],
                    'r_status' => $input_post['r_status'],
                    'ijin_no' => $input_post['ijin_no'],
                    'jenis_masa' => $input_post['jenis_masa'],
                    //'r_nama' => $wp_nama,
                    //'r_alamat' => $wp_alamat,
                    'no_telp' => $input_post['no_telp'],
                    'r_lokasi' => $input_post['r_lokasi'],
                    'skpd_lama' => $input_post['skpd_old'],
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

        if($this->session->userdata("mode")=="edit"){
        if( $input_post['multiple']==0){
            $lastinput = date('Y-m-d', strtotime($input_post['masadari']));
            $customer_usaha_id = $input_post['customer_usaha_id'];
            $pajak_id = $input_post['pajak_id'];
            $masapajak_bulan = $input_post['masapajak_bulan'];
            $rekening_id = $input_post['rekening_id'];
            $type_id = $input_post['type_id'];

            $cekduplikat= $this->sptpd_model->is_multiple($customer_usaha_id, $pajak_id, $type_id, $rekening_id, $lastinput, $this->session->userdata("mode"));   
            if ($cekduplikat==true) {
                $this->session->set_flashdata('msg_warning', 'Pajak tersebut dengan Masa Pajak: '. $masapajak_bulan.' Sudah Ada, Harap Cek Data-data Sebelumnya');
                redirect(active_module_url($this->controller.'/index/'.$p_usaha_id));
            }
            else{
                //data_valid
                $update_data = array_merge($update_data, $reklame_data, $air_tanah_data);
                $this->sptpd_model->update($p_id, $update_data);
                }
        }
        else{
            $update_data = array_merge($update_data, $reklame_data, $air_tanah_data);
            $this->sptpd_model->update($p_id, $update_data);
            }
        }
        else{
            $this->session->set_flashdata('msg_warning', 'UNKNOWN MODE');
            redirect(active_module_url($this->controller.'/index/'.$p_usaha_id));
        }

            $update_uid = sipkd_user_id();
            $updated = date('Y-m-d h:i:s');

            if ($p_usaha_id == pad_air_tanah_id()) {
            $end = $input_post['end_loop_sumur'];
                $query = $this->db->query("select * from pad_air_tanah where spt_id = $p_id limit 1");
                foreach ($query->result() as $row) {
                    $create_uid = $row->create_uid;
                    $created = $row->created;
                }

                $query = $this->db->query("delete from pad_air_tanah where spt_id = $p_id"); //delete dulu
                $spt_id = $p_id;
                    for ($i=1; $i<=$end; $i++){
                        $m_sumur = 'sumur_ke'.$i;
                        $m_awal = 'meteran_awal'.$i;
                        $m_akhir = 'meteran_akhir'.$i;
                        $m_volume = 'volume'.$i;
                        $m_sipa = 'sipa_no'.$i;

                        $sumur =  $input_post[$m_sumur];
                        $awal = $input_post[$m_awal];
                        $akhir = $input_post[$m_akhir];
                        $vol = $input_post[$m_volume];
                        $sipa_no = $input_post[$m_sipa];

                        $this->db->query("insert into pad_air_tanah (sumur_ke, spt_id, sipa_no, awal, akhir, volume, create_uid, created, update_uid, updated) 
                                          values ($sumur, $spt_id, '$sipa_no' , $awal, $akhir, $vol,  $create_uid, '$created', $update_uid, '$updated')");
                    }

                        $tarif = $input_post['tarif'];

                        $this->db->query("delete from pad_air_tanah_hit where spt_id=$spt_id");

                        for($i=1; $i<=$input_post['max_hit']; $i++){
                        $volume = $input_post['hit_vol'.$i];
                        $hda = $input_post['hit_hda'.$i];
                        $jumlah = $input_post['hit_val'.$i];
                        $this->db->query("insert into pad_air_tanah_hit 
                                             (spt_id,  vol,      hda,  jumlah,  tarif,  update_uid,   updated) 
                                      values ($spt_id, $volume,  $hda, $jumlah, $tarif, $update_uid, '$updated')");
                    }
                }

                if ($p_usaha_id == pad_reklame_id()) {
                                    // data tambahan / detail
                    $dtKD = $this->input->post('dtKD');
                    $tambahan_data2 = array();
                    
                    if(isset($dtKD)) {
                        $i = 1;
                        $dtKD = json_decode($dtKD, true);
                        
                        if(count($dtKD['dtKD']) > 0){
                            $rd_row = array();
                            foreach($dtKD['dtKD'] as $rows) {
                                $rd_row = array (
                                    'spt_id' => $p_id,
                                    'media_id'  => $rows[0],
                                    'kelas_jalan_id'  => $rows[2],
                                    'panjang'  => $rows[4],
                                    'lebar' => $rows[5],
                                    'tinggi'  => $rows[6],
                                    'sisi'  => $rows[8],
                                    'banyak' => $rows[9],
                                    'lama' => $rows[10],
                                    'nsr' => pad_to_decimal($rows[11]),
                                    'tambahan' => pad_to_decimal($rows[12]),
                                    'alamat' => $rows[14],
                                    'status_baliho' => $rows[16],
                                    'jalan_id' => $rows[17], // taroh belakang takut merubah susunan
                                    'updated' => $updated,
                                    'update_uid' => $update_uid,
                                );
                                $i++;
                                $tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
                            }
                            
                            //langsung ajah dah - sementara
                            $query= "delete from pad_spt_reklame where spt_id = $p_id"; 
                            $this->db->query($query);
                            $this->db->insert_batch('pad_spt_reklame', $tambahan_data2);
                        }
                      }
                   } 



            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }

        $data['dt'] = $post_data;
        $get        = (object) $post_data;

        $options              = array();
        $js                   = 'id="customer_usaha_id" class="input-xlarge" readonly ';
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
        $js                   = 'id="pajak_id" class="form-control"';
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
        if ($row = $pajak_detail->sptpd_get_pajak_detail($get->pajak_id, $get->terimatgl, 0)) {
            $data['dt']['rekening_id'] = $row->rekening_id;
            $data['dt']['kode']  = $row->kode;
            $data['dt']['jatuhtempo']  = $row->jatuhtempo;
        } else {
            $data['dt']['kode']  = '';
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
                $options[$row->id] = $row->nama;
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
                $options[$row->id] = $row->nama;
            }}
            $js                    = 'id="r_lokasi_id" class="input-medium" required ';
            $data['select_lokasi'] = form_dropdown('r_lokasi_id', $options, $get->r_lokasi_id, $js);

            $options              = array(
                1 => 'Tidak ada',
                2 => 'Produk Rokok +25%',
                //3 => 'Reklame Pendidikan -25%',
                //4 => 'Kenaikan 25% & Pengurangan 25%'
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

    public function get_validasi()
    {
        $this->load_auth();
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
        $this->session->set_userdata('mode','edit');
        
        $p_usaha_id = $this->uri->segment(4);
        $p_type_id  = $this->uri->segment(5);
        $p_id       = $this->uri->segment(6);
        
        $this->session->set_userdata('usaha_id',$p_usaha_id );
        $sptpd = $this->sptpd_model->get($p_id);
        $tglinput = date('Y-m-d', strtotime($sptpd->created));

        /*
        if ($tglinput < date('Y-m-d')) {
            $this->session->set_flashdata('msg_warning', 'Aktivitas Edit diperbolehkan hanya dalam waktu 1 hari');
            redirect(active_module_url($this->controller));
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
            $data['dt']['type_id']            = $get->type_id;
            $data['dt']['so']                 = $get->so;
            $data['dt']['masadari']           = date('d-m-Y', strtotime($get->masadari));
            $data['dt']['masapajak_bulan']    = date('M-Y', strtotime($get->masadari));
            $masapajak_bulan                = date('M-Y', strtotime($get->masadari));
            $data['dt']['jatuhtempotgl']      = date('d-m-Y', strtotime ( '-1 day' , strtotime ( $data['dt']['masadari'] ) ) );

            $data['dt']['masasd']             = date('d-m-Y', strtotime($get->masasd));
            $data['dt']['minimal_omset']           = $get->minimal_omset;
            $data['dt']['dasar']              = $get->dasar;
            $data['dt']['pajak']              = $get->pajak_terhutang;

            $data['dt']['tarif']              = $get->tarif;

           if ($p_usaha_id==pad_air_tanah_id() || $p_usaha_id==pad_reklame_id()){
                $data['dt']['denda']              = $get->denda;
                $data['dt']['bunga']              = $get->bunga;
            }else{
                $data['dt']['denda']              = 0;
                $data['dt']['bunga']              = 0;
            }
            
            $data['dt']['setoran']            = $get->setoran;
            $data['dt']['kenaikan']           = $get->kenaikan;
            $data['dt']['kompensasi']         = $get->kompensasi;
            $data['dt']['lain2']              = $get->lain2;
            $data['dt']['air_manfaat_id']     = $get->air_manfaat_id;
            $data['dt']['air_zona_id']        = $get->air_zona_id;
            $data['dt']['meteran_awal']       = $get->meteran_awal;
            $data['dt']['meteran_akhir']      = $get->meteran_akhir;
            
            $data['dt']['r_bayarid']          = $get->r_bayarid;
            $data['dt']['r_tarifid']          = $get->r_tarifid;
            
            $data['dt']['enabled']            = $get->enabled;
            $data['dt']['unit_id']            = $get->unit_id;
            $data['dt']['customer_id']        = $get->customer_id;
            
            $data['dt']['isprint_dc']         = $get->isprint_dc;
            $data['dt']['notes']              = $get->notes;
            $data['dt']['rekening_id']        = $get->rekening_id;
            $data['dt']['cara_bayar']         = $get->cara_bayar;
            $bulan_telat                      = $get->bulan_telat;

            if (wp_login()) {           
                // data tambahan
                //
            }
            
            $data['dt']['nopd'] = $this->load->model('objek_pajak_model')->get_nopd($get->customer_usaha_id);

            $options              = array();
            $js                   = 'id="customer_usaha_id" class="input-xlarge" ';
            $data['select_usaha'] = form_dropdown('customer_usaha_id', $options, null, $js);

            $select_data = $this->load->model('pajak_model')->get_select($get->pajak_id);
            $options     = array();
            foreach ($select_data as $rows) {
                $options[$rows->id] = $rows->nama;
                if ($rows->id == $get->pajak_id){
                $nama_pajak = $rows->nama;
                }
            }
            $js                   = 'id="pajak_id" class="input-xxlarge"';
            $data['select_pajak'] = form_dropdown('pajak_id', $options, $get->pajak_id, $js);

            $select_data = $this->load->model('sptpd_type_model')->get_select();
            $options     = array();
            foreach ($select_data as $rows) {
                $options[$rows->id] = $rows->typenm;
            }
            $js                        = 'id="type_id" class="input-small" onChange="void(0);"';
            $data['select_sptpd_type'] = form_dropdown('type_id', $options, $get->type_id, $js);


            $select_data = array((object) array('id'=>2, 'cara_bayar'=>'ATM / TELLER'), 
                                 (object) array('id'=>1, 'cara_bayar'=>'TRANSFER'));
            $cara_bayar = '';
            if($select_data) {
            foreach ($select_data as $row) {
                if ($row->id == $data['dt']['cara_bayar'])
                    $cara_bayar .= "<option value={$row->id} selected >{$row->cara_bayar}</option>";
                else
                    $cara_bayar .= "<option value={$row->id}>{$row->cara_bayar}</option>";
            }};
            
            $trmtgl = (new DateTime($data['dt']['terimatgl']))->format('Y-m-d');          
            $masadari = (new DateTime($data['dt']['masadari']))->format('Y-m-d');
            $jtptgl =  (new DateTime($data['dt']['jatuhtempotgl'] ))->format('Y-m-d');  

            $val_data = $this->sptpd_model->get($p_id);
            $val_data->id = $data['dt']['id'];
            $val_data->masapajak_bulan = $masapajak_bulan;
            $val_data->nama_pajak = $nama_pajak;
            $val_data->cara_bayar = $cara_bayar;
            $val_data->pajak = $data['dt']['pajak'];
            $val_data->ijintgl_view = $data['dt']['terimatgl'];
            $val_data->persen_bunga = pad_bunga();
            $val_data->jatuhtempotgl_view = date('d-m-Y', strtotime($data['dt']['jatuhtempotgl']));  
            $val_data->terimatgl_view = date('d-m-Y', strtotime($data['dt']['terimatgl']));  
            $val_data->masadari_view = date('d-m-Y', strtotime($data['dt']['masadari']));  
           
            if ($p_usaha_id == pad_reklame_id()){
                if (strtotime($trmtgl) > strtotime($jtptgl)){
                    
                    $query = $this->db->query("select hit_jdendarek_real('$val_data->jatuhtempotgl_view','$val_data->terimatgl_view') as bulan_telat");
                    foreach ($query->result() as $row) {
                        $val_data->bulan_telat = $row->bulan_telat;
                        $bulan_telat_real =  $row->bulan_telat;
                    }
                    //cari interval
                    $query = $this->db->query("SELECT date '$jtptgl' + interval '$bulan_telat_real months' as jatuhtempo_rek_new");
                    foreach ($query->result() as $row) {
                        $newjtp=  $row->jatuhtempo_rek_new;
                    }
                    $val_data->jatuhtempotgl_rek = $newjtp;
                    $val_data->jatuhtempotgl_rek_view = date('d-m-Y', strtotime($newjtp));

                    if ($bulan_telat_real > 24){
                        $val_data->bulan_telat = 24;
                    }
                    $val_data->new_denda  = round(($val_data->dasar * $val_data->tarif) * (pad_bunga()/100) * $val_data->bulan_telat) ;

                    $val_data->pajak = round(($val_data->dasar * $val_data->tarif)  + $val_data->new_denda);
                }else{
                    $val_data->jatuhtempotgl_rek = $data['dt']['jatuhtempotgl'];
                    $val_data->jatuhtempotgl_rek_view = date('d-m-Y', strtotime($data['dt']['jatuhtempotgl']));
                    $val_data->bulan_telat = 0;
                    $val_data->new_denda  = 0;
                    $val_data->pajak = round(($val_data->dasar * $val_data->tarif)  + $val_data->new_denda);
                }
                $this->session->set_userdata('masadari_temp', $masadari);
            }else if($p_usaha_id == pad_air_tanah_id()){
                    $query = $this->db->query("select hit_jdendaat('$val_data->masadari_view','$val_data->terimatgl_view') as bulan_telat");
                    foreach ($query->result() as $row) {
                        $val_data->bulan_telat =  $row->bulan_telat;
                    }
                    $val_data->new_denda  = round($val_data->dasar * $val_data->tarif *(pad_bunga()/100) * $val_data->bulan_telat) ;
                    $val_data->pajak = round(($val_data->dasar * $val_data->tarif)  + $val_data->new_denda);
               $this->session->set_userdata('masadari_temp', $masadari);
            }


            echo json_encode($val_data);


            $pajak_detail = $this->load->model('pad_model');
            if ($row = $pajak_detail->sptpd_get_pajak_detail($get->pajak_id, $get->terimatgl, 0)) {
                $data['dt']['rekening_id'] = $row->rekening_id;
                $data['dt']['kode']  = $row->kode;
                $data['dt']['jatuhtempo']  = $row->jatuhtempo;
                $data['dt']['multiple'] = $row->multiple;

            }

            if ($p_usaha_id == pad_reklame_id() && $p_type_id == pad_dok_office_id()) {
                //
            } else if ($p_usaha_id == pad_air_tanah_id() && $p_type_id == pad_dok_office_id()) {
                //
            } else {
            }
        } else {
            show_404();
        }
    }


    public function proces_validasi()
    {
        $this->load_auth();
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->controller));
        }
        $controller     = 'sptpd'; //default
        $mode           = $this->input->post('mode');
        $cara_bayar     = $this->input->post('cara_bayar');
        $spt_id         = $this->input->post('spt_id');
        $jatuhtempotgl  = $this->input->post('jatuhtempotgl');
        $pajak          = $this->input->post('pajak');
        $source_nama    = 'pad_spt';
        $p_usaha_id     = $this->session->userdata('usaha_id');
        $prosestgl_skpd = $this->input->post('prosestgl_skpd');
        $petugas_id     = $this->input->post('petugas_id');
        $pejabat_id     = $this->input->post('pejabat_id');
        $type_id        = $this->input->post('type_ids');
        $bulan_telat    = $this->input->post('bulan_telat');
        $masadari       = $this->session->userdata('masadari_temp');

        if ($mode=='proses_skpd'){
            $controller = 'skpd';
            $this->proses_skpd($p_usaha_id , $spt_id,$prosestgl_skpd, $petugas_id, $pejabat_id,$type_id ); //PROSES SKPD
        }
        else if ($mode=='validate' ){
            $controller = 'sptpd';
        }

        if($mode=='validate' || $mode=='proses_skpd'){

        /*
        if ($this->sptpd_model->is_kohir_ok($spt_id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
             redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
        }
        */

        if ($this->sptpd_model->is_bayar($spt_id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
             redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
        }
         // Validasi
        if ($this->sptpd_model->is_validasi_ok($spt_id)) {
            $this->session->set_flashdata('msg_warning', 'Data sudah divalidasi sebelumnya');
            redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
        }

        $proses = 2 ; //sudah validasi, 1 belum
        $tgl = date('Y-m-d h:i:s');

         if($p_usaha_id==pad_air_tanah_id() || $p_usaha_id==pad_reklame_id()){
             $bunga      = $this->input->post('denda'); //khusus office bunga dinsert (DENDA)
             $query = $this->db->query("update pad_spt 
                          set cara_bayar = $cara_bayar , proses = $proses, tanggal_validasi = '$tgl', denda=0, bunga=$bunga ,bulan_telat=$bulan_telat , pajak_terhutang=$pajak 
                          where id= $spt_id");
         }
         else{
             $query = $this->db->query("update pad_spt 
                          set cara_bayar = $cara_bayar , proses = $proses, tanggal_validasi = '$tgl' , denda=0, bunga=0 
                          where id= $spt_id"); //khusus self
        }

        $query = $this->db->query("select s.id as source_id, 
                        s.tahun, u.id as usaha_id, get_invno(s.id) as invoice_no, s.masadari, 
                        u.nama as jenis_usaha, jp.nama as jenis_pajak,
                        get_npwpd(c.id,true) as npwpd, c.nama as nama_wp, 
                        c.alamat as alamat_wp, cu.opnm as op_nama, cu.opalamat as op_alamat, 
                        s.tahun||lpad(u.id::text, 2, '0')||lpad(get_invno(s.id)::text, 5, '0') as nomor_tagihan,
                        s.dasar as pokok, s.denda, s.bunga, s.pajak_terhutang as total, 0 as status_bayar,
                        s.jatuhtempotgl as jatuh_tempo,
                        extract(year from s.masadari)||lpad(extract(month from s.masadari)::text, 2, '0') periode,
                        left(get_rekening(r.kode)::text,11) as rekening_pokok, r.nama as nama_pokok,
                        left(get_rekening(d.kode)::text,11) as rekening_denda, d.nama as nama_denda
                        from pad_spt s 
                        inner join pad_customer c on c.id=s.customer_id 
                        inner join pad_customer_usaha cu on cu.id=s.customer_usaha_id 
                        inner join pad_usaha u on u.id=cu.usaha_id 
                        inner join pad_jenis_pajak jp on jp.id=s.pajak_id 
                        inner join pad_rekening r on r.id=s.rekening_id
                        inner join pad_rekening d on d.id=jp.rekdenda_id
                        where s.id=$spt_id");



        foreach ($query->result() as $row)
        {
           $source_id   = $row->source_id;
           $tahun       = $row->tahun;
           $npwpd       = $row->npwpd;
           $nama_wp     = $row->nama_wp;
           $alamat_wp   = $row->alamat_wp;
           $op_nama      = $row->op_nama;
           $op_alamat    = $row->op_alamat;
           $rekening_pokok  = str_replace(".","",$row->rekening_pokok);//rekening pokok
           $nama_pokok  = $row->nama_pokok; //nama pokok
           $invoice_no     = $row->invoice_no; 
           $usaha_id    = $row->usaha_id;
           $spt_id      = $spt_id;
           $jenis_usaha = $row->jenis_usaha;
           $jenis_pajak = $row->jenis_pajak;
           $nomor_tagihan = $row->nomor_tagihan;
           $status_bayar =$row->status_bayar;
           $periode     = $row->periode;
           $rekening_denda = str_replace(".","",$row->rekening_denda);
           $nama_denda  = $row->nama_denda;
           
           $jatuh_tempo = $row->jatuh_tempo;
           $pokok = $row->pokok;
           if ($usaha_id==pad_air_tanah_id() || $usaha_id==pad_reklame_id())
           {
               $denda = $row->denda;
               $bunga = $row->bunga;
               if ($usaha_id==pad_reklame_id()){
                $jatuh_tempo = $jatuhtempotgl;
                }

               $total = $pajak;
           }else{
               $denda = 0;
               $bunga = 0;
               $total = $row->total;
           }
           

        }


            $update_data = array(
                            'tahun' => $tahun,
                            'usaha_id' => $usaha_id,
                            'invoice_no' => $invoice_no,
                            'source_id' => $source_id,
                            'source_nama' => $source_nama,  
                            'jatuh_tempo' => $jatuhtempotgl,
                            'nomor_tagihan' => $nomor_tagihan,
                            'jenis_usaha' => $jenis_usaha, //pad_usaha.nama
                            'jenis_pajak' => $jenis_pajak, //pad_jenis_pajak.nama
                            'pokok' => $pokok,
                            'denda' => $denda,
                            'bunga' => $bunga,
                            'total' => $total,
                            'npwpd' => $npwpd,
                            'nama_wp' => $nama_wp,
                            'alamat_wp' => $alamat_wp,
                            'alamat_op' => $op_alamat,
                            'nama_op' => $op_nama,
                            'status_bayar' => 0,
                            'rekening_pokok' => $rekening_pokok,
                            'rekening_denda' => $rekening_denda,
                            'periode' => $periode, //Desember 2015
                            'nama_pokok' => $nama_pokok,
                            'nama_denda' => $nama_denda,
                            'created' => date('Y-m-d h:i:s'),
                            'create_uid' => sipkd_user_id(),
                        );
            $save = $this->invoice_model->save($update_data);
                            
            if ($query && $save) {
                if ($mode=='proses_skpd' || $mode=='recall'){
                    $this->session->set_userdata('rpt_skpd', 1);
                    $this->session->set_userdata('id_skpd', $spt_id);

                    $query = $this->db->query("update pad_spt 
                          set jatuhtempotgl = '$jatuhtempotgl' where id= $spt_id");
                }
                else{
                    $this->session->set_userdata('rpt_skpd', 0);
                }

        
                if($this->session->userdata('skpd_tipe')=='SKPDKB'){
                 $this->session->set_flashdata('msg_success', 'Proses SKPDKB Berhasil');
                 redirect(active_module_url("{$controller}/kb/{$p_usaha_id}"));
                }
                else if($this->session->userdata('skpd_tipe')=='SKPDKBT'){
                 $this->session->set_flashdata('msg_success', 'Proses SKPDKBT Berhasil');
                 redirect(active_module_url("{$controller}/kbt/{$p_usaha_id}"));               
                }
                else{
                 $this->session->set_flashdata('msg_success', 'Data berhasil divalidasi');
                 redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
                }

            }
            else{

                if($this->session->userdata('skpd_tipe')=='SKPDKB'){
                 $this->session->set_flashdata('msg_warning', 'Proses SKPDKB Gagal');
                 redirect(active_module_url("{$controller}/kb/{$p_usaha_id}"));
                }
                else if($this->session->userdata('skpd_tipe')=='SKPDKBT'){
                 $this->session->set_flashdata('msg_warning', 'Proses SKPDKBT Gagal');
                 redirect(active_module_url("{$controller}/kbt/{$p_usaha_id}"));               
                }
                else{
                 $this->session->set_flashdata('msg_warning', 'Data Gagal divalidasi');
                 redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
                }
            }
        }

        if($mode=='cancel' || $mode=='recall'){

        if ($mode=='recall'){
            $controller = 'skpd';
            $newjtp = strtotime ( '-1 day' , strtotime ($masadari) ) ;
            $jatuhtempotgl = date ( 'Y-m-d' , $newjtp );
            $this->db->query("update pad_spt 
              set jatuhtempotgl = '$jatuhtempotgl' where id= $spt_id");

            $this->recall_skpd($p_usaha_id , $spt_id); //PROSES SKPD

        }
        else if ($mode=='cancel' ){
            $controller = 'sptpd';
        }

        $proses = 1 ; //batalkan validasi
        $updated = date('Y-m-d h:i:s');
        $update_uid = sipkd_user_id();

        if($p_usaha_id==pad_reklame_id()){
            $query = $this->db->query("select pajak_terhutang, bunga from pad_spt where id=$spt_id");
            foreach ($query->result() as $row)
            {
              $pajak_terhutang   = $row->pajak_terhutang;
              $bunga = $row->bunga;
            }
            $pajak = $pajak_terhutang - $bunga ;
            $query = $this->db->query("update pad_spt 
                      set cara_bayar = $cara_bayar , proses = $proses, pajak_terhutang=$pajak , bunga=0, 
                      updated = '$updated', update_uid = $update_uid  
                      where id= $spt_id");
        }
        else {
            $query = $this->db->query("update pad_spt 
                                  set cara_bayar = $cara_bayar , proses = $proses, 
                                  updated = '$updated', update_uid = $update_uid  
                                  where id= $spt_id");
        }

        $query_delete = $this->db->query("delete from pad_invoice 
                         where source_id=$spt_id and source_nama='$source_nama'");

                            
            if ($query && $query_delete) {
                $this->session->set_userdata('rpt_skpd', 0);
                
                if($this->session->userdata('skpd_tipe')=='SKPDKB'){
                 $this->session->set_flashdata('msg_success', 'Recall Berhasil');
                 redirect(active_module_url("{$controller}/kb/{$p_usaha_id}"));
                }
                else if($this->session->userdata('skpd_tipe')=='SKPDKBT'){
                 $this->session->set_flashdata('msg_success', 'Validasi Dibatalkan');
                 redirect(active_module_url("{$controller}/kbt/{$p_usaha_id}"));               
                }
                else{
                 $this->session->set_flashdata('msg_success', 'Validasi Dibatalkan');
                 redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
                }
            }
            else{
                 $this->session->set_flashdata('msg_danger', 'Data Gagal dibatalkan');
                 redirect(active_module_url("{$controller}/index/{$p_usaha_id}"));
            }
        }

    }

    public function delete()
    {

        $id = $this->uri->segment(4);
        $p_usaha_id =  $this->uri->segment(5);
		$this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
		
		//cek kohir

        if ($this->sptpd_model->is_kohir_ok($id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }


                        // kalau user sa boleh edit. 17-10-2014 (AA)
        if ($this->sptpd_model->is_bayar($id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }

                // Validasi
        //if($p_usaha_id != 4){
        //if($p_usaha_id != 7){
        if ($this->sptpd_model->is_validasi_ok($id)) {
            $this->session->set_flashdata('msg_warning', 'Data tidak dapat diubah karena sudah divalidasi, Harap unvalidasi jika ingin mengubah data');
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
        //}}
        

        if ($id && $this->sptpd_model->get($id)) {
            if($p_usaha_id == 7){
                $query = $this->db->query("delete from pad_air_tanah where spt_id = $id");
                $query = $this->db->query("delete from pad_air_tanah_hit where spt_id = $id");
            }
            $this->sptpd_model->delete($id);
            $this->skpd_model->delete_by_spt($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        } else {
            show_404();
        }
    }

	// SK
	public function proses_skpd( $usaha_id , $spt_id , $tgl_proses, $petugas_id, $pejabat_id, $type_id )
	{
		$this->load_auth();
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url("{$this->controller}/index/{$p_usaha_id}"));
        }
		
        if ($usaha_id && $spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {
			//$kohirno = $this->skpd_model->generate_kohirno(pad_tahun_anggaran(), $usaha_id);

            $query = $this->db->query("select get_nokohir($spt_id) as kohirno;");
            foreach ($query->result() as $row){ $kohirno = $row->kohirno;} ;

			$update_data = array (
				'spt_id' => $spt_id,
				'kohirno' => $kohirno,
				'usaha_id' => $usaha_id,
				'tahun' => pad_tahun_anggaran(),
				'kohirtgl' => date('Y-m-d', strtotime($tgl_proses)),
                'petugas_id' => $petugas_id,
                'pejabat_id' => $pejabat_id,
				'enabled' => 1,
				'created' => date('Y-m-d h:i:s'),
				'create_uid' => sipkd_user_id(),
                'type_id' => $type_id
			);
			$this->skpd_model->save($update_data);
			
            //update jth tempo 
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
            if(substr($rek->kode,0,5)=='41104') {
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
            
            if(substr($rek->kode,0,5)=='41108') {
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
	
	public function recall_skpd($usaha_id, $spt_id)
	{
		$this->load_auth();
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url("{$this->controller}/index/{$usaha_id}"));
        }
		
		// cek pmb
        if ($this->sptpd_model->is_bayar($spt_id)) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
             redirect(active_module_url("{$this->controller}/index/{$usaha_id}"));
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
			$rows = $model->sptpd_get_cu($c_id, $cu_id);
		else {
			$rows = $model->sptpd_get_cu($c_id, $cu_id);
		}
		
        $usaha   = '';
        $cu_data = new stdClass();
        if ($rows) {
            $cu_data->customer_id       = $rows[0]->customer_id;
            $cu_data->customernm        = $rows[0]->customernm;
            $cu_data->npwpd             = $rows[0]->npwpd2;
            $cu_data->so                = $rows[0]->so;
            $cu_data->konterid          = $rows[0]->konterid;
            $cu_data->zona_id           = $rows[0]->zona_id;
            $cu_data->manfaat_id        = $rows[0]->manfaat_id;
            $cu_data->golongan_id       = $rows[0]->golongan_id;
            $cu_data->kelompok_usaha_id = $rows[0]->kelompok_usaha_id;

            $cu_data->wpnama = $rows[0]->wpnama;
            $cu_data->wpalamat = $rows[0]->wpalamat;


            //JUST INIT
            $kelompok_usaha = 0;
            $manfaat = 0;
            $zona = 0;

            $selected = "selected";
            foreach ($rows as $row) {
                if ($cu_id == $row->customer_usaha_id) {
                    $usaha .= "<option value={$row->customer_usaha_id} selected>{$row->usahanm}</option>";
                } else {
                    $usaha .= "<option value={$row->customer_usaha_id}>{$row->usahanm}</option>";
                }
            }
            $cu_data->usaha = $usaha;

            //AT Info
            $ku_id = $cu_data->kelompok_usaha_id;
            if($ku_id==''){
                $kelompok_usaha = '-';
            }
            else{
            $query = $this->db->query("select nama from pad_air_kelompok_usaha where id= $ku_id");
            foreach ($query->result() as $row){
                    $kelompok_usaha = $row->nama;
                }
            }
            $cu_data->kelompok_usaha = $kelompok_usaha;

            $mn_id = $cu_data->manfaat_id;
            if($mn_id==''){
                $manfaat = '-';
            }
            else{
            $query = $this->db->query("select nama from pad_air_manfaat where id= $mn_id");
            foreach ($query->result() as $row){
                    $manfaat = $row->nama;
                }
            }
            $cu_data->manfaat = $manfaat;

            $zn_id = $cu_data->zona_id;
            if($mn_id==''){
                $zona = '-';
            }
            else{
            $query = $this->db->query("select nama from pad_air_zona where id= $zn_id");
            foreach ($query->result() as $row){
                    $zona = $row->nama;
                }
            }
            $cu_data->zona = $zona;

            if ($cu_data->golongan_id==1)
                $golongan = 'ABT';
            else if ($cu_data->golongan_id==2)
                $golongan = 'AP';
            else
                $golongan = '-';
            
            $cu_data->golongan = $golongan;
            //Get Bulan SPT Pajak yg Terakhir
            $month_ini = new DateTime("first day of last month");
            $query = $this->db->query("select max(masadari) as max_masa from pad_spt 
                                       where customer_id=$c_id and customer_usaha_id=$cu_id and pajak_id=$u_id");
            foreach ($query->result() as $row){
                     $max_masa = $row->max_masa;
                }    
            $max_masa_f  = date('d-m-Y', strtotime($max_masa));
            $month_ini_f = $month_ini->format('d-m-Y');

            if($max_masa_f==$month_ini_f)
            {
                $cu_data->masadari = $month_ini_f;
                $cu_data->masapajak_bulan = $month_ini->format('M-Y');
            }
            else if($max_masa != '')
            {
                $cu_data->masadari        = date('d-m-Y', strtotime("$max_masa + 1 month"));
                $cu_data->masapajak_bulan = date('M-Y', strtotime($cu_data->masadari));
            }
            else
            {
                $cu_data->masadari = $month_ini_f;
                $cu_data->masapajak_bulan = $month_ini->format('M-Y'); 
            }

            echo json_encode($cu_data);
        }
    }

    function get_cu_edit()
    {
        $c_id  = $this->uri->segment(4);
        $cu_id = $this->uri->segment(5);
        $u_id  = $this->uri->segment(6); // param untuk wp_login (opsional di menu default)
        
        $model = $this->load->model('pad_model');
        $rows = $model->sptpd_get_cu($c_id, $u_id);
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

    function get_pajak()
    {
        $cu_id    = $this->uri->segment(4);
        $pajak_id    = $this->uri->segment(5);
        $model    = $this->load->model('pad_model');
		$usaha_id = $model->sptpd_get_usaha_id($cu_id);

        //if ($rows = $model->sptpd_get_pajak($usaha_id,$pajak_id)) {
        if ($rows = $model->sptpd_get_pajak($usaha_id)) {
			$pajak = "";
            foreach ($rows as $row) {
                if($row->usaha_id == pad_reklame_id())
                    $pajak .= "<option value={$row->id}>{$row->nama} = ".number_format($row->reklame ,0,',','.')."</option>";
                else
                    if($row->id == $pajak_id)
                    $pajak .= "<option value={$row->id} selected>{$row->nama} (Default)</option>";
                    //else
                    //$pajak .= "<option value={$row->id} >{$row->nama}</option>";

            }
            echo $pajak;
        }
    }

    function get_sumur()
    {
        $cu_id    = $this->uri->segment(4);
        $spt_id   = $this->uri->segment(5);
        $model    = $this->load->model('pad_model');
        $usaha_id = $model->sptpd_get_usaha_id($cu_id);
        $sumur_data = $this->load->model('customer_usaha_air_tanah_model')->get_select($cu_id);
        $sumur = "";
        $end_loop = "";
        $total_volume = "";
        $i=0;

        if($spt_id=='x'){
         foreach ($sumur_data as $row) {
            $i=$i+1;
            $sumur .="<div class='col-sm-1'>
                      <input class='form-control' style='text-align:center;' name='sumur_ke".$i."' id='sumur_ke".$i."' type='text' readonly value={$row->sumur_ke} >
                      </div>
                      <div class='col-sm-2'>
                      <input class='form-control' style='text-align:right;' name='sipa_no".$i."' id='sipa_no".$i."' type='text' readonly value={$row->sipa_no} >
                      </div>
                      <div class='col-sm-3'>
                      <input class='form-control' style='text-align:right;' name='meteran_awal".$i."' id='meteran_awal".$i."' type='text' value='0'>
                      </div>
                      <div class='col-sm-3'>
                      <input class='form-control' style='text-align:right;' name='meteran_akhir".$i."' id='meteran_akhir".$i."' type='text' value='0'>
                      </div>
                      <div class='col-sm-3'>
                      <input class='form-control' style='text-align:right;' name='volume".$i."' id='volume".$i."' type='text' value='0'>
                      </div>
                      <br>";          
            }
            } else {
            $query = $this->db->query("select*from pad_air_tanah where spt_id = $spt_id ");
            foreach ($query->result() as $row){
            $i=$i+1;

            $awal = number_format($row->awal ,3,",",".");
            $akhir = number_format($row->akhir ,3,",",".");
            $volume = number_format($row->volume ,3,",",".");

            $sumur .="<div class='col-sm-1'>
                      <input class='form-control' style='text-align:center;' name='sumur_ke".$i."' id='sumur_ke".$i."' type='text' readonly value={$row->sumur_ke} >
                      </div>
                      <div class='col-sm-2'>
                      <input class='form-control' style='text-align:right;' name='sipa_no".$i."' id='sipa_no".$i."' type='text' readonly value={$row->sipa_no} >
                      </div>
                      <div class='col-sm-3'>
                      <input class='form-control' style='text-align:right;' name='meteran_awal".$i."' id='meteran_awal".$i."' type='text' value={$awal}>
                      </div>
                      <div class='col-sm-3'>
                      <input class='form-control' style='text-align:right;' name='meteran_akhir".$i."' id='meteran_akhir".$i."' type='text' value={$akhir}>
                      </div>
                      <div class='col-sm-3'>
                      <input class='form-control' style='text-align:right;' name='volume".$i."' id='volume".$i."' type='text' value={$volume}>
                      </div>
                      <br>";          
        }
       }
        echo $sumur;
        $end_loop .="<input name='end_loop_sumur' id='end_loop_sumur' type='text' value={$i} readonly>";
        $total_volume .= "<div class='col-sm-7'></div>
                          <label class='control-label col-sm-2'>TOTAL : </label>
                          <div class='col-sm-3'>
                          <input class='form-control' style='text-align:right;' name='total_volume' id='total_volume' type='text' readonly>
                          </div>";
        echo $total_volume;
        echo $end_loop;

    }

    public function hitung_npa()
    {
        $golongan   = $this->uri->segment(4);
        $zona_id   = $this->uri->segment(5);
        $kelompok_id   = $this->uri->segment(6);
        $vol_max = $this->uri->segment(7);

        $query = $this->db->query("SELECT h.id, z.id as zona_id, k.id as kelompok_id, h.golongan, h.volume, h.hda
                                    FROM pad_air_hda h
                                    JOIN pad_air_zona z ON h.zona_id = z.id
                                    JOIN pad_air_kelompok_usaha k ON h.kelompok_usaha_id = k.id
                                    WHERE zona_id = $zona_id
                                    AND kelompok_usaha_id = $kelompok_id
                                    AND golongan = $golongan
                                    ORDER BY h.volume ASC");
        $vol = 0;
        $hda = 0;
        $npa = 0;
        $val = 0;
	$vol1 = 0;
        $vol_old = 0;
        $view_npa  ="";
        $i=0;
        foreach ($query->result() as $row)
        {
           $i++;
           $vol = $row->volume;                              
           $vol_old = $row->volume;
           $hda = $row->hda;                            
            if ($vol_max > ($vol_old - $vol1)){
		$vol = $vol - $vol1;
                $npa = $npa + ($vol * $hda); 
                $val = $vol * $hda;                            
                $vol_max = $vol_max - $vol;
		$vol1 = $vol1 + $vol;
            //VIEW 
               $view_npa .=  "<div class='col-sm-3'>".
               $vol." m<sup>3 </sup></div> 
               <input type='hidden' id='hit_vol$i' name='hit_vol$i' value=$vol > 
               <div class='col-sm-1'> x </div>
               <div class='col-sm-3'>Rp. ".number_format($hda,0,',','.')."</div>
               <input type='hidden' id='hit_hda$i' name='hit_hda$i' value=$hda > 
               <div class='col-sm-1'> = </div>
               <div class='col-sm-4'>Rp. ".number_format($val,0,',','.').",-</div>
               <input type='hidden' id='hit_val$i' name='hit_val$i' value=$val > "; 
                }                          
            else{                                
                $npa = $npa + ($vol_max * $hda);
                $val = $vol_max * $hda; 
               //VIEW
               $view_npa .=  "<div class='col-sm-3'>".
               $vol_max." m<sup>3 </sup></div> 
               <input type='hidden'  id='hit_vol$i' name='hit_vol$i' value=$vol_max > 
               <div class='col-sm-1'> x </div>
               <div class='col-sm-3'>Rp. ".number_format($hda,0,',','.')."</div>
               <input type='hidden'  id='hit_hda$i' name='hit_hda$i' value=$hda > 
               <div class='col-sm-1'> = </div>
               <div class='col-sm-4'>Rp. ".number_format($val,0,',','.').",-</div>
               <input type='hidden' id='hit_val$i' name='hit_val$i' value=$val >
               <div class='col-sm-4'></div>
               <div class='col-sm-3'><b>Total</b> </div>
               <div class='col-sm-1'> = </div>
               <div class='col-sm-4'><b>Rp. ".number_format($npa,0,',','.').",-</b></div>
               <input type='hidden' name='final_npa' id='final_npa' value='$npa' />"; 
               break; 
           }                
        }
        $view_npa .= "<input type='hidden' name='max_hit' id='max_hit' value='$i' />";
        echo $view_npa;
        echo "<br>"; 
    }

    function get_detail_npa(){
        $spt_id   = $this->uri->segment(4);
        $query = $this->db->query("select * from pad_air_tanah_hit where spt_id = $spt_id ");
        $view_npa = "";
        $dasar = 0;
        foreach ($query->result() as $row) {
               $vol = $row->vol;                              
               $hda = $row->hda; 
               $jumlah = $row->jumlah;
               $dasar = $dasar + $jumlah;
               $view_npa .=   "<div class='col-sm-3'>".$vol." m<sup>3 </sup></div> 
                               <div class='col-sm-1'> x </div>
                               <div class='col-sm-3'>Rp. ".number_format($hda,0,',','.')."</div>
                               <div class='col-sm-1'> = </div>
                               <div class='col-sm-4'>Rp. ".number_format($jumlah,0,',','.').",-</div>"; 
        }

       $view_npa .=   "<hr></hr><div class='col-sm-3'></div> 
               <div class='col-sm-1'></div>
               <div class='col-sm-3'><b>NPA </b></div>
               <div class='col-sm-1'> = </div>
               <div class='col-sm-4'>Rp. ".number_format($dasar,0,',','.').",-</div>"; 

        echo $view_npa;
    }

    function get_nsr($jalan_kelas_id, $media_id)
    {
        $date_now = date('Y-m-d');
        $nsr_data = new stdClass();
        $nsr = null;
        $masa = null;
        $masatext = '';
        $query = $this->db->query("SELECT n.id, k.id as kelas_id, m.id as media_id, n.nsr, m.masa, n.tmt 
                                    FROM pad_reklame_nsr n
                                    JOIN pad_reklame_media m ON n.media_id = m.id
                                    JOIN pad_reklame_kelas_jalan k ON n.kelas_jalan_id = k.id
                                    WHERE k.id=$jalan_kelas_id AND m.id=$media_id AND n.enabled =1 AND (n.tmt < '$date_now')");
        foreach ($query->result() as $row) {
            $nsr = $row->nsr;
            $masa = $row->masa;
            if ($masa==0){
                $masatext = 'Tahun';
                $jenismasa = 'TAHUNAN';
            }
            else if ($masa==1){
                $masatext = 'Bulan';
                $jenismasa = 'BULANAN';
            }
            else if ($masa==2){
                $masatext = 'Minggu';
                $jenismasa = 'MINGGUAN';
            }
            else{
                $masatext = 'Hari';
                $jenismasa = 'HARIAN';
            }

        }

        $nsr_data->nsr = $nsr;
        $nsr_data->masa = $masa;
        $nsr_data->masatext = $masatext;
        $nsr_data->jenismasa = $jenismasa;

        echo json_encode($nsr_data);

    }

    public function get_jdendarek($tgl_jtempo, $tgl_terima)
    {
        $bulan_telat_data = new stdClass();
        $query = $this->db->query("select hit_jdendarek('$tgl_jtempo','$tgl_terima') as bulan_telat");
        foreach ($query->result() as $row) {
            $bulan_telat = $row->bulan_telat;
        }
        $ijin_no = $query->num_rows();
        $bulan_telat_data->bulan_telat = $bulan_telat;
        echo json_encode($bulan_telat_data);
    }

    public function get_jdendaat($tgl_jtempo, $tgl_terima)
    {
        $bulan_telat_data = new stdClass();
        $query = $this->db->query("select hit_jdendaat('$tgl_jtempo','$tgl_terima') as bulan_telat");
        foreach ($query->result() as $row) {
            $bulan_telat = $row->bulan_telat;
        }
        $ijin_no = $query->num_rows();
        $bulan_telat_data->bulan_telat = $bulan_telat;
        echo json_encode($bulan_telat_data);
    }

    function cek_ijin($ijin_no)
    {
        $date_now = date('Y-m-d');
        $ijin_no_data = new stdClass();
        //$ijin_no = null;
        $query = $this->db->query("select ijin_no from pad_spt where ijin_no = '$ijin_no';");
        foreach ($query->result() as $row) {
            $ijin_no = $row->ijin_no;
        }
        $ijin_no = $query->num_rows();
        $ijin_no_data->ijin_no = $ijin_no;
        echo json_encode($ijin_no_data);
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
                $jalan .= "<option value={$row->id}>{$row->nama}</option>";
            }
            echo $jalan;
        }
    }
    
   function get_media_reklame()
    {
        $jenis_pajak_id = $this->uri->segment(4);
        $kelas_jalan_id = $this->uri->segment(5);
        $masadari = $this->uri->segment(6);
        //On Edit
        $media_id = $this->uri->segment(7);


        $model = $this->load->model('reklame_media_model');
        if($media_id ==''){
            if ($rows = $model->get_select_on_spt($jenis_pajak_id, $kelas_jalan_id, $masadari)) {
                $media = "";
                $media .= "<option value=0>Pilih</option>";
                foreach ($rows as $row) {
                    $media .= "<option value={$row->id}>{$row->media}</option>";
                }
                echo $media;
            }
        }else{
            if ($rows = $model->get_select_on_spt($jenis_pajak_id, $kelas_jalan_id, $masadari)) {
                $media = "";
                $media .= "<option value=0>Pilih</option>";
                foreach ($rows as $row) {
                if($media_id == $row->id)    
                    $media .= "<option value={$row->id} selected>{$row->media}</option>";
                else
                    $media .= "<option value={$row->id}>{$row->media}</option>";
                }
                echo $media;
            }
        }
    }

    function get_jalan_reklame()
    {
        $nama_jalan ="";
        $jalan_id = $this->uri->segment(4);
        $query = $this->db->query("select nama from pad_reklame_jalan where id = $jalan_id");
        foreach ($query->result() as $row){
        {$nama_jalan = $row->nama;}
        }
        echo $nama_jalan;
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
        $minimal_omset = $this->uri->segment(6);
        $model     = $this->load->model('pad_model');

        if ($row = $model->sptpd_get_pajak_detail($pajak_id, $pajak_tmt, $minimal_omset)) {
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

	function cetak_old() {		
		$type = $this->uri->segment(4);

		$qs   = urldecode ($_SERVER['QUERY_STRING']);
		parse_str($qs, $qs_data);
		
		$params = array();
		// foreach ($qs_data as $key => $val)
			// $params[$key] = $val;
					
		$sptpd  = $this->load->model('sptpd_model')->get($this->input->get('id'));
		$cu = $this->load->model('objek_pajak_model')->get($sptpd->customer_usaha_id);
        $report = $this->load->model('report_model')->get_report($cu->usaha_id, $sptpd->type_id);
        $rpt = $this->input->get('rpt');
		switch ($rpt) {
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

		// $rpt = $this->module.'/'.$rpt;
		
		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, false);
	}

    function cetak() {      
        $type = $this->uri->segment(4);
                    
        $rpt     = $this->input->get('rpt');
        $kondisi = "";
        
        if($rpt=='sptpd_tr') {
            $wp_id = $this->input->get('w');
            if(!empty($wp_id))
                $kondisi .= " AND c.id={$wp_id} ";
            $type_id = $this->input->get('t');
            if($type_id!=999)
                $kondisi .= " AND st.id={$type_id} ";
            $usaha_id= $this->input->get('u');
            if($usaha_id!=999)
                $kondisi .= " AND u.id={$usaha_id} ";
            
            $kondisi .= " AND s.tahun=".pad_tahun_anggaran();
                            
            $params = array(
                "daerah" => pad_pemda_daerah(),
                "dinas" => pad_pemda_nama(),         
                'kondisi' => (string)$kondisi,
            );
        } elseif($rpt=='sptpd_omset') {
            $rpt = 'sptpd_rincian_omset';
            $sptpd_id = $this->input->get('no');
            $terbilang = terbilang(pad_to_decimal($this->input->get('val')), 3) . " Rupiah";
            $params = array(
                'sptpd_id' => (int)$sptpd_id,
                "terbilang" => $terbilang,

                "alamat" => pad_pemda_alamat(),
                "telp" => pad_pemda_telp(),
                "fax" => pad_pemda_fax(),
                "website" => pad_pemda_website(),
                "email" => pad_pemda_email(),
                "alamat_lengkap" => pad_pemda_alamat_lengkap(),

                "daerah" => pad_pemda_daerah(),
                "dinas" => pad_pemda_nama(),
        "logo" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/logorpt__.jpg",
        "logobjb" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/bank-bjb.jpg",
                //"logo" => base_url('assets/img/logorpt__.jpg'),
                //"logobjb" => base_url('assets/img/bank-bjb.jpg'),
            );

        } elseif($rpt=='reklame_skpd') {

            $rpt = 'reklame_skpd';
            $sptpd_id = $this->input->get('id');

            $query = $this->db->query("select pajak_terhutang from pad_spt where id=$sptpd_id");
            foreach ($query->result() as $row){$pajak_terhutang = $row->pajak_terhutang;}

            $terbilang = terbilang(pad_to_decimal($this->input->get('val')), 3) . " Rupiah";
                $params = array(
                    'spt_id' => intval($sptpd_id),
                    'sptpd_id' => (int)$sptpd_id,
                    //"terbilang" => $sptpd->pajak_terhutang,
                    
                    "terbilang" => strtoupper(terbilang($pajak_terhutang)),
                    
                    "alamat" => pad_pemda_alamat(),
                    "telp" => pad_pemda_telp(),
                    "fax" => pad_pemda_fax(),
                    "website" => pad_pemda_website(),
                    "email" => pad_pemda_email(),
                    "alamat_lengkap" => pad_pemda_alamat_lengkap(),

                    "daerah" => pad_pemda_daerah(),
                    "dinas" => pad_pemda_nama(),
                    "logo" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/logorpt__.jpg",
                    "logobjb" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/bank-bjb.jpg",
                );

        } elseif($rpt=='airtanah_skpd') {

            $rpt = 'airtanah_skpd';
            $sptpd_id = $this->input->get('id');

            $query = $this->db->query("select pajak_terhutang from pad_spt where id=$sptpd_id");
            foreach ($query->result() as $row){$pajak_terhutang = $row->pajak_terhutang;}

            $terbilang = terbilang(pad_to_decimal($this->input->get('val')), 3) . " Rupiah";
                $params = array(
                    'spt_id' => intval($sptpd_id),
                      "terbilang" => strtoupper(terbilang($pajak_terhutang)),

                    "alamat" => pad_pemda_alamat(),
                    "telp" => pad_pemda_telp(),
                    "fax" => pad_pemda_fax(),
                    "website" => pad_pemda_website(),
                    "email" => pad_pemda_email(),
                    "alamat_lengkap" => pad_pemda_alamat_lengkap(),

                    "daerah" => pad_pemda_daerah(),
                    "dinas" => pad_pemda_nama(),
                    "logo" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/logorpt__.jpg",
                    "logobjb" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/bank-bjb.jpg",
                );

        }elseif($rpt=='sptpd') {

            $rpt = 'spt_00';
            $sptpd_id = $this->input->get('id');
            $terbilang = terbilang(pad_to_decimal($this->input->get('val')), 3) . " Rupiah";
                $params = array(
                    'spt_id' => intval($sptpd_id),
                     "terbilang" => strtoupper(terbilang($sptpd->pajak_terhutang)),

                    "alamat" => pad_pemda_alamat(),
                    "telp" => pad_pemda_telp(),
                    "fax" => pad_pemda_fax(),
                    "website" => pad_pemda_website(),
                    "email" => pad_pemda_email(),
                    "alamat_lengkap" => pad_pemda_alamat_lengkap(),

                    "daerah" => pad_pemda_daerah(),
                    "dinas" => pad_pemda_nama(),
                    "logo" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/logorpt__.jpg",
                    "logobjb" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/bank-bjb.jpg",
                );

        } 
        else {
            $rpt = 'sptpd_slip';
            $sptpd_id = $this->input->get('no');
            $terbilang = terbilang(pad_to_decimal($this->input->get('val')), 3) . " Rupiah";
            $params = array(
                'sptpd_id' => (int)$sptpd_id,
                 "terbilang" => strtoupper(terbilang($sptpd->pajak_terhutang)),
                
                "alamat" => pad_pemda_alamat(),
                "telp" => pad_pemda_telp(),
                "fax" => pad_pemda_fax(),
                "website" => pad_pemda_website(),
                "email" => pad_pemda_email(),
                "alamat_lengkap" => pad_pemda_alamat_lengkap(),
                
                "daerah" => pad_pemda_daerah(),
                "dinas" => pad_pemda_nama(),            
                "logo" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/logorpt__.jpg",
                "logobjb" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/bank-bjb.jpg",

                //"logo" => base_url('assets/img/logorpt__.jpg'),
                //"logobjb" => base_url('assets/img/bank-bjb.jpg'),
            );
        }
        $ignore_html_pg = TRUE;
        $rpt = $rpt;
        
        $db_pad = $this->load->database('pad', TRUE);
        $jasper = $this->load->library('Jasper');
        $jasper->db   = $db_pad->database;
        $jasper->usr  = $db_pad->username;
        $jasper->pwd  = $db_pad->password;
        $jasper->port = $db_pad->port;
        
        echo $jasper->cetak($rpt, $params, $type, $ignore_html_pg);
    }
}
