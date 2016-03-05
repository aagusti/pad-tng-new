<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subjek_pajak_model extends CI_Model {
	private $tbl = 'pad_customer';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('kode'); 
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);

		if($query->num_rows()!==0)
		{
			return $query->row();
		}
		else 
		{
			return FALSE;
		}

	}
	
	function get_npwpd($id, $formatted = true)
	{
		if ($formatted)
			$this->db->select('get_npwpd(id, true) as npwpd', false);
		else
			$this->db->select('get_npwpd(id, false) as npwpd', false);
			
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row()->npwpd;
		}
		else
			return FALSE;
	}
	
	function get_typeahead_npwpd($xnpwpd = NULL, $formatted = true)
	{
        $formatted = $formatted ? 'true' : 'false';
		$this->db->select("id, get_npwpd(id, {$formatted}) as npwpd, nama", false);
		$this->db->like(array("lower(get_npwpd(id, {$formatted}))" => strtolower($xnpwpd)), false); 
		$this->db->order_by('npwpd'); 
		
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function get_typeahead_csname($xname = NULL, $formatted = true)
	{
        $formatted = $formatted ? 'true' : 'false';
		$this->db->select("id, get_npwpd(id, {$formatted}) as npwpd, get_npwpd(id, false) as npwpd2, nama", false);
		$this->db->like(array("lower(nama)" => strtolower($xname)), false); 
		$this->db->order_by('nama'); 
		
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
    function cek_formno($formno) {
        $new_formno = $formno;
        $no_loncat = array(24360,24359,24358,24357,24356,24355,24354,24353,24352,24351,24350,24349,24348,24347,24346,24344);
        if (in_array($formno, $no_loncat)) {
            $new_formno++;
            $this->cek_formno($new_formno);
        }
        return $new_formno;
    }
    
	function generate_npwpd($rp, $pb, $kec_id, $kel_id, $formnomor=0) {
        if($formnomor==0) {
            $this->db->select_max('formno', 'nomor');
            $this->db->where('rp',$rp);
            //no loncat
            $this->db->where('formno not in (24360,24359,24358,24357,24356,24355,24354,24353,24352,24351,24350,24349,24348,24347,24346,24344)');
            // $this->db->where('pb',$pb);
            $formno = $this->db->get($this->tbl)->row()->nomor;
            $formno++;
        } else $formno = $formnomor;
        
        $formno = $this->cek_formno($formno);
        
		$this->db->select('kode');
		$this->db->where('id',$kec_id);
		$kec_kd = $this->db->get('pad_kecamatan')->row()->kode;
		
		$this->db->select('kode');
		$this->db->where('id',$kel_id);
		$this->db->where('kecamatan_id',$kec_id);
		$kel_kd = $this->db->get('pad_kelurahan')->row()->kode;
		
		$npwpd = $rp.$pb.str_pad($formno, 7, '0', STR_PAD_LEFT).$kec_kd.$kel_kd;
		
		$ret = array();
		$ret['formno'] = $formno;
		$ret['npwpd']  = $npwpd;
		return $ret;
	} 
    
    function duplicate_wp($cid, $cuid, $rp, $pb, $kecamatan_id, $kelurahan_id, $nama) {
        $gen_data = $this->load->model('subjek_pajak_model')->generate_npwpd($rp, $pb, $kecamatan_id, $kelurahan_id);
        $formno = $gen_data['formno'];
        
        //customer 
        $sql = "insert into pad_customer(
            parent, npwpd, rp, pb, formno, reg_date, nama, kecamatan_id, 
            kelurahan_id, kabupaten, alamat, kodepos, telphone, wpnama, wpalamat, 
            wpkelurahan, wpkecamatan, wpkabupaten, wptelp, wpkodepos, pnama, 
            palamat, pkelurahan, pkecamatan, pkabupaten, ptelp, pkodepos, 
            ijin1, ijin1no, ijin1tgl, ijin1tglakhir, ijin2, ijin2no, ijin2tgl, 
            ijin2tglakhir, ijin3, ijin3no, ijin3tgl, ijin3tglakhir, ijin4, 
            ijin4no, ijin4tgl, ijin4tglakhir, kukuhno, kukuhnip, kukuhtgl, 
            kukuh_jabat_id, kukuhprinted, enabled, create_date, create_uid, 
            write_date, write_uid, tmt, customer_status_id, kembalitgl, kembalioleh, 
            kartuprinted, kembalinip, penerimanm, penerimaalamat, penerimatgl, 
            catatnip, kirimtgl, batastgl, petugas_jabat_id, pencatat_jabat_id)

            (select parent, npwpd, rp, pb, {$formno}, reg_date, nama, kecamatan_id, 
            kelurahan_id, kabupaten, alamat, kodepos, telphone, wpnama, wpalamat, 
            wpkelurahan, wpkecamatan, wpkabupaten, wptelp, wpkodepos, pnama, 
            palamat, pkelurahan, pkecamatan, pkabupaten, ptelp, pkodepos, 
            ijin1, ijin1no, ijin1tgl, ijin1tglakhir, ijin2, ijin2no, ijin2tgl, 
            ijin2tglakhir, ijin3, ijin3no, ijin3tgl, ijin3tglakhir, ijin4, 
            ijin4no, ijin4tgl, ijin4tglakhir, kukuhno, kukuhnip, kukuhtgl, 
            kukuh_jabat_id, kukuhprinted, enabled, create_date, create_uid, 
            write_date, write_uid, tmt, customer_status_id, kembalitgl, kembalioleh, 
            kartuprinted, kembalinip, penerimanm, penerimaalamat, penerimatgl, 
            catatnip, kirimtgl, batastgl, petugas_jabat_id, pencatat_jabat_id 
            from pad_customer where id={$cid})";
        $this->db->query($sql);
		$new_cid = $this->db->insert_id(); 
        
		$this->db->where('id', $new_cid);
		$this->db->update($this->tbl,array('nama'=>$nama));
        
        //customer usaha
        $konter = $this->load->model('objek_pajak_model')->max_konter($new_cid);
        $sql = "insert into pad_customer_usaha(
            konterid, reg_date, customer_id, usaha_id, so, kecamatan_id, 
            kelurahan_id, notes, enabled, create_date, create_uid, write_date, 
            write_uid, customer_status_id, aktifnotes, tmt, air_zona_id, 
            air_manfaat_id, def_pajak_id, opnm, opalamat, latitude, longitude, 
            kd_restojmlmeja, kd_restojmlkursi, kd_restojmltamu, kd_filmkursi, 
            kd_filmpertunjukan, kd_filmtarif, kd_bilyarmeja, kd_bilyartarif, 
            kd_bilyarkegiatan, kd_diskopengunjung, kd_diskotarif)
            
            (select {$konter}, reg_date, {$new_cid}, usaha_id, so, kecamatan_id, 
            kelurahan_id, notes, enabled, create_date, create_uid, write_date, 
            write_uid, customer_status_id, aktifnotes, tmt, air_zona_id, 
            air_manfaat_id, def_pajak_id, opnm, opalamat, latitude, longitude, 
            kd_restojmlmeja, kd_restojmlkursi, kd_restojmltamu, kd_filmkursi, 
            kd_filmpertunjukan, kd_filmtarif, kd_bilyarmeja, kd_bilyartarif, 
            kd_bilyarkegiatan, kd_diskopengunjung, kd_diskotarif 
            from pad_customer_usaha 
            where id={$cuid})";
        $this->db->query($sql);
		$new_cuid = $this->db->insert_id();
        
        $ret = new stdClass();
        $ret->cid  = $new_cid;
        $ret->cuid = $new_cuid;
        return $ret; 
    }
    
    function rename_wp($id, $new_name) { 
        //customer 
		$this->db->where('id', $id);
		$this->db->update($this->tbl,array('nama'=>$new_name));
    }
	
	//-- admin
	function save($data) {
		$this->db->insert($this->tbl,$data);
		return $this->db->insert_id();
	}
	
	function update($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->tbl,$data);
	}
	
	function delete($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->tbl);
	}
}

/* End of file _model.php */