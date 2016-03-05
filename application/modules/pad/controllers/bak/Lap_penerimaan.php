<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class lap_penerimaan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }

        $module = 'lap_penerimaan';
        $this->load->library('module_auth', array(
            'module' => $module
        ));

        $this->load->model(array(
            'apps_model'
        ));

		$this->load->helper(active_module());
    }

    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

		$select_data = $this->load->model('usaha_model')->get_select();
		$options     = array();
		if($select_data)
		foreach ($select_data as $row) {
			$options[$row->id] = $row->nama;
		}
		$js = 'id="usahaid" class="input-xlarge" required ';
		$data['select_usaha'] = form_dropdown('usahaid', $options, null, $js);
		
        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();

        $this->load->view('vlap_penerimaan', $data);
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
		$ignore_html_pg = TRUE;
		switch ($rpt) {
			case 'trm_tgl':
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'kondisi' => '',
				);
				break;
			case 'trm_tgl_jenis':
                $usaha_id = $this->input->get('usaha_id');
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'usaha_id' => (int)$usaha_id,
				);
				break;
			case 'trm_rekap':
                $usaha_id = $this->input->get('usaha_id');
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'usaha_id' => (int)$usaha_id,
				);
				break;
			case 'trm_piutang':
                $usaha_id = $this->input->get('usaha_id');
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'usaha_id' => (int)$usaha_id,
				);
				break;
			case 'trm_masa':
			case 'trm_masa_jenis':
			case 'trm_tgl_reklame':
			case 'trm_tgl_air':
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
					'kondisi' => $kondisi,
				);
				break;

			case 'trm_blm_dialokasikan':
			case 'trm_sdh_dialokasikan':
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tglawal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
					'tglakhir' => date('Y-m-d', strtotime($this->input->get('tglakhir'))),
				);
				break;
			case 'lra_harian':
                $kondisi =  $this->input->get('kondisi');
                $type_rpt = $kondisi;
                $kondisi = $kondisi=='perobjek' ? ' and r.levelid=3 ' : '';
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'tanggal' => date('Y-m-d', strtotime($this->input->get('tglawal'))),
                    'kondisi' => $kondisi,
                    'type_rpt' => $type_rpt,
				);
				break;
			case 'lra_mingguan':
                $kondisi =  $this->input->get('kondisi');
                $type_rpt = $kondisi;
                $kondisi = $kondisi=='perobjek' ? ' and r.levelid=3 ' : '';
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'minggu' => $this->input->get('minggu'),
                    'kondisi' => $kondisi,
                    'type_rpt' => $type_rpt,
				);
				break;
			case 'lra_bulanan':
                $kondisi =  $this->input->get('kondisi');
                $type_rpt = $kondisi;
                $kondisi = $kondisi=='perobjek' ? ' and r.levelid=3 ' : '';
				$params = array(
					'tglcetak' => date('Y-m-d', strtotime($this->input->get('tglcetak'))),
					'bulan' => $this->input->get('bulan'),
                    'kondisi' => $kondisi,
                    'type_rpt' => $type_rpt,
				);
				break;
		}
        $tambahan = array(
			"tahun" => pad_tahun_anggaran(),
            "daerah" => pad_pemda_daerah(),
            "dinas" => pad_pemda_nama(),
        );
        $params = array_merge($params, $tambahan);
        $ignore_html_pg = FALSE; //paging aja semua

		$rpt = 'penerimaan/'.$rpt;
		// var_dump($params);

		$jasper = $this->load->library('Jasper');
		echo $jasper->cetak($rpt, $params, $type, $ignore_html_pg);
	}
}
