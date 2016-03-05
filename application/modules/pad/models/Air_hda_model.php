<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class air_hda_model extends CI_Model {
	private $tbl = 'pad_air_hda';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		// jangan dirubah urutannya... tapi gpp udah pake fungsi lain
		$this->db->order_by('zona_id'); 
		$this->db->order_by('manfaat_id'); 
		$this->db->order_by('volume_id', 'desc'); 
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
	
	function get_hda($zona, $manfaat, $volume) {
		$this->db->select('volume_id, hda');
		$this->db->where('zona_id',$zona);
		$this->db->where('manfaat_id',$manfaat);
		$this->db->where('volume_id',$volume);
		$this->db->order_by('volume_id', 'desc'); 
		
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
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