<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sspd_model extends CI_Model {
	private $tbl = 'pad_sspd';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('sspdno'); 
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
	
	function get_by_spt($spt_id)
	{
		$this->db->where('invoice_id',$spt_id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row();
		}
		else
			return FALSE;
	}
	
	function generate_sspdno($tahun) {
		$this->db->select_max('sspdno', 'nomor');
		$this->db->where('tahun',$tahun);
		$sspdno = $this->db->get($this->tbl)->row()->nomor;
		$sspdno++;
		
		return $sspdno;
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
	
	function update_by_spt($spt_id, $data) {
		$this->db->where('spt_id', $spt_id);
		$this->db->update($this->tbl,$data);
	}
	
	function delete($id) {
		$this->db->where('invoice_id', $id);
		$this->db->delete($this->tbl);
	}
	
	function delete_by_spt($spt_id) {
		$this->db->where('spt_id', $spt_id);
		$this->db->delete($this->tbl);
	}
    
	function cancel_by_spt($spt_id) {
        // sebenarnya kurang tepat (akan ngupdate semua sspd dgn spt_id yg sama), tapi gpp lah :D
		$this->db->where('spt_id', $spt_id);
		$this->db->update($this->tbl, array('is_valid'=>0));
        
		$this->db->where('id', $spt_id);
		$this->db->update('pad_spt', array('status_pembayaran'=>0));
	}
}

/* End of file _model.php */