<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class objek_pajak_model extends CI_Model {
	private $tbl = 'pad_customer_usaha';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('konterid'); 
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
	
	function get_nopd($id, $formatted = true)
	{
		if ($formatted)
			$this->db->select('get_nopd(id, true) as nopd', false);
		else
			$this->db->select('get_nopd(id, false) as nopd', false);
			
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row()->nopd;
		}
		else
			return FALSE;
	}
		
	function get_typeahead_nopd($xnopd = NULL, $usaha_id = FALSE, $formatted = true)
	{
        $formatted = $formatted ? 'true' : 'false';
		$this->db->select("cu.id, get_nopd(cu.id, {$formatted}) as nopd, cu.customer_id, cu.usaha_id, c.nama as customernm, u.nama||' - '||cu.notes as usahanm, cu.def_pajak_id ", false);
		$this->db->from('pad_customer_usaha cu');
		$this->db->join('pad_customer c', 'c.id=cu.customer_id');
		$this->db->join('pad_usaha u', 'u.id=cu.usaha_id');
		$this->db->like(array("lower(get_nopd(cu.id, {$formatted}))" => strtolower($xnopd)), false); 
		
		if ($usaha_id)
			$this->db->where(array('u.id' => $usaha_id)); 
		else
			$this->db->where_not_in('u.id', array(pad_reklame_id(), pad_air_tanah_id())); 
		
		$this->db->order_by('nopd'); 
		
		$query = $this->db->get();
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function max_konter($cid) {
		$this->db->select_max('konterid', 'nomor');
		$this->db->where('customer_id',$cid);
		$konterid = $this->db->get($this->tbl)->row()->nomor;
		$konterid++;
		
		return $konterid;
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