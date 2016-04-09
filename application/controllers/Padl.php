<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Padl extends CI_Controller
{
    private $module = 'eis-padl';
    
    function __construct()
    {
        parent::__construct();        

        /*$this->load->model(array(
            'apps_model',
            'login_model',
            'pbbm_model'
        ));
        $this->pbbm_model->set_userarea();*/
    }

    public function get_dashboard($awal, $akhir, $group)
    {
      $amt = $this->rest_client->get('transaksi/realisasi',
                                      array('awal'=>$awal,
                                            'akhir'=>$akhir,
                                            'group'=>$group));
      return $amt;
    }    
    
    public function index()
    {
      $this->rest_client->initialize(array( 'server'=>RPC_SERVER,
                                            'http_user'=>RPC_USER,
                                            'http_pass'=>RPC_PASS,
                                            'http_auth'=> RPC_AUTH
                                          ));
                                          //'api_key'         => 'Setec_Astronomy'
                                          //'api_name'        => 'X-API-KEY'
                                          //'ssl_verify_peer' => TRUE,
                                          //'ssl_cainfo'      => '/certs/cert.pem'
      $today = date('Ymd');
      $year = date('Y').'0101';
      $month = date('Ym').'01';
      $dow = date('w');
      $week = date('Ymd', strtotime("-$dow days"));
      
      $data['current'] = 'padl';
      $data['akhir'] = $today;
      $data['awal'] = $year;
      
      $val = $this->get_dashboard($today,$today,3);
      
      $data['today_amt'] = number_format(
                          (double)$val[0]->pokok, 0, ',', '.');
      $data['today_trans'] = number_format(
                          (double)$val[0]->jumlah, 0, ',', '.');
      
      
      $val = $this->get_dashboard($week,$today,3);
      $data['week_amt']  = number_format(
                          (double)$val[0]->pokok, 0, ',', '.');;
      $data['week_trans'] = number_format(
                          (double)$val[0]->jumlah, 0, ',', '.');
      
      
      $val = $this->get_dashboard($month,$today,3);
      $data['month_amt'] = number_format(
                          (double)$val[0]->pokok, 0, ',', '.');
      $data['month_trans'] = number_format(
                          (double)$val[0]->jumlah, 0, ',', '.');
      
      $val = $this->get_dashboard($year,$today,3);
      $data['year_amt']  = number_format(
                          (double)$val[0]->pokok, 0, ',', '.');
      $data['year_trans'] = number_format(
                          (double)$val[0]->jumlah, 0, ',', '.');
      $this->load->view('vpadl', $data);
    }
}
