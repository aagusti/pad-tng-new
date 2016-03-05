<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class skpd_model extends CI_Model {
	private $tbl = 'pad_kohir';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('kohirno'); 
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
			return FALSE;
	}
	
	function generate_kohirno($tahun, $usaha_id) {
		$this->db->select_max('kohirno', 'nomor');
		$this->db->where('tahun',$tahun);
		// $this->db->where('usaha_id',$usaha_id);
		$kohirno = $this->db->get($this->tbl)->row()->nomor;
		$kohirno++;
		
		return $kohirno;
	}
    
    function get_skpdj_data($usaha_id='', $cu_id='') {
        $pad_reklame_id  = pad_reklame_id();
        $pad_airtanah_id = pad_air_tanah_id();
        
		$qry = "SELECT 
            --c.id as c_id, get_npwpd(c.id, true) as npwpd, upper(c.customernm) customernm, 
            --(case when c.wpnama='' then c.pnama else c.wpnama end) as nama, c.alamat, 
            --u.usahanm, kel.kelurahannm, kec.kecamatannm, 
            cu.id as cu_id
            --, cu.usaha_id 

            FROM pad_customer c 
            INNER JOIN pad_customer_usaha cu ON cu.customer_id=c.id 
            INNER JOIN pad_usaha u ON u.id=cu.usaha_id 
            INNER JOIN pad_kecamatan kec ON kec.id = cu.kecamatan_id 
            INNER JOIN pad_kelurahan kel ON kel.kecamatan_id = cu.kecamatan_id and kel.id = cu.kelurahan_id 

            WHERE c.rp = 'P' AND cu.customer_status_id = 1 
            AND cu.usaha_id not in ({$pad_reklame_id}, {$pad_airtanah_id})
            AND extract(month from cu.created)<extract(month from now())
            AND extract(year from cu.created)=extract(year from now())
            AND ( 
                select count(*) 
                from pad_spt s1 
                where s1.customer_usaha_id = cu.id 
                and extract(month from s1.masadari)= extract(month from now()-interval '1 month')
                and extract(year from s1.masadari)= extract(year from now()-interval '1 month')
            ) < 1
            ";
			
		if ($cu_id!='')
			$qry .= " and cu.id in ({$cu_id}) ";
		if ($usaha_id!='')
			$qry .= " and cu.usaha_id in ({$usaha_id}) ";
        $qry .= "ORDER BY cu.usaha_id, c.npwpd desc";
        
		$query = $this->db->query($qry);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
    }
    
	function proses_skpdj($spt_id, $omset) {
        $this->load->model('sptpd_model');
        $this->load->model('pajak_model');
        
        $sptpd = $this->sptpd_model->get($spt_id);
        
        $tahun = date('Y');
        $sptno = $this->sptpd_model->generate_sptno($tahun);
        
        $omset_skpdj = $omset * 125/100;
        
        $terimatgl  = date('Y-m-d');
        $lastmonth  = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
        $lastmonth  = date('Y-m-d', $lastmonth);
        $masadari   = date("Y-m-01", strtotime($lastmonth));
        $masasd     = date("Y-m-t", strtotime($lastmonth));
        
        $jatuhtempo = date_create($terimatgl);
        date_modify($jatuhtempo,"+30 days");
        $jatuhtempotgl = date_format($jatuhtempo,"Y-m-d");

        $skpdj_type_id = '3'; // <------------ id default
        $skpdj_type = $this->db->query("select id from pad_spt_type where typenm ilike '%skpdj%' limit 1");
        if($skpdj_type->num_rows()!==0)
            $skpdj_type_id = $skpdj_type->row()->id;
            
        $so = 'O';
        $pajak_id = $sptpd->pajak_id;
        $tarif = $this->db->query("select tarif from pad_tarif_pajak where pajak_id={$pajak_id}
            order by tmt desc limit 1")->row()->tarif;
            
        $pajak_terhutang = $omset_skpdj * $tarif;
        $notes = 'entry by system';
        $user_id = sipkd_user_id();
        
        //SPT
        $sql = "insert into pad_spt(
            tahun, sptno, type_id, so, masadari, masasd, jatuhtempotgl, 
            dasar, tarif, denda, bunga, setoran, kenaikan, kompensasi, lain2, 
            pajak_terhutang, 
            enabled, created, create_uid, terimanip, terimatgl, isprint_dc, 
            status_pembayaran, notes,

            customer_id, customer_usaha_id, rekening_id, 
            pajak_id, r_bayarid, 
            minimal_omset, air_manfaat_id, air_zona_id, meteran_awal, 
            meteran_akhir, volume, satuan, r_panjang, r_lebar, r_muka, r_banyak, 
            r_luas, r_tarifid, r_kontrak, r_lama, r_jalan_id, r_jalanklas_id, 
            r_lokasi, r_judul, r_kelurahan_id, r_lokasi_id, r_calculated, 
            r_nsr, r_nsrno, r_nsrtgl, r_nsl_kecamatan_id, r_nsl_type_id, 
            r_nsl_nilai, unit_id, 
            r_nsr_id, r_lokasi_pasang_id, r_lokasi_pasang_val, r_jalanklas_val, 
            r_sudut_pandang_id, r_sudut_pandang_val, r_tinggi, r_njop, r_status, 
            r_njop_ketinggian, rek_no_paneng, sptno_lengkap, 
            sptno_lama, r_nama, r_alamat)

            (select 
            {$tahun}, {$sptno}, {$skpdj_type_id}, '{$so}', '{$masadari}', '{$masasd}', '{$jatuhtempotgl}', 
            {$omset_skpdj}, {$tarif}, 0 denda, 0 bunga, 0 setoran, 0 kenaikan, 0 kompensasi, 0lain2, 
            {$pajak_terhutang}, 
            1 enabled, '{$terimatgl}', {$user_id}, {$user_id}, '{$terimatgl}', false isprint_dc, 
            0 status_pembayaran, '{$notes}', 

            customer_id, customer_usaha_id, rekening_id, 
            pajak_id, r_bayarid, 
            minimal_omset, air_manfaat_id, air_zona_id, meteran_awal, 
            meteran_akhir, volume, satuan, r_panjang, r_lebar, r_muka, r_banyak, 
            r_luas, r_tarifid, r_kontrak, r_lama, r_jalan_id, r_jalanklas_id, 
            r_lokasi, r_judul, r_kelurahan_id, r_lokasi_id, r_calculated, 
            r_nsr, r_nsrno, r_nsrtgl, r_nsl_kecamatan_id, r_nsl_type_id, 
            r_nsl_nilai, unit_id, 
            r_nsr_id, r_lokasi_pasang_id, r_lokasi_pasang_val, r_jalanklas_val, 
            r_sudut_pandang_id, r_sudut_pandang_val, r_tinggi, r_njop, r_status, 
            r_njop_ketinggian, rek_no_paneng, sptno_lengkap, 
            sptno_lama, r_nama, r_alamat
            from pad_spt where id={$spt_id})";
            
        $this->db->query($sql);
		$new_spt_id = $this->db->insert_id(); 
        
        //SKPD J
        $usaha_id = $this->pajak_model->get($sptpd->pajak_id)->usaha_id;
        $kohirno  = $this->generate_kohirno($tahun, $usaha_id);
        $update_data = array (
            'spt_id' => $new_spt_id,
            'kohirno' => $kohirno,
            'usaha_id' => $usaha_id,
            'tahun' => $tahun,
            'kohirtgl' => $terimatgl,

            'enabled' => 1,
            'created' => $terimatgl,
            'create_uid' => $user_id,
        );
        $this->save($update_data);
        
        /*        
        $ret = new stdClass();
        $ret->cid  = $new_cid;
        $ret->cuid = $new_cuid;
        return $ret; 
        */
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
	
	function delete_by_spt($spt_id) {
		$this->db->where('spt_id', $spt_id);
		$this->db->delete($this->tbl);
	}
}

/* End of file _model.php */