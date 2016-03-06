<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class app_status_model extends CI_Model {
	private $tbl = 'app_status';
	
	function __construct() {
		parent::__construct();
	}
		
	function get_all($app_id)
	{	$sql = "select *
				from app_status
				where app_id=".$app_id."
				order by tahun";
				
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
	
	function cek_tahun($app_id, $tahun) 
	{
		$this->db->where('app_id',$app_id);
		$this->db->where('tahun',$tahun);
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return TRUE;
		}
		else
			return FALSE;
	}
	
	//-- admin
	function save($app_id, $tahun, $step) {
		//set other step=closing
		if($step<>'closing') {
			$this->db->where('app_id',$app_id);
			$this->db->update($this->tbl,array('step'=>'closing'));
		}
		
		//then insert data
		$data = array('app_id' => $app_id, 'tahun' => $tahun, 'step' => $step);
		$this->db->insert($this->tbl,$data);
		return $this->db->insert_id();
	}
	
	function update($app_id, $tahun, $step) {
		//set other step=closing
		if($step<>'closing') {
			$this->db->where('app_id',$app_id);
			$this->db->update($this->tbl,array('step'=>'closing'));
		}

		//then update current
		$data = array('app_id' => $app_id, 'tahun' => $tahun, 'step' => $step);
		$this->db->where('app_id',$app_id);
		$this->db->where('tahun',$tahun);
		$this->db->update($this->tbl,$data);
	}
	
	function delete($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->tbl);
	}
}

/* End of file _model.php */