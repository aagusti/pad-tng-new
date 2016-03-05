<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class kecamatan_model extends CI_Model {
	private $tbl = 'pad_kecamatan';
	
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
	
	function get_select()
	{
		$this->db->select('id, nama');
		$this->db->where(array('enabled'=>'1')); 
		$this->db->order_by('nama'); 
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}

	function get_select_zona($kec_id)
	{
		$query = $this->db->query("select zona_id, z.nama from pad_kecamatan k join pad_air_zona z on k.zona_id=z.id where k.id=$kec_id");
		//$query = $this->db->query($this->db);
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