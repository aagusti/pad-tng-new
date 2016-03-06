<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class stpd_model extends CI_Model {
	private $tbl = 'pad_stpd';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('stpdno'); 
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
	/* 
	function get_by_spt($spt_id)
	{
		$this->db->where('spt_id',$spt_id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row();
		}
		else
			return FALSE;
	}
	 */
	function generate_stpdno($tahun) {
		
		$query = $this->db->query("select max(stpdno) as nomor from pad_stpd where tahun= $tahun");
		foreach ($query->result() as $row)
		{
		   $stpdno = $row->nomor;
		}
		if ($query->num_rows() > 0)
		{
			$stpdno = $stpdno+1;
		}
		else
		{
			$stpdno = 1;
		}
		return $stpdno;
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
		$this->db->query("delete from pad_invoice where source_id=$id and source_nama='pad_stpd'");
	}
	
	function delete_by_spt($spt_id) {
		$this->db->where('spt_id', $spt_id);
		$this->db->delete($this->tbl);
	}
}

/* End of file _model.php */