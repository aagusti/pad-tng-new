<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class groups_model extends CI_Model {
	private $tbl = 'groups';
	
	function __construct() {
		parent::__construct();
	}
		
	function get_all()
	{	$sql = "select *
				from groups
				order by id";
				
		$query = $this->db->query($sql);
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
	
	function is_locked($id)
	{
		$this->db->where('id',$id);
		$this->db->where('locked',1);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return TRUE;
		}
		else
			return FALSE;
	}
	
	function leave_one_super_admin() {
		$this->db->where('group_id ',1);  //--> id super admin
		$query = $this->db->get('user_groups');
		if($query->num_rows==1)
		{
			return TRUE;
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