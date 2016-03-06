<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            //'apps_model',
            'login_model'
        ));
    }
    
    public function index()
    {
        if (is_login()) {
            redirect(active_module_url());
        } else {
            redirect('padl');
        }
    }
    
    function login()
    {        
        $this->session->set_userdata('login', FALSE);
        $this->session->set_userdata('canchangemod', FALSE);
        $this->session->unset_userdata('groupname');
        
        $data['current'] = 'login';
        $data['faction'] = site_url('login');
        
        //5:53 PM 2/27/2016 aagusti
        //$data['app_id']      = DEF_MODULE; // from config
        //$data['app_enabled'] = SELECT_MODULE; // from config
        
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('userid', 'User ID', 'required');
        $this->form_validation->set_rules('passwd', 'Password', 'required');
        //$this->form_validation->set_rules('app_id', 'Module', 'required');
        
        $data['dt']['userid'] = $this->input->post('userid');
        $data['dt']['passwd'] = $this->input->post('passwd');
        if ($this->form_validation->run() == TRUE) {
            $uid   = $this->input->post('userid');
            $pwd   = $this->input->post('passwd');
            $login = $this->login_model->check_user($uid);
            
            if ($login) {
                if ($login->passwd == $pwd) {
                    //last login & verifikasi bahwa sedang login
                    $last_login = date('Y-m-d h:i:s');
                    $qry  = "update app.users set last_login='$last_login' where userid='$uid' ";
                    $this->db->query($qry);
                    $this->session->set_userdata('uid', $uid);
                    $this->session->set_userdata('userid', $login->userid);
                    $this->session->set_userdata('username', htmlspecialchars($login->username));
                    $last_login = date('d-m-Y | H:i:s',strtotime($login->last_login ));
                    $this->session->set_userdata('last_login', $last_login);
                    $this->session->set_userdata('nip', $login->nip);
                    $this->session->set_userdata('login', TRUE);
                    $rs = $this->login_model->check_group($login->userid);
                    if ($rs) {
                        $this->session->set_userdata('groupid', $rs->id);
                        $this->session->set_userdata('groupkd', $rs->kode);
                        $this->session->set_userdata('groupname', $rs->nama);
                        
                        
                        if (is_super_admin()) {
                            $this->session->set_userdata('active_module', 'admin');
                            $this->session->set_userdata('app_id', $this->login_model->get_appid('admin'));
                            
                        } else {
                            if ($uapp = $this->login_model->check_user_app()) {
                                $this->session->set_userdata('app_id', $uapp->app_id);
                                $this->session->set_userdata('active_module', $uapp->app_path);
                                
                                $this->session->set_userdata('groupid', $uapp->group_id);
                                $this->session->set_userdata('groupkd', $uapp->group_kode);
                                $this->session->set_userdata('groupname', htmlspecialchars($uapp->group_nama));
                                
                                if($uapp->modcnt > 1)
                                    $this->session->set_userdata('canchangemod', true);

                            } else {
                                $this->session->set_userdata('login', FALSE);
                            }
                        }
                        
                        if ($this->session->userdata('login') == TRUE) {
                            $this->session->set_flashdata('msg_success', 'Selamat datang, ' . htmlspecialchars($login->username) . '.');
                            redirect(active_module_url());
                        }
                    }
                } else 
                    $this->session->set_flashdata('msg_error', 'User ID atau Password salah!');
            } else 
                $this->session->set_flashdata('msg_error', 'User ID tidak terdaftar atau dimatikan!');
            redirect('login');
        } else {
            $this->session->set_flashdata('msg_warning', 'Harap melengkapi isian!');
        }
        
        $this->load->view('vlogin', $data);
    }
    
    function dologout()
    {
        $this->session->sess_destroy();
        
        $this->session->set_flashdata('msg_info', 'Anda telah logout. Terimakasih.');
        redirect('login');
    }
    
    function info()
    {
        $data['current'] = '';
        $this->load->view('vinfo', $data);
    }
    
    function change_module()
    {
        $m = $this->uri->segment(2);
        
        //$this->session->set_userdata('active_module', 'admin');
        
        //$id = $this->login_model->get_appid('admin');
        //if ($id) $this->session->set_userdata('app_id', $id->id);
        
        if ($m) {
            $id = $this->login_model->get_appid($m);
            if ($id) {
                $this->session->set_userdata('active_module', $m);
                $this->session->set_userdata('app_id', $id->id);
            }
        }
        redirect(active_module_url());
    }
}

/* End of file */
