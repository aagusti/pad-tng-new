<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//author: irul()

class Module_auth {
	public $create = FALSE;
	public $read   = FALSE;
	public $update = FALSE;
	public $delete = FALSE;
	public $exec   = FALSE;
	public $upload = FALSE;
	
	public $msg_create = 'Anda tidak memiliki hak akses untuk menambah data.';
	public $msg_read   = 'Anda tidak memiliki hak akses untuk membaca data pada modul tersebut.';
	public $msg_update = 'Anda tidak memiliki hak akses untuk mengubah data.';
	public $msg_delete = 'Anda tidak memiliki hak akses untuk menghapus data.';
	public $msg_exec   = 'Anda tidak memiliki hak akses untuk mengeksekusi.';
	public $msg_upload = 'Anda tidak memiliki hak akses untuk mengunggah dokumen.';
	
	public function __construct($params) { 
		$CI =& get_instance();
		
		$CI->load->library('session');
		//if($CI->session->userdata('groupname')=='SA') {
		if(is_super_admin()) {
			$this->create = TRUE;
			$this->read   = TRUE;
			$this->update = TRUE;
			$this->delete = TRUE;
			$this->exec   = TRUE;
			$this->upload = TRUE;
		} else {
			//if(!$CI->session->userdata('groupid')=='') {
			
				$qry = "select gm.reads, gm.writes, gm.deletes, gm.inserts 
						from group_modules gm
						inner join modules m on m.id=gm.module_id
						where 1=1
						and gm.group_id=".sipkd_group_id()."
						--and gm.module_id=1	
						and m.kode='".$params['module']."'
						and m.app_id=".sipkd_app_id()."; ";
				$auth = $CI->db->query($qry);
				if($auth->num_rows()!==0) {
					$auth1 = $auth->row();
					$this->create = ($auth1->inserts ==1 ? TRUE : FALSE);
					$this->read   = ($auth1->reads   ==1 ? TRUE : FALSE);
					$this->update = ($auth1->writes  ==1 ? TRUE : FALSE);
					$this->delete = ($auth1->deletes ==1 ? TRUE : FALSE);
					//$this->exec   = ($auth1->execute ==1 ? TRUE : FALSE);
					//$this->upload = ($auth1->upload  ==1 ? TRUE : FALSE);
				} 
			//}
		}
	}
}
?>
