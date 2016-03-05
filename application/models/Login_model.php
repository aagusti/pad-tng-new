<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login_model extends CI_Model
{
    private $tbl = 'app.users';

    function __construct()
    {
        parent::__construct();
    }
    
    function check_user($uid)
    {
        /*$fields = explode(',', POS_FIELD);
        $field  = "";
        $join   = "";
        foreach ($fields as $f) {
            $f = trim($f);
            $join .= " AND u.$f=tp.$f ";
            $field .= "u.$f ,";
        }
        ;*/
        $qry  = "select u.id userid, u.nama username, u.nip, u.passwd, u.last_login
                from app.users u 
                where u.userid='$uid' and disabled<>1
                limit 1";
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function check_group($uid)
    {
        $qry = "select g.*
        from app.groups g
                     inner join app.user_groups ug on g.id=ug.group_id
                 
        where ug.user_id='$uid'
            order by g.id limit 1 ";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function check_user_app()
    {
        $uid  = $this->session->userdata('userid');
        $mid  = $this->session->userdata('app_id');
        if($mid <> '')
            $mid = ' and m.app_id=' . $mid ;
        
        $qry  = "select distinct a.id app_id, a.app_path, g.id as group_id, g.kode as group_kode, g.nama as group_nama  
            from app.user_groups ug 
                inner join app.groups g on g.id=ug.group_id 
                inner join app.group_modules gm on g.id=gm.group_id
                inner join app.modules m on gm.module_id=m.id
                inner join app.apps a on m.app_id=a.id
            where ug.user_id={$uid} {$mid} and (gm.reads=1 or gm.writes=1 or gm.deletes=1 or gm.inserts=1)
                order by a.id";
                
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            //20140120 -- biar nanti bisa pilih module kalo usernya ada di lebih dari 1 module
            $ret = new stdClass();
            $ret = $rows->row();
            $ret->modcnt = $rows->num_rows();
            return $ret;
        } else {
            return FALSE;
        }
    }
    
    function get_module($app_id)
    {
        $qry = "select * 
                from app.apps a
                where a.id=$app_id
                limit 1";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function aktif_tahun($app_id)
    {
        $qry = "select * 
                from app.apps a
                inner join app.app_status s on a.id=s.app_id
                where s.step<>'closing' and a.id=$app_id
                order by a.id, s.tahun
                limit 1";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function inaktif_tahun($app_id)
    {
        $qry = "select max(tahun) tahun, step
                from app.apps a
                inner join app.app_status s on a.id=s.app_id
                where s.step='closing' and a.id=$app_id
                group by 2
                limit 1";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function get_appid($m)
    {
        $qry  = "select id 
                from app.apps a
                where a.app_path='$m'
                limit 1";
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
}