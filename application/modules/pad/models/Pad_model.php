<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pad_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }

  function get_pemda() {
    $qry = "select * from pad.pad_pemda where enabled=1 limit 1;";

    $query = $this->db->query($qry);
    if($query->num_rows()!==0)
    {
      return $query->row();
    }
    else
      return FALSE;
  }

  //login mod
  function check_user($uid, $pwd) {		
    $qry = "select distinct
        --get_npwpd(c.id, true) userid, 
        --up.*, ug.*
        u.id userid, u.nama username, u.nip, u.passwd, 
        c.id cust_id, c.nama cust_nm
      from user_pad up
      inner join pad_customer c on c.id=up.customer_id

      inner join users u on u.id=up.user_id
      inner join user_groups ug on ug.user_id=up.user_id

      inner join groups g on g.id=ug.group_id
      inner join group_modules gm on gm.group_id=ug.group_id

      inner join modules m on m.id=gm.module_id
      inner join apps a on a.id=m.app_id

      where 1=1
      and up.disabled <> 1 and g.kode = 'user' 
      and get_npwpd(c.id, false) = ? and up.passwd = ?";

    $rows = $this->db->query($qry, array($uid, $pwd));
    if($rows->num_rows() == 1) {
      return $rows->row();
    } else {
      return FALSE;
    }
  }
  
  // ----
  // cek apakah wp memiliki usaha yg disebutkan
  function check_cu($customer_id, $usaha_id)
  {
    $qry = "select cu.id 
      from pad_customer_usaha cu
      inner join pad_customer c on cu.customer_id=c.id
      where cu.customer_id = ? ";
      
    if (!empty($usaha_id))
      $qry .= "and cu.usaha_id in ({$usaha_id}) ";
    else
      $qry .= "and cu.usaha_id not in (".pad_reklame_id().", ".pad_air_tanah_id().") ";
      
    $query = $this->db->query($qry, array($customer_id));
    if($query->num_rows()!==0)
    {
      return $query->result();
    }
    else
      return FALSE;
  }
  
  
  function sptpd_get_cu($customer_id, $usaha_id)
  {
    // terlanjur sayang
    $qry = "select cu.id customer_usaha_id, cu.customer_id, cu.usaha_id, get_npwpd(c.id, true) as npwpd, get_npwpd(c.id, false) as npwpd2, c.nama as customernm,
      cu.konterid, cu.zona_id, cu.manfaat_id , cu.golongan_id, cu.kelompok_usaha_id , u.so, cu.def_pajak_id,
      cast(u.nama||' ('||cu.konterid||' | '||coalesce(cu.opnm,u.nama)||')' as character varying) as usahanm,
            
            c.wpnama, c.wpalamat 

      from pad_customer_usaha cu
      inner join pad_customer c on cu.customer_id=c.id
      inner join pad_usaha u on cu.usaha_id=u.id
      where cu.customer_id = ?   
      and cu.id = $usaha_id";

    if (wp_login() && empty($usaha_id))
      $qry .= "and u.id not in (".pad_reklame_id().", ".pad_air_tanah_id().") ";
    if (wp_login() && !empty($usaha_id))
      $qry .= "and u.id in ({$usaha_id}) ";
        
    $qry .= "order by c.rp,c.pb,c.formno, cu.usaha_id, cu.konterid";
    $query = $this->db->query($qry, array($customer_id));
    
    if($query->num_rows()!==0)
    {
      return $query->result();
    }
    else
      return FALSE;
  }

  //fungsi baru mencari WP & ID WP Saja
  function sptpd_get_c_id($customer_id)
  {	
    $qry = "select id as customer_id, nama as customernm, wpnama, wpalamat, get_npwpd(c.id, true) as npwpd, get_npwpd(c.id, false) as npwpd2 
        from pad_customer c where id = $customer_id";	

    $query = $this->db->query($qry, array($customer_id));
    
    if($query->num_rows()!==0)
    {
      return $query->result();
    }
    else
      return FALSE;
  }

  function sptpd_get_usaha_id($cu_id) 
  {
    $qry = "select cu.usaha_id
      from pad_customer_usaha cu
      where cu.id = ?";

    $query = $this->db->query($qry, array($cu_id));
    if($query->num_rows()!==0)
    {
      return $query->row()->usaha_id;
    }
    else
      return FALSE;
  }
  
  function sptpd_get_pajak_detail($pajak_id, $pajak_tmt, $minimal_omset)
  {
    if ($minimal_omset > 0){
    $qry = "select pt.pajak_id, 
      pt.tarif, pt.minimal_omset, pt.reklame, 
      --pt.id, pt.tmt, 
      --p.carahitung, 
      p.jatuhtempo, p.multiple, p.masapajak, r.id rekening_id, substr(r.kode,1,8) as kode, r.insidentil
      from pad_tarif_pajak pt  
      inner join pad_jenis_pajak p on p.id=pt.pajak_id 
      inner join pad_rekening r on r.id=p.rekening_id 

      where pt.pajak_id = $pajak_id
      and pt.tmt <=  '$pajak_tmt' 
      and pt.minimal_omset <= $minimal_omset
      order by pt.minimal_omset desc, pt.tmt desc  
      limit 1";
    }else{
    $qry = "select pt.pajak_id, 
      pt.tarif, pt.minimal_omset, pt.reklame, 
      --pt.id, pt.tmt, 
      --p.carahitung, 
      p.jatuhtempo, p.multiple, p.masapajak, r.id rekening_id, substr(r.kode,1,8) as kode, r.insidentil
      from pad_tarif_pajak pt  
      inner join pad_jenis_pajak p on p.id=pt.pajak_id 
      inner join pad_rekening r on r.id=p.rekening_id 

      where pt.pajak_id = $pajak_id
      and pt.tmt <=  '$pajak_tmt' 
      order by pt.minimal_omset asc , pt.tmt desc  
      limit 1";
    }

    $query = $this->db->query($qry, array($pajak_id, $pajak_tmt));		
    if($query->num_rows()!==0)
    {
      return $query->row();
    }
    else
      return FALSE;
  }
  
  /*
  function sptpd_get_pajak($usaha_id,$pajak_id)
  {
    if ($pajak_id){
    $qry = "select p.id, p.nama, pt.reklame, p.usaha_id
      --, r.kode,p.kodesub, r.rekeningnm, p.jatuhtempo, 
      --p.carahitung, p.usaha_id, p.multiple
      FROM pad_jenis_pajak p
      INNER JOIN pad_rekening r on p.rekening_id=r.id
            
            inner join pad_tarif_pajak pt on pt.pajak_id=p.id -- bogor kerjaannya
      
            where usaha_id=? and p.enabled=1 and r.enabled=1 and pajak_id=$pajak_id 
      order by r.kode, p.nama";
    }
    else {
      $qry = "select p.id, p.nama, pt.reklame, p.usaha_id
      --, r.kode,p.kodesub, r.rekeningnm, p.jatuhtempo, 
      --p.carahitung, p.usaha_id, p.multiple
      FROM pad_jenis_pajak p
      INNER JOIN pad_rekening r on p.rekening_id=r.id
            
            inner join pad_tarif_pajak pt on pt.pajak_id=p.id -- bogor kerjaannya
      
            where usaha_id=? and p.enabled=1 and r.enabled=1  
      order by r.kode, p.nama";
    }
    $query = $this->db->query($qry, array($usaha_id));
    if($query->num_rows()!==0)
    {
      return $query->result();
    }
    else
      return FALSE;
  }
  */

  function sptpd_get_pajak($usaha_id)
  {
    $qry = "select p.id, p.nama, pt.reklame, p.usaha_id
      --, r.kode,p.kodesub, r.rekeningnm, p.jatuhtempo, 
      --p.carahitung, p.usaha_id, p.multiple
      FROM pad_jenis_pajak p
      INNER JOIN pad_rekening r on p.rekening_id=r.id
            
            inner join pad_tarif_pajak pt on pt.pajak_id=p.id -- bogor kerjaannya
      
            where usaha_id=? and p.enabled=1 and r.enabled=1  
      order by r.kode, p.nama";
    $query = $this->db->query($qry, array($usaha_id));
    if($query->num_rows()!==0)
    {
      return $query->result();
    }
    else
      return FALSE;
  }
}

/* End of file _model.php */