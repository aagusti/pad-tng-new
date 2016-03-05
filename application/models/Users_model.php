<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class users_model extends CI_Model {
	private $tbl = 'app.users';
	
	function __construct() {
		parent::__construct();
	}
		
	function get_all() {
        $sql = "select u.*
				from app.users u
				order by u.disabled desc, u.nama";
				
        $this->db->trans_start();
		$query = $this->db->query($sql);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->result();
        else
            return false;
	}
	
	function get_by_group($group_id, $in_group=false) {	
        $sql = "select * from (
					select 1 in_group, u.*, ".$group_id." group_id
					from app.users u
					inner join app.user_groups ug on ug.user_id=u.id
					where group_id=".$group_id."
					union
					select 0 as in_group, u.*,".$group_id." group_id
					from users u
					where u.id not in (select user_id from user_groups where group_id=".$group_id.")
				) as gu
				".($in_group? " where in_group=1 ": "")."
				order by in_group desc, disabled desc, nama";
				
        $this->db->trans_start();
		$query = $this->db->query($sql);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->result();
        else
            return false;
	}
	
	function get($id)
	{
        $this->db->trans_start();
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->row();
        else
            return false;
	}
	
	//-- admin
	function save($data) {
        $this->db->trans_start();
		$this->db->insert($this->tbl,$data);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return $this->db->insert_id();
        else
            return false;
	}
	
	function update($id, $data) {
        $this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update($this->tbl,$data);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
	
	function delete($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete($this->tbl);
        $this->db->trans_complete();
            
        if($this->db->trans_status())
            return true;
        else
            return false;
	}
}

/* End of file _model.php */