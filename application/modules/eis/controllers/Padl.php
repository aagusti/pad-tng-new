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

    public function get_dashboard($awal, $akhir, $group){
      $amt = $this->rest_client->get('transaksi/realisasi',
                                      array('awal'=>$awal,
                                              'akhir'=>$akhir,
                                              'group'=>$group));
      return $amt;
    }    
    
    public function index()
    {
        $this->rest_client->initialize(array('server'=>RPC_SERVER,
                                            'http_user'=>RPC_USER,
                                            'http_pass'=>RPC_PASS,
                                            'http_auth'=> RPC_AUTH
                                            ));
                
                                            //'api_key'         => 'Setec_Astronomy'
                                            //'api_name'        => 'X-API-KEY'
                                            //'ssl_verify_peer' => TRUE,
                                            //'ssl_cainfo'      => '/certs/cert.pem'
                
        $data['current'] = 'padl';
        $today = date('Ymd');
        $year = date('Y').'0101';
        $month = date('Ym').'01';
        $dow = date('w');
        $week = date('Ymd', strtotime("-$dow days"));
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
    
    public function get_data()
    {
        $this->load->model('pbbEisModel', 'pbb');
        
        /* Today PBB */
        $today                = $this->pbb->today_pbb();
        $data['today_amount'] = number_format($today["amount_transaksi"], 0, ',', '.');
        $data['today_trans']  = number_format($today["jumlah_transaksi"], 0, ',', '.');
        $data['today_cap']    = $this->pbb->today_cap;
        
        /* Week PBB */
        $week                = $this->pbb->week_pbb();
        $data['week_amount'] = number_format($week["amount_transaksi"], 0, ',', '.');
        $data['week_trans']  = number_format($week["jumlah_transaksi"], 0, ',', '.');
        $data['week_cap']    = $this->pbb->week_cap;
        
        /* Month PBB */
        $month                = $this->pbb->month_pbb();
        $data['month_amount'] = number_format($month["amount_transaksi"], 0, ',', '.');
        $data['month_trans']  = number_format($month["jumlah_transaksi"], 0, ',', '.');
        $data['month_cap']    = $this->pbb->month_cap;
        
        /* Year PBB */
        $year                = $this->pbb->year_pbb();
        $data['year_amount'] = number_format($year["amount_transaksi"], 0, ',', '.');
        $data['year_trans']  = number_format($year["jumlah_transaksi"], 0, ',', '.');
        $data['year_cap']    = $this->pbb->year_cap;
        
        
        $data['current'] = 'dashboard';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('dashboardview', $data);
        
    }
}
