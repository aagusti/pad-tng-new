<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class penerimaan_model extends CI_Model {
	private $tbl_terima = 'pad_terima';
	private $tbl_terima_line = 'pad_terima_line';
	
	function __construct() {
		parent::__construct();
	}

	function max_terimano($tahun) {	
		$this->db->select_max('terimano', 'nomor');
		$this->db->where('tahun',$tahun);
		$sptno = $this->db->get($this->tbl_terima)->row()->nomor;
		$sptno++;
		
		return $sptno;
	}
				
	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl_terima);
		if($query->num_rows()!==0)
		{
			return $query->row();
		}
		else
			return FALSE;
	}
	
	function get_terima_id_by_spt($spt_id) {
		$this->db->where('spt_id', $spt_id);
		$this->db->limit(1);
		$query = $this->db->get($this->tbl_terima_line);
		if($query->num_rows()!==0)
		{
			return $query->row()->terima_id;
		}
		else
			return FALSE;
	}
	
	
	//-- terima
	function update_setoran($terima_id) {		
		// hitung total setoran
		$this->db->select_sum('amount', 'setoran');
		$this->db->where('terima_id', $terima_id);
		$new_setoran = $this->db->get($this->tbl_terima_line)->row()->setoran;
		$new_setoran = empty($new_setoran) ? 0 : $new_setoran;
		
		$this->db->where('id', $terima_id);
		$this->db->update($this->tbl_terima, array('jmlterima' => $new_setoran));
		
		return $new_setoran;
	}
	
	function save($data) {
		$this->db->insert($this->tbl_terima,$data);
		return $this->db->insert_id();
	}
	
	function update($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->tbl_terima,$data);
	}
	
	function delete($id) {
		$this->db->where('terima_id', $id);
		$this->db->delete($this->tbl_terima_line);
		
		$this->db->where('id', $id);
		$this->db->delete($this->tbl_terima);
	}
	
	function delete_if_no_spt($terima_id) {	
		$this->db->where('terima_id', $terima_id);
		$query = $this->db->get($this->tbl_terima_line);
		if($query->num_rows() < 1)
			$this->delete($terima_id);
	}
	
	// -- terima line
	function save2($data) {
		$this->db->insert($this->tbl_terima_line,$data);
		return $this->db->insert_id();
	}
	
	function update2($id, $data) {
		$this->db->where('id', $id);
		$this->db->update($this->tbl_terima_line,$data);
	}
	
	function delete2($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->tbl_terima_line);
	}
	
	function delete2_by_spt($spt_id) {
		$this->db->where('spt_id', $spt_id);
		$this->db->delete($this->tbl_terima_line);
	}
}

/* End of file _model.php */