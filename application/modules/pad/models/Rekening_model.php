<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rekening_model extends CI_Model {
    private $tbl = 'pad.pad_rekening';

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

    function get_select($levelid = false, $issummary = false)
    {
        if($levelid)
            $this->db->where(array('levelid'=>$levelid));
        if($issummary)
            $this->db->where(array('issummary'=>$issummary));

        $this->db->select('id, nama, pad.get_rekening(kode) kode', false);
        $this->db->where(array('enabled'=>'1'));
        $this->db->order_by('kode');
        $query = $this->db->get($this->tbl);
        if($query->num_rows()!==0)
        {
            return $query->result();
        }
        else
            return FALSE;
    }
    
    function get_select_padl($levelid = false, $issummary = false)
    {
        if($levelid)
            $this->db->where(array('levelid'=>$levelid));
        if($issummary)
            $this->db->where(array('issummary'=>$issummary));

        $this->db->where("kode ilike '411%'", null, false);
        $this->db->select('id, substring(kode,1,5) as kode, nama', false);
        $this->db->where(array('enabled'=>'1'));
        $this->db->order_by('kode');
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