<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reklame_media_model extends CI_Model {
	private $tbl = 'pad_reklame_media';
	
	function __construct() {
		parent::__construct();
	}
	
	function get_all()
	{
		$this->db->order_by('id'); 
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

	function get_select()
	{
		$this->db->order_by('media');  
		$query = $this->db->get($this->tbl);
		if($query->num_rows()!==0)
		{
			return $query->result();
		}
		else
			return FALSE;
	}

	function get_select_on_spt($jenis_pajak_id, $kelas_jalan_id, $masadari)
	{
		$sql = "select distinct md.jenis_pajak_id, kj.id kelas_jalan_id, nsr.id nsr_id, md.id, md.media, md.singkatan, md.masa, md.keterangan	
				from pad_reklame_media md
				inner join pad_reklame_nsr nsr on nsr.media_id = md.id
				inner join pad_reklame_kelas_jalan kj on kj.id = nsr.kelas_jalan_id ";
        $where = "where nsr.enabled = 1 and nsr.tmt <= '$masadari' and jenis_pajak_id = $jenis_pajak_id and kelas_jalan_id = $kelas_jalan_id";
		$query = $this->db->query($sql.$where);
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