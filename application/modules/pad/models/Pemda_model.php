<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pemda_model extends CI_Model {
  private $tbl = 'pad.pad_pemda';
  
  function __construct() {
    parent::__construct();
  }
  
  function get_all()
  {
    $this->db->order_by('pemda_nama');    
    $query = $this->db->get($this->tbl);
    if($query->num_rows()!==0)
    {
      return $query->result();
    }
    else
      return FALSE;
  }
  
  function get_one()
  {
    //$this->db->where(array('enabled' => '1'));    
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
    $this->db->select('id, pemda_nama');
    $this->db->order_by('pemda_nama'); 
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