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
        $this->datatables->select('c.id, get_npwpd(c.id, true) as npwpd, c.nama as customernm, 
			(case when c.wpnama=\'\' then c.pnama else c.wpnama end) as nama, c.alamat, 
			(select count(*) from pad_customer_usaha where customer_id = c.id) as jml_op', false);
        $this->datatables->from('pad_customer c');
        $this->datatables->where('c.rp', 'P');
        $this->datatables->numeric_column('5');
		
        echo $this->datatables->generate();
    }
	
    function grid_for_spt() {
        $this->load->library('Datatables');
        $this->datatables->select('c.id, get_nopd(cu.id, true) as nopd,  cu.opnm, get_npwpd(c.id, true) as npwpd, c.nama as customernm, 
			(case when c.wpnama=\'\' then c.pnama else c.wpnama end) as nama, c.alamat, cu.id as cu_id, cu.usahanm, cu.def_pajak_id, c.wpnama, c.wpalamat', false);
        $this->datatables->from('pad_customer c');
        $this->datatables->join('(select cu.id, cu.usaha_id, u.nama as usahanm, cu.customer_id , cu.def_pajak_id, cu.opnm   
			from pad_customer_usaha cu 
			inner join pad_usaha u on cu.usaha_id = u.id) as cu', 'cu.customer_id = c.id');
        $this->datatables->where('c.rp', 'P');
		
		//if ($this->uri->segment(4) <> '')
		$this->datatables->where('cu.usaha_id', $this->uri->segment(4));
			
        echo $this->datatables->generate();
    }

    function grid_for_skpd($usaha_id) {
        $this->load->library('Datatables');
        $this->datatables->select('s.id, get_skpdno(s.id) skpdno, k.kohirtgl,                                                                                           
            case when s.r_nama is not null then r_nama else c.nama end as customernm, cu.opnm,                                                                                      
            get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa,                                                                                        
            s.pajak_terhutang as pajak', false);
        $this->datatables->from('pad_spt s');
        $this->datatables->join('pad_customer_usaha cu', 's.customer_usaha_id=cu.id', 'left');
        $this->datatables->join('pad_customer c', 'cu.customer_id=c.id', 'left');
        $this->datatables->join('pad_spt_type st', 'st.id=s.type_id', 'left');
        $this->datatables->join('pad_kohir k', 'k.spt_id=s.id ', 'left');
        $this->datatables->where("upper(st.typenm) like 'SK%'");
        $this->datatables->where("cu.usaha_id", $usaha_id );
        $this->datatables->where("k.kohirno is NOT NULL");
        $this->datatables->where("s.proses", 2);
            
        echo $this->datatables->generate();
    }
	
    function grid_for_jalan() {
        $this->load->library('Datatables');
        $this->datatables->select('j.id, j.jalan_kelas_id, kj.nama as kelas_jalan, j.nama as jalan', false);
        $this->datatables->from('pad_reklame_jalan j');
        $this->datatables->join('pad_reklame_kelas_jalan kj', 'kj.id = j.jalan_kelas_id');
        $this->datatables->where('j.enabled', 1);     
        echo $this->datatables->generate();
    }                                                                   


/*
    SELECT s.id, get_skpdno(s.id) skpdno, k.kohirtgl,                                                                                           
    case when s.r_nama is not null then r_nama else c.nama end as customernm, cu.opnm,                                                                                      
    get_bulan(extract(month from s.masadari)::int, TRUE)||extract(year from s.masadari) as masa,                                                                                        
    s.pajak_terhutang as pajak                                                                                      
FROM pad_spt s                                                                                          
LEFT JOIN pad_customer_usaha cu ON s.customer_usaha_id=cu.id                                                                                            
LEFT JOIN pad_customer c ON cu.customer_id=c.id                                                                                         
LEFT JOIN pad_spt_type st ON st.id=s.type_id                                                                                            
LEFT JOIN pad_kohir k ON k.spt_id=s.id                                                                                          
WHERE upper(st.typenm) like 'SK%'                                                                                           
AND s.terimatgl BETWEEN $tgldari AND $tglsampai                                                                                         
AND cu.usaha_id =  $usaha_id                                                                                            
AND k.kohirno is Not NULL                                                                                           
AND s.proses =  '2'                                                                                         
ORDER BY s.id desc                                                                                          
LIMIT 13           
*/                                                                                 


	function grid_objek_pajak() {
        $cid = $this->uri->segment(4);
		
        $this->load->library('Datatables');
        $this->datatables->select('cu.id, konterid, get_nopd(cu.id, true) as nopd, u.nama as notes, opnm', false);
        $this->datatables->from('pad_customer_usaha cu');
        $this->datatables->join('pad_usaha u','u.id= cu.usaha_id');
        $this->datatables->where('customer_id', $cid);
        echo $this->datatables->generate();
	}
    
    // grid kartu data hotel
    function grid_kd() 
    {
        $c_id    = $this->uri->segment(4);
        $this->session->set_userdata('c_id', $c_id );
        $konterid    = $this->uri->segment(5);
        $this->session->set_userdata('konter', $konterid );
        $this->load->library('Datatables');
        $this->datatables->select('notes, tarif, kamar, volume', false);
        $this->datatables->from('pad_customer_detail');			
        $this->datatables->where('customer_id', $c_id);
        $this->datatables->where('konterid', $konterid);
        $this->datatables->add_column('batal', '<a class="delete" href="">Hapus</a>');
        
        echo $this->datatables->generate();
    }

    function grid_at() 
    {
        $cu_id    = $this->uri->segment(4);
        $this->session->set_userdata('cu_id', $cu_id );
        $konterid    = $this->uri->segment(5);
        $this->session->set_userdata('konter', $konterid );
        $c_id    = $this->uri->segment(6);
        $this->session->set_userdata('konter', $c_id );
        $this->load->library('Datatables');
        $this->datatables->select('sumur_ke, sipa_no, max_volume, disabled', false);
        $this->datatables->from('pad_customer_usaha_air_tanah');         
        $this->datatables->where('customer_usaha_id', $c_id);
        $this->datatables->add_column('batal', '<a class="delete" href="">Hapus</a>');
        
        echo $this->datatables->generate();
    }

    function get_max_sumur() 
    {
        $c_id    = $this->uri->segment(6);
        $query = $this->db->query("select max(sumur_ke) as sumur_ke from pad_customer_usaha_air_tanah where customer_usaha_id = $c_id");
        $sumur_data = new stdClass();
        foreach ($query->result() as $row)
        {
           $sumur_ke = $row->sumur_ke + 1;
        }
        $sumur_data->sumur_ke = $sumur_ke;

        echo json_encode($sumur_data);
    }

    function cek_sipa($sipa_no) 
    {
        $query = $this->db->query("select sipa_no from pad_customer_usaha_air_tanah where sipa_no = '$sipa_no'");
        $sipa_data = new stdClass();
        if( $query->num_rows()>0 ){
        $sipa_data->exist = '1';
        }else{
        $sipa_data->exist = '0';
        }
        echo json_encode($sipa_data);
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
        $this->form_validation->set_rules('kukuhtgl','Tgl. Pengukuhan','required');
        $this->form_validation->set_rules('kukuhno','No. Pengukuhan','required|trim');
        
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
			
			//$gen_data = $this->subjek_pajak_model->generate_npwpd($input_post['rp'], $input_post['pb'], $input_post['kecamatan_id'], $input_post['kelurahan_id']);
			//$formno = $gen_data['formno'];
			//$npwpd  = $gen_data['npwpd']; AMbil dari fungsi saja

            $query = $this->db->query("select get_nowp() as formno from pad.pad_customer limit 1;");
            foreach ($query->result_array() as $row)
            {
               $formno = $row['formno'];
            }

            $post_data  = array(
				//'npwpd' => 0,
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
                'kukuhtgl' => date('Y-m-d', strtotime($input_post['kukuhtgl'])),
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
		
        /*
		$options = array(
			'1' => 'PRIBADI',
			'2' => 'BADAN'
		);
        */

        $options = array(
            '1' => 'Air Tanah',
            '2' => 'Self',
            '3' => 'Reklame'

        );
		$js                = 'id="pb" class="input-medium" ';
		$data['select_pb'] = form_dropdown('pb', $options, '', $js);
		
		$select_data  = $this->load->model('kecamatan_model')->get_select();
		$options      = array();
		$kec_id = '';
		if($select_data) {
		foreach ($select_data as $row) {
			if($kec_id == '') $kec_id = $row->id;
			$options[$row->id] = $row->nama;
		}}
		$js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" ';
		$data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, '', $js);

		$select_data = $this->load->model('kelurahan_model')->get_select($kec_id);
		$options     = array();
		if($select_data) {
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}}
		$js                       = 'id="kelurahan_id" class="input-large" ';
		$data['select_kelurahan'] = form_dropdown('kelurahan_id', $options, '', $js);

		//op
		$options = array(
			'1' => 'AKTIF',
			'2' => 'TUTUP',
			'3' => 'TUTUP SEMENTARA',
            '4' => 'NORMAL',
            '5' => 'LIBUR',
		);
		$js = 'id="pb" class="input-medium"';
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
            $data['dt']['kukuhtgl']           = date('d-m-Y', strtotime($get->kukuhtgl));
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
            		
           
            
            $options = array(
                '1' => 'Air Tanah',
                '2' => 'Self',
                '3' => 'Reklame'

            );
            $js                = 'id="pb" class="input-medium" ';
            $data['select_pb'] = form_dropdown('pb', $options, $get->pb, $js);
            
            $select_data = $this->load->model('kecamatan_model')->get_select();
            $options     = array();
            if($select_data) {
			foreach ($select_data as $row) {
				$options[$row->id] = $row->nama;
			}}
            $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);" ';
            $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, $get->kecamatan_id, $js);
            
            $select_data = $this->load->model('kelurahan_model')->get_select($get->kecamatan_id);
            $options     = array();
            if($select_data) {
            foreach ($select_data as $row) {
                $options[$row->id] = $row->nama;
            }}
            $js                       = 'id="kelurahan_id" class="input-large" ';
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

			//op - just show
			$options = array();
			$js = 'id="op_status" class="input" ';
			$data['select_op_status'] = form_dropdown('op_status', $options, '', $js);
			
			$js = 'id="op_usaha_id" class="input" onChange="get_op_pajak(this.value);"';
			$data['select_op_usaha'] = form_dropdown('op_usaha_id', $options, '', $js);
            
			$js = 'id="op_pajak_id" class="input" ';
			$data['select_op_pajak'] = form_dropdown('op_pajak_id', $options, '', $js);
			
            $js = 'id="op_kecamatan_id" class="input" onChange="get_op_kelurahan(this.value);get_air_zona(this.value);" ';
            $data['select_op_kecamatan'] = form_dropdown('op_kecamatan_id', $options, '', $js);
            
            $js = 'id="op_kelurahan_id" class="input"';
            $data['select_op_kelurahan'] = form_dropdown('op_kelurahan_id', $options, $get->kelurahan_id, $js);
    
            $js = 'id="op_mall_id" class="input"';
            $data['select_op_mall'] = form_dropdown('op_mall_id', $options, '', $js);

            $js = 'id="op_cash_register" class="input"';
            $data['select_op_cash_register'] = form_dropdown('op_cash_register', $options, '', $js);

            $js = 'id="op_bandara" class="input"';
            $data['select_op_bandara'] = form_dropdown('op_bandara', $options, '', $js);

            $js = 'id="op_pelaporan" class="input"';
            $data['select_op_pelaporan'] = form_dropdown('op_pelaporan', $options, '', $js);

            $js = 'id="op_pembukuan" class="input"';
            $data['select_op_pembukuan'] = form_dropdown('op_pembukuan', $options, '', $js);

            $js = 'id="op_wajib_pajak" class="input"';
            $data['select_op_wajib_pajak'] = form_dropdown('op_wajib_pajak', $options, '', $js);

            //Air Tanah
            $js = 'id="op_zona" class="input" readonly';
            $data['select_op_zona'] = form_dropdown('op_zona', $options, '', $js);

            $js = 'id="op_golongan" class="input"';
            $data['select_op_golongan'] = form_dropdown('op_golongan', $options, '', $js);

            $js = 'id="op_manfaat" class="input"';
            $data['select_op_manfaat'] = form_dropdown('op_manfaat', $options, '', $js);

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
			//$npwpd  = $gen_data['npwpd']; --> ganti bentuk npwpd
			
            $post_data  = array(
				//'npwpd' => 0,
                
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
                'kukuhtgl' => date('Y-m-d', strtotime($input_post['kukuhtgl'])),
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
            // uplod dokeumen
            // $this->unggah($cid);
            // -- END data tambahan / detail
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('subjek_pajak'));
        }
        
        $get               = (object) $post_data;
        $options = array(
            '1' => 'Air Tanah',
            '2' => 'Self',
            '3' => 'Reklame'

        );
        $js                = 'id="pb" class="input-medium"';
        $data['select_pb'] = form_dropdown('pb', $options, $get->pb, $js);
        
        $select_data = $this->load->model('kecamatan_model')->get_select();
        $options     = array();
		if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }}
        $js                       = 'id="kecamatan_id" class="input-medium" onChange="get_kelurahan(this.value);"';
        $data['select_kecamatan'] = form_dropdown('kecamatan_id', $options, $get->kecamatan_id, $js);
        
        $select_data = $this->load->model('kelurahan_model')->get_select($get->kecamatan_id);
        $options     = array();
		if($select_data) {
        foreach ($select_data as $row) {
            $options[$row->id] = $row->nama;
        }}
        $js                       = 'id="kelurahan_id" class="input-large"';
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
        //$this->get_air_zona();
    }

    function get_air_zona() { //By kecamatan
        $kec_id    = $this->uri->segment(4);
        $air_zona = $this->load->model('kecamatan_model')->get_select_zona($kec_id);
        echo json_encode($air_zona);
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

        if(($op_data->tanggal_tutup) =='')
        $op_data->tanggal_tutup = '';
        else
        $op_data->tanggal_tutup = date('d-m-Y', strtotime($op_data->tanggal_tutup));

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

        $select_data = $this->load->model('reklame_mall_model')->get_all();
        $mall = '';
        $mall .= "<option value=999>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->mall_id)
                $mall .= "<option value={$row->id} selected>{$row->nama}</option>";
            else
                $mall .= "<option value={$row->id}>{$row->nama}</option>";
        }}

		//wkkk kode ngarang, baka <- fixed :p
        $select_data = array((object) array('id'=>1, 'status'=>'AKTIF'), (object) array('id'=>2, 'status'=>'TUTUP'), (object) array('id'=>3, 'status'=>'TUTUP SEMENTARA'),
                        (object) array('id'=>4, 'status'=>'NORMAL'), (object) array('id'=>5, 'status'=>'LIBUR'));
        $status = '';
		if($select_data) {
        foreach ($select_data as $row) {
			if ($row->id == $op_data->customer_status_id)
				$status .= "<option value={$row->id} selected >{$row->status}</option>";
			else
				$status .= "<option value={$row->id}>{$row->status}</option>";
        }}

        $select_data =  (object) array((object) array('id'=>1, 'val'=>'YA'), (object) array('id'=>2, 'val'=>'TIDAK'));
        $op_yes_no1 = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->cash_register)
            $op_yes_no1 .= "<option value={$row->id} selected>{$row->val}</option>";
            else
            $op_yes_no1 .= "<option value={$row->id}>{$row->val}</option>";
        }}
        $op_yes_no2 = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->pelaporan)
            $op_yes_no2 .= "<option value={$row->id} selected>{$row->val}</option>";
            else
            $op_yes_no2 .= "<option value={$row->id}>{$row->val}</option>";
        }}
        $op_yes_no3 = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->pembukuan)
            $op_yes_no3 .= "<option value={$row->id} selected>{$row->val}</option>";
            else
            $op_yes_no3 .= "<option value={$row->id}>{$row->val}</option>";

        }}
        $op_yes_no4 = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->bandara)
            $op_yes_no4 .= "<option value={$row->id} selected>{$row->val}</option>";
            else
            $op_yes_no4 .= "<option value={$row->id}>{$row->val}</option>";

        }}
        $select_data = $this->load->model('air_zona_model')->get_select();
        $zona     = '';
        $zona .= "<option value=0>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->zona_id)
                $zona .= "<option value={$row->id} selected>{$row->nama}</option>";
            else
                $zona .= "<option value={$row->id}>{$row->nama}</option>";
        }}

        $select_data = $this->load->model('air_manfaat_model')->get_select();
        $manfaat     = '';
        $manfaat .= "<option value=0>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->manfaat_id)
                $manfaat .= "<option value={$row->id} selected>{$row->nama}</option>";
            else
                $manfaat .= "<option value={$row->id}>{$row->nama}</option>";
        }}

        $select_data =  (object) array((object) array('id'=>0, 'golongan'=>'TIDAK ADA'), (object) array('id'=>1, 'golongan'=>'ABT'), (object) array('id'=>2, 'golongan'=>'AP'));
        $golongan = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->golongan_id)
            $golongan .= "<option value={$row->id} selected>{$row->golongan}</option>";
            else
            $golongan .= "<option value={$row->id}>{$row->golongan}</option>";
        }}

        $select_data =  (object) array((object) array('id'=>1, 'val'=>'Wajib Pajak'), (object) array('id'=>2, 'val'=>'Wajib Pungut/Bendahara'));
        $op_wp = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == $op_data->wajib_pajak)
            $op_wp .= "<option value={$row->id} selected>{$row->val}</option>";
            else
            $op_wp .= "<option value={$row->id}>{$row->val}</option>";
        }}

        $kelompok_usaha = 0;

        $ku_id = $op_data->kelompok_usaha_id;
        if ($ku_id != ''){
        $query = $this->db->query("select nama from pad_air_kelompok_usaha where id= $ku_id");
            if ($query->num_rows()>0){
            foreach ($query->result() as $row)
                {
                    $kelompok_usaha = $row->nama;
                }
            }
		}
        //echo json_encode($select_data);
		$op_data->usaha     = $usaha;
		$op_data->pajak     = $pajak;
		$op_data->status    = $status;
		$op_data->kecamatan = $kecamatan;
		$op_data->kelurahan = $kelurahan;
        $op_data->mall = $mall;
        $op_data->cash_register = $op_yes_no1;
        $op_data->pelaporan = $op_yes_no2;
        $op_data->pembukuan = $op_yes_no3;
        $op_data->bandara = $op_yes_no4;
        $op_data->wajib_pajak = $op_wp;
        $op_data->zona     = $zona;
        $op_data->manfaat  = $manfaat;
        $op_data->golongan = $golongan;
        $op_data->kelompok_usaha = $kelompok_usaha;

        echo json_encode($op_data);
    }
	
    function new_op() {
    $op_data = new stdClass();
		$op_data->tmt = date('d-m-Y');
        $op_data->tanggal_tutup = date('d-m-Y');

		
        $select_data = $this->load->model('usaha_model')->get_select();
        $usaha = '';
        $usaha_id = '';
		if($select_data) {
        foreach ($select_data as $row) {
            $usaha_id == '' ? $usaha_id = $row->id : $usaha_id;
			$usaha .= "<option value={$row->id}>{$row->nama}</option>";
        }}
        
        $select_data = $this->load->model('pajak_model')->get_select2($usaha_id);
        $pajak = '';
		if($select_data) {
        foreach ($select_data as $row) {
			$pajak .= "<option value={$row->id}>{$row->nama}</option>";
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

        $select_data = $this->load->model('reklame_mall_model')->get_all();
        $mall = '';
        $mall .= "<option value=999>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            $mall_id = empty($mall_id) ? $row->id : $mall_id;
             $mall .= "<option value={$row->id}>{$row->nama}</option>";
        }}

        $select_data = $this->load->model('reklame_mall_model')->get_all();
        $mall = '';
        $mall .= "<option value=999>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            $mall_id = empty($mall_id) ? $row->id : $mall_id;
             $mall .= "<option value={$row->id}>{$row->nama}</option>";
        }}

        $zona     = '';
        /*
        $select_data = $this->load->model('air_zona_model')->get_select();
        $zona     = '';
        $zona .= "<option value=0>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            $zona_id = empty($zona_id) ? $row->id : $zona_id;
             $zona .= "<option value={$row->id}>{$row->nama}</option>";
        }}
        */

        $select_data = $this->load->model('air_manfaat_model')->get_select();
        $manfaat     = '';
        $manfaat .= "<option value=0>TIDAK ADA</option>";
        if($select_data) {
        foreach ($select_data as $row) {
            $manfaat_id = empty($manfaat_id) ? $row->id : $manfaat_id;
             $manfaat .= "<option value={$row->id}>{$row->nama}</option>";
        }}

        $select_data =  (object) array((object) array('id'=>0, 'golongan'=>'TIDAK ADA'), (object) array('id'=>1, 'golongan'=>'ABT'), (object) array('id'=>2, 'golongan'=>'AP'));
        $golongan = '';
		if($select_data) {
        foreach ($select_data as $row) {
			$golongan .= "<option value={$row->id}>{$row->golongan}</option>";
        }}


        $select_data =  (object) array((object) array('id'=>1, 'val'=>'YA'), (object) array('id'=>2, 'val'=>'TIDAK'));
        $op_yes_no = '';
        if($select_data) {
        foreach ($select_data as $row) {
            if ($row->id == 2)
            $op_yes_no .= "<option value={$row->id} selected>{$row->val}</option>";
            else
            $op_yes_no .= "<option value={$row->id}>{$row->val}</option>";
        }}



        $select_data =  (object) array((object) array('id'=>1, 'val'=>'Wajib Pajak'), (object) array('id'=>2, 'val'=>'Wajib Pungut / Bendahara'));
        $op_wp = '';
        if($select_data) {
        foreach ($select_data as $row) {
            $op_wp .= "<option value={$row->id}>{$row->val}</option>";
        }}

        $select_data =  (object) array((object) array('id'=>1, 'status'=>'AKTIF'), (object) array('id'=>2, 'status'=>'TUTUP'), (object) array('id'=>3, 'status'=>'TUTUP SEMENTARA'),
                        (object) array('id'=>4, 'status'=>'NORMAL'), (object) array('id'=>5, 'status'=>'LIBUR'));
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
        $op_data->mall      = $mall;
        $op_data->cash_register = $op_yes_no;
        $op_data->pelaporan = $op_yes_no;
        $op_data->pembukuan = $op_yes_no;
        $op_data->bandara = $op_yes_no;
        $op_data->wajib_pajak = $op_wp;
        $op_data->zona     = $zona;
        $op_data->manfaat  = $manfaat;
        $op_data->golongan = $golongan;


		
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
            'aktifnotes' => $this->input->get_post('aktifnotes'),

			'customer_status_id' => $this->input->get_post('op_status'),
			'enabled' => 1,
			'tmt' => $this->input->get_post('op_tmt') == '' ? NULL : date('Y-m-d', strtotime($this->input->get_post('op_tmt'))),
            
			'opnm' => $this->input->get_post('op_nama'),
			'opalamat' => $this->input->get_post('op_alamat'),
			'latitude' => pad_to_decimal($this->input->get_post('op_latitude')),
			'longitude' => pad_to_decimal($this->input->get_post('op_longitude')),
			'def_pajak_id' => $this->input->get_post('op_pajak_id'),
          // data tambahan           
            'kd_restojmlmeja' => pad_to_decimal($this->input->get_post('kd_restojmlmeja')),
            'kd_restojmlkursi' => pad_to_decimal($this->input->get_post('kd_restojmlkursi')),
            'kd_restojmltamu' => pad_to_decimal($this->input->get_post('kd_restojmltamu')),
            'kd_filmkursi' => pad_to_decimal($this->input->get_post('kd_filmkursi')),
            'kd_filmpertunjukan' => pad_to_decimal($this->input->get_post('kd_filmpertunjukan')),
            'kd_filmtarif' => pad_to_decimal($this->input->get_post('kd_filmtarif')),
            'kd_bilyarmeja' => pad_to_decimal($this->input->get_post('kd_bilyarmeja')),
            'kd_bilyartarif' => pad_to_decimal($this->input->get_post('kd_bilyartarif')),
            'kd_bilyarkegiatan' => pad_to_decimal($this->input->get_post('kd_bilyarkegiatan')),
            'kd_diskopengunjung' => pad_to_decimal($this->input->get_post('kd_diskopengunjung')),
            'kd_diskotarif' => pad_to_decimal($this->input->get_post('kd_diskotarif')),
            'mall_id' => pad_to_decimal($this->input->get_post('op_mall_id')),

            'cash_register'=> pad_to_decimal($this->input->get_post('op_cash_register')),
            'pelaporan'=> pad_to_decimal($this->input->get_post('op_pelaporan')),
            'pembukuan'=> pad_to_decimal($this->input->get_post('op_pembukuan')),
            'bandara'=> pad_to_decimal($this->input->get_post('op_bandara')),
            'wajib_pajak'=> pad_to_decimal($this->input->get_post('op_wajib_pajak')),
            'jumlah_karyawan'=> pad_to_decimal($this->input->get_post('jumlah_karyawan')),
            'tanggal_tutup'=> $this->input->get_post('tanggal_tutup') == '' ? NULL : date('Y-m-d', strtotime($this->input->get_post('tanggal_tutup'))),

            'parkir_luas'=> pad_to_decimal($this->input->get_post('parkir_luas')),
            'parkir_masuk'=> pad_to_decimal($this->input->get_post('parkir_masuk')),
            'parkir_keluar'=> pad_to_decimal($this->input->get_post('parkir_keluar')),
            'parkir_tarif_mobil'=> pad_to_decimal($this->input->get_post('parkir_tarif_mobil')), 
            'parkir_tambahan'=> pad_to_decimal($this->input->get_post('parkir_tambahan')),
            'parkir_kapasitas_mobil'=> pad_to_decimal($this->input->get_post('parkir_kapasitas_mobil')),
            'parkir_mobil_hari'=> pad_to_decimal($this->input->get_post('parkir_mobil_hari')),

          'parkir_motor_luas'=> pad_to_decimal($this->input->get_post('parkir_motor_luas')),
          'parkir_motor_masuk'=> pad_to_decimal($this->input->get_post('parkir_motor_masuk')), 
          'parkir_motor_keluar'=> pad_to_decimal($this->input->get_post('parkir_motor_keluar')), 
          'parkir_tarif_motor'=> pad_to_decimal($this->input->get_post('parkir_tarif_motor')),
          'parkir_motor_tambahan'=> pad_to_decimal($this->input->get_post('parkir_motor_tambahan')), 
          'parkir_kapasitas_motor'=> pad_to_decimal($this->input->get_post('parkir_kapasitas_motor')),
          'parkir_motor_hari'=> pad_to_decimal($this->input->get_post('parkir_motor_hari')),

            'kelompok_usaha_id' => pad_to_decimal($this->input->get_post('op_kelompok_usaha_id')),
            'zona_id'=> pad_to_decimal($this->input->get_post('op_zona')),
            'manfaat_id'=> pad_to_decimal($this->input->get_post('op_golongan')),
            'golongan_id'=> pad_to_decimal($this->input->get_post('op_manfaat')),


		);
		
		$op_model = $this->load->model('objek_pajak_model');
		if ($mode == 'add') {
			$konter = $op_model->max_konter($this->input->get_post('customer_id'));
			$this->session->set_userdata('konter', $konter);
			$new_data = array(
				'konterid' => $konter,
				'reg_date' => date('Y-m-d'),
				'created' => date('Y-m-d h:i:s'),
                'create_uid' => sipkd_user_id(),
			);
			$update_data = array_merge($update_data, $new_data);
			$op_model->save($update_data);
		}
		
		if ($mode == 'edit') {
			$new_data = array(
				'updated' => date('Y-m-d h:i:s'),
                'update_uid' => sipkd_user_id(),
			);
			$update_data = array_merge($update_data, $new_data);
			$op_model->update($op_id, $update_data);
		}
                    // data tambahan / detail
            $dtKD = $this->input->post('dtKD');
            $tambahan_data2 = array();
            
            if(isset($dtKD)) {
                $i = 1;
                $dtKD = json_decode($dtKD, true);
                
                if(count($dtKD['dtKD']) > 0){
                    if ($mode == 'add') {
                        $konter = $this->session->userdata('konter');
                        $this->session->unset_userdata('konter');
                        $query = $this->db->query("SELECT id FROM pad_customer_usaha ORDER BY id DESC LIMIT 1");
                            foreach ($query->result() as $row)
                            {
                               $cu_id = $row->id;
                            }
                    }
                    if ($mode == 'edit') {
                        $konter = $this->input->get_post('op_konterid');
                        $cu_id =  $this->input->get_post('op_id');
                    }
                    $cid = $this->input->get_post('customer_id');
                    $op_u_id =  $this->input->get_post('op_usaha_id');
                    $rd_row = array();
                    foreach($dtKD['dtKD'] as $rows) {
                        $rd_row = array (
                            'customer_id' =>$cid,
                            'customer_usaha_id' =>$cu_id,
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

            $dtAT = $this->input->post('dtAT');
            $tambahan_data2 = array();
            
            if(isset($dtAT)) {
                $i = 1;
                $dtAT = json_decode($dtAT, true);
                
                if(count($dtAT['dtAT']) > 0){
                    if ($mode == 'add') {
                        $konter = $this->session->userdata('konter');
                        $this->session->unset_userdata('konter');
                        $query = $this->db->query("SELECT id FROM pad_customer_usaha ORDER BY id DESC LIMIT 1");
                            foreach ($query->result() as $row)
                            {
                               $cu_id = $row->id;
                            }
                    }
                    if ($mode == 'edit') {
                        $konter = $this->input->get_post('op_konterid');
                         $cu_id =  $this->input->get_post('op_id');
                    }
                    $cid = $this->input->get_post('customer_id');
                    $op_u_id =  $this->input->get_post('op_usaha_id');
                    $rd_row = array();
                    foreach($dtAT['dtAT'] as $rows) {
                        $rd_row = array (
                            'customer_id' =>$cid,
                            'konterid' => $konter,
                            'customer_usaha_id' => $cu_id,
                            'sumur_ke' => $i,
                            'sipa_no'  => $rows[1],
                            'max_volume'  => pad_to_decimal($rows[2]),
                            'disabled' => $rows[3],
                        );
                        $i++;
                        $tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
                    }
                    
                    //langsung ajah dah - sementara
                    $query= "delete from pad_customer_usaha_air_tanah where (customer_id=$cid and konterid=$konter)"; 
                    $this->db->query($query);
                    $this->db->insert_batch('pad_customer_usaha_air_tanah', $tambahan_data2);
                }
            }

	}
	
    public function delete_op() {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('subjek_pajak'));
        }
        //delete juga cust. detail
        $cid = $this->session->userdata('cu_id');
        $konter = $this->session->userdata('konter');
        $this->session->unset_userdata('konter', 'cu_id');
        $query= "delete from pad_customer_detail where (customer_id=$cid and konterid=$konter)"; 
        $this->db->query($query);
        $query= "delete from pad_customer_usaha_air_tanah where (customer_id=$cid and konterid=$konter)"; 
        $this->db->query($query);

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
                    //'img' => base_url('assets/img/kartuwp.jpg'), kartuwp.jpg
                    //'ttd' => base_url('assets/img/ttd_cap.gif'),
                    "img" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/kartuwp.jpg",
                    "ttd" => $_SERVER["DOCUMENT_ROOT"]."/assets/img/ttd_cap.gif",
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
