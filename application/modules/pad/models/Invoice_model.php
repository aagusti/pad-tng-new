<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class invoice_model extends CI_Model {
	private $db_pad;
	private $tbl = 'pad_invoice';
	
	function __construct() {
		parent::__construct();
		$this->db_pad = $this->load->database('pad', TRUE);
	}
	
	//-- admin
	function save($data) {
		$this->db_pad->insert($this->tbl,$data);
		return $this->db_pad->insert_id();
	}
	
	function update($id, $data) {
		$this->db_pad->where('id', $id);
		$this->db_pad->update($this->tbl,$data);
	}
	
	function delete($id) {
		$this->db_pad->where('id', $id);
		$this->db_pad->delete($this->tbl);
	}
}

/* End of file _model.php */