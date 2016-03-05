<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class lap_penetapan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }

        $module = 'lap_penetapan';
        $this->load->library('module_auth', array(
            'module' => $module
        ));

        $this->load->model(array(
            'apps_model'
        ));

        $this->load->helper(active_module());
        $this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
    }

    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $data['current'] = 'penetapan';
        $data['apps']    = $this->apps_model->get_active_only();

        $select_data = $this->load->model('usaha_model')->get_select();
        $options     = array();
        if($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->id;
        }
        $js = 'id="usahaid" class="input-xlarge" required ';
        $data['select_usaha'] = form_dropdown('usahaid', $options, null, $js);

        $select_data = $this->load->model('sptpd_type_model')->get_select();
        $options     = array();
        $options[] = 'SEMUA';
        if($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->typenm;
        }
        $js = 'id="type_id" class="input-small" required ';
        $data['select_type_id'] = form_dropdown('type_id', $options, null, $js);

        $select_data = $this->load->model('rekening_model')->get_select(3);
        $options     = array();
        if($select_data)
        foreach ($select_data as $row) {
            $options[$row->id] = $row->kode.' | '.$row->kode;
        }
        $js = 'id="rekeningid" class="input-xlarge" required ';
        $data['select_rekening'] = form_dropdown('rekeningid', $options, null, $js);

        $options     = array(1=>'Tgl. Pendataan/SKPD',2=>'Tgl. Bayar');
        $js = 'id="rangetgl" class="input-medium" required ';
        $data['select_rangetgl'] = form_dropdown('rangetgl', $options, 1, $js);

        $this->load->view('vlap_penetapan', $data);
    }

    /* report */
    function show_rpt() {
        $cls_mtd_html = $this->router->fetch_class()."/cetak/html";
        $cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf";
        $data['rpt_html'] = active_module_url($cls_mtd_html). '?' . $_SERVER['QUERY_STRING'] ;
        $data['rpt_pdf']  = active_module_url($cls_mtd_pdf) . '?' . $_SERVER['QUERY_STRING'] ;
        $this->load->view('vjasper_viewer', $data);
    }

    function cetak() {
        $type = $this->uri->segment(4);

        $qs   = urldecode ($_SERVER['QUERY_STRING']);
        parse_str($qs, $qs_data);

        $params = array();
        // foreach ($qs_data as $key => $val)
            // $params[$key] = $val;

        $kondisi = $this->input->get('kondisi');
        if (!empty($kondisi))
            $kondisi = 'and s.type_id='.$this->input->get('kondisi');

        $rpt = $this->input->get('rpt');

        $tglawal       = date('Y-m-d', strtotime($this->input->get('tglawal')));
        $tglakhir      = date('Y-m-d', strtotime($this->input->get('tglakhir')));
        $tglawalbayar  = date('Y-m-d', strtotime($this->input->get('tglawalbayar')));
        $tglakhirbayar = date('Y-m-d', strtotime($this->input->get('tglakhirbayar')));
        $tglcetak      = date('Y-m-d', strtotime($this->input->get('tglcetak')));

        $ignore_html_pg = TRUE;
        switch ($rpt) {
            case 'tap_register':
            case 'tap_register_airtanah':
                $type_id = intval($this->input->get('type_id'));
                if ($type_id > 0)
                    $kondisi = 'and s.type_id='.$type_id;
                else $kondisi = '';

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali':
            case 'tap_kendali_blm_bayar':
                $type_id = intval($this->input->get('type_id'));
                if ($type_id > 0) {
                    $kondisi = 'and s.type_id='.$type_id;
                } else $kondisi = '';

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali_self':
            case 'tap_kendali_self_sdh_bayar':
                // $kondisi  = ' and s.type_id='.pad_dok_self_id();
                $kondisi  = ' and cd.usaha_id not in('.pad_reklame_id().','.pad_air_tanah_id().') ';
                $kondisi .= " and date(s.terimatgl) between '{$tglawal}' and '{$tglakhir}' ";

                $kondisi_bayar = " and date(ss.sspdtgl) between '{$tglawalbayar}' and '{$tglakhirbayar}' ";

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                    'kondisi_bayar' => $kondisi_bayar,
                );
                break;
            case 'tap_kendali_blm_bayar_self':
                // $kondisi = 'and s.type_id='.pad_dok_self_id();
                $kondisi  = ' and cd.usaha_id not in('.pad_reklame_id().','.pad_air_tanah_id().') ';
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali_airtanah':
            case 'tap_kendali_airtanah_sdh_bayar':
                $type_id = intval($this->input->get('type_id'));
                if ($type_id > 0)
                    $kondisi = 'and s.type_id='.$type_id;
                else $kondisi = '';

                $kondisi .= " and date(k.kohirtgl) between '{$tglawal}' and '{$tglakhir}' ";
                $kondisi_bayar = " and date(ss.sspdtgl) between '{$tglawalbayar}' and '{$tglakhirbayar}' ";

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                    'kondisi_bayar' => $kondisi_bayar,
                );
                break;
            case 'tap_kendali_airtanah_blm_bayar':
                $type_id = intval($this->input->get('type_id'));
                if ($type_id > 0)
                    $kondisi = 'and s.type_id='.$type_id;
                else $kondisi = '';

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_register_reklame':
                $type_id = intval($this->input->get('type_id'));
                if ($type_id > 0)
                    $kondisi = 'and s.type_id='.$type_id;
                else $kondisi = '';
                $naskah = trim($this->input->get('naskah'));
                $kondisi .= $naskah != "" ? " and s.r_judul ilike '%{$naskah}%' " : "";

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_register_reklame_jthtempo':
                $type_id = intval($this->input->get('type_id'));
                if ($type_id > 0)
                    $kondisi = 'and s.type_id='.$type_id;
                else $kondisi = '';
                $naskah = trim($this->input->get('naskah'));
                $kondisi .= $naskah != "" ? " and s.r_judul ilike '%{$naskah}%' " : "";

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali_reklame':
            case 'tap_kendali_reklame_sdh_bayar':
                $insidentil = intval($this->input->get('insidentil'));
                $type_id = intval($this->input->get('type_id'));
                $kondisi = '';
                if ($insidentil > -1)
                    $kondisi .= ' and r.insidentil='.$insidentil;
                if ($type_id > 0)
                    $kondisi .= ' and s.type_id='.$type_id;
                $naskah = trim($this->input->get('naskah'));
                $kondisi .= $naskah != "" ? " and s.r_judul ilike '%{$naskah}%' " : "";

                $kondisi .= " and date(k.kohirtgl) between '{$tglawal}' and '{$tglakhir}' ";
                $kondisi_bayar = " and date(ss.sspdtgl) between '{$tglawalbayar}' and '{$tglakhirbayar}' ";

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                    'kondisi_bayar' => $kondisi_bayar,
                );
                // print_r($kondisi_bayar);exit;
                break;
            case 'tap_kendali_reklame_blm_bayar':
                $insidentil = intval($this->input->get('insidentil'));
                $type_id = intval($this->input->get('type_id'));
                $kondisi = '';
                if ($insidentil > -1)
                    $kondisi .= ' and r.insidentil='.$insidentil;
                if ($type_id > 0)
                    $kondisi .= ' and s.type_id='.$type_id;
                $naskah = trim($this->input->get('naskah'));
                $kondisi .= $naskah != "" ? " and s.r_judul ilike '%{$naskah}%' " : "";

                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali_reklame_jthtempo_tgl':
                $naskah = trim($this->input->get('naskah'));
                $kondisi = $naskah != "" ? " and s.r_judul ilike '%{$naskah}%' " : "";
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali_reklame_jthtempo':
                $naskah = trim($this->input->get('naskah'));
                $kondisi = $naskah != "" ? " and s.r_judul ilike '%{$naskah}%' " : "";
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'kondisi' => $kondisi,
                );
                break;

            case 'tap_kendali_reklame_jthtempo_rek':
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                    'rekeningid' => (int) $this->input->get('rekeningid'),
                );
                break;

            case 'tap_kendali_airtanah':
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'bulan' => (int) $this->input->get('bulan'),
                    'tahun' => (int) $this->input->get('tahun'),
                );
                break;

            case 'tap_kendali_airtanah_skpd':
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                );
                break;

            case 'tap_kendali_airtanah_tgl':
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                );
                break;

            case 'tap_kendali_airtanah_masa':
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
                );
                break;

            case 'tap_catat_vol_airtanah':
                $params = array(
                    'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
                    'tahun' => (int) $this->input->get('tahun'),
                );
                break;
        }
        $tambahan = array(
            "daerah" => pad_pemda_daerah(),
            //"dinas" => pad_pemda_kode(),
        );
        $params = array_merge($params, $tambahan);
        $ignore_html_pg = false; //paging aja semua

        $rpt = 'penetapan/'.$rpt;
        // var_dump($params);
        // die;

        $jasper = $this->load->library('Jasper');
        
        if($type=='pdf'){
        echo $jasper->cetak($rpt, $params, $type, $ignore_html_pg);
        }
                //IF HTML, convert ke excel
        else if($type=='html'){
        $assetpath = 'assets/file';
        $tmp = $assetpath.'/tmp/report'.sipkd_user_id().'.html';

        if (is_file($tmp))
        {
            unlink($tmp);
        }

        ob_start();
        echo $jasper->cetak($rpt, $params, $type, $ignore_html_pg);
        echo '1';
        file_put_contents($tmp, ob_get_contents());

        $objPHPExcel = new PHPExcel();
        $inputFileType = 'HTML';
        $inputFileName =  $tmp;
        $outputFileType = 'Excel2007';
        $outputFileName = $assetpath.'/Report.xls';
        $filename = $rpt.date("d-m-Y").".xls";

        $objPHPExcelReader = IOFactory::createReader($inputFileType);
        $objPHPExcel = $objPHPExcelReader->load($inputFileName);
    
        ini_set('zlib.output_compression','Off'); 
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        //the folowing two lines make sure it is saved as a xls file
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename='.$filename);
                        //simpan dalam file sample.xls
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');                
        $objWriter->save('php://output');
        }


        // echo $jasper->query_debug($rpt, $params);
    }
}
