<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class usaha_model extends CI_Model {
	private $tbl = 'pad_usaha';
	
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
	

	function get_without()
	{
		$this->db->order_by('nama'); 
		$pad_reklame_id  = pad_reklame_id();
		$pad_ppj_id = 5;
        $this->db->where("id not in ({$pad_reklame_id},{$pad_ppj_id })");
		$query = $this->db->get($this->tbl);


		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}

	function get_select($so=false)
	{
		$this->db->select('id, nama');
        if($so=='S' || $so=='O')
            $this->db->where(array('enabled'=>'1', 'so'=>$so)); 
        else
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

	function get_select_one($id)
	{
		$this->db->select('id, nama');
        $this->db->where('id',$id);
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
	
	function get_so($id)
	{
		$this->db->select('so');
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->row()->so;
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