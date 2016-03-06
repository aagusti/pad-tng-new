<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pajak_model extends CI_Model {
	private $tbl = 'pad_jenis_pajak';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('nama'); 
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function get_select($pajak_id)
	{
		/*
		$this->db->select('id, nama');
		$this->db->where(array('enabled'=>'1', 'usaha_id'=>'')); 
		$this->db->order_by('nama'); 
		$query = $this->db->get($this->tbl);
		*/
		
		$this->db->select('p1.id, p1.nama');
		$this->db->from('pad_jenis_pajak p1');
		$this->db->join('pad_jenis_pajak p2', 'p1.usaha_id=p2.usaha_id');
		$this->db->where(array('p1.enabled'=>'1', 'p2.id'=>$pajak_id)); 
		$this->db->order_by('p1.nama'); 
		$query = $this->db->get();
		
		//die(last_query());
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}
	
	function get_select2($usaha_id)
	{
		$this->db->select('id, nama');
		$this->db->where(array('enabled'=>'1', 'usaha_id'=>$usaha_id)); 
		$this->db->order_by('nama'); 
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
	
	function get_jatuhtempo($usaha_id)
	{
        $this->db->select_max('jatuhtempo');
		$this->db->where(array('enabled'=>'1', 'usaha_id'=>$usaha_id)); 
		$query = $this->db->get($this->tbl);

		if($query->num_rows()!==0)
		{
			return $query->row()->jatuhtempo;
		}
		else
			return FALSE;
	}
	
	function get_rek_denda($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row()->rekdenda_id;
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