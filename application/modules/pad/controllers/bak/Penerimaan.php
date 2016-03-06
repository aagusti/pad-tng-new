<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class penerimaan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            echo "<script>window.location.replace('" . base_url() . "');</script>";
            exit;
        }

        $module = 'penerimaan';
        $this->load->library('module_auth', array(
            'module' => $module
        ));

        $this->load->model(array(
            'apps_model', 'penerimaan_model', 'sptpd_model'
        ));

		$this->load->helper(active_module());
    }

    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();

        $this->load->view('vpenerimaan', $data);
    }

    function grid()
    {
		/*
		select t.id, t.terimano, t.terimatgl, t.npwpd, c.nama, t.jmlterima as bayar, cu.usaha_id,
		(select sum(amount)
		   from pad_terima_line
		   where terima_id=t.id
		) as terima

		from pad_terima t
		inner join pad_customer c on t.customer_id=c.id
		inner join pad_customer_usaha cu on cu.customer_id=c.id

		where t.enabled=1
		and (extract(year from t.terimatgl)=2013) and (extract(month from t.terimatgl)=7)
		group by 1,2,3,4,5,6,7
		order by t.terimano desc
		 */
        $this->load->library('Datatables');
        $this->datatables->select('t.id, (t.tahun||\'.\'||t.terimano::text) as terimano, t.terimatgl, t.npwpd, c.nama,
			(select sum(amount)
			   from pad_terima_line
			   where terima_id=t.id
			) as terima, t.jmlterima as bayar');
        $this->datatables->from('pad_terima t');
        $this->datatables->join('pad_customer c', 'c.id = t.customer_id');
        $this->datatables->join('pad_customer_usaha cu', 'c.id = cu.customer_id');
        $this->datatables->where('t.enabled', 1);
		$this->datatables->where('t.tahun', pad_tahun_anggaran());

		if ($this->input->get('iSortCol_0') == 1) {
			$sort = $this->input->get('sSortDir_0');
			$this->datatables->order_by('t.terimano', $sort);
		}
		$this->datatables->group_by('t.id, t.terimano, t.terimatgl, t.npwpd, c.nama');


        $this->datatables->date_column('2');
        $this->datatables->rupiah_column('5,6');
        echo $this->datatables->generate();
    }

	function grid_pajak_terhutang() {
		/*
		select s.id, (s.tahun||'.'||s.sptno::text) as sptno, get_nopd(cu.id, true) as nopd, s.pajak_terhutang,
		(((extract(year from age(s.jatuhtempotgl)) * 12) + extract(month from age(s.jatuhtempotgl))) * s.pajak_terhutang * (select pad_bunga from pad_pemda where enabled=1 limit 1) + s.bunga) as bunga,
		(s.pajak_terhutang + ((extract(year from age(s.jatuhtempotgl)) * 12) + extract(month from age(s.jatuhtempotgl))) * s.pajak_terhutang * (select pad_bunga from pad_pemda where enabled=1 limit 1) + s.bunga) as total,
		(extract(month from age(s.jatuhtempotgl))) as bln_telat_bayar

		from pad_spt s
		inner join pad_customer_usaha cu on cu.id=s.customer_usaha_id

		where s.id not in (select spt_id from pad_terima_line)
		and s.customer_id=200022280000
		order by sptno
		*/

		$c_id = $this->uri->segment(4);
        $this->load->library('Datatables');
        $this->datatables->select('s.id, (s.tahun||\'.\'||s.sptno::text) as sptno, get_nopd(cu.id, true) as nopd, s.pajak_terhutang,
			(((extract(year from age(s.jatuhtempotgl)) * 12) + extract(month from age(s.jatuhtempotgl))) * s.pajak_terhutang * (select pad_bunga from pad_pemda where enabled=1 limit 1) + s.bunga) as bunga,
			(s.pajak_terhutang + ((extract(year from age(s.jatuhtempotgl)) * 12) + extract(month from age(s.jatuhtempotgl))) * s.pajak_terhutang * (select pad_bunga from pad_pemda where enabled=1 limit 1) + s.bunga) as total,
			(((extract(year from age(s.jatuhtempotgl)) * 12) + extract(month from age(s.jatuhtempotgl)))) as bln_telat_bayar', false);
        $this->datatables->from('pad_spt s');
        $this->datatables->join('pad_customer_usaha cu', 'cu.id = s.customer_usaha_id');

        $this->datatables->where('s.id not in (select spt_id from pad_terima_line)');
		$this->datatables->where('s.customer_id', $c_id);

        $this->datatables->rupiah_column('3,4,5');
        $this->datatables->unset_column('bln_telat_bayar');
        echo $this->datatables->generate();
	}

	function grid_terima_line() {
		/*
		select s.id, (s.tahun||'.'||s.sptno::text) as sptno, get_nopd(cu.id, true) as nopd, sum(tl.amount) as setoran
		from pad_terima_line tl
		inner join pad_spt s on s.id=tl.spt_id
		inner join pad_customer_usaha cu on cu.id=s.customer_usaha_id
		inner join pad_terima t on t.id=tl.terima_id

		where t.id=15
		group by 1,2,3
		*/

		$t_id = $this->uri->segment(4);
        $this->load->library('Datatables');
        $this->datatables->select('s.id, (s.tahun||\'.\'||s.sptno::text) as sptno, get_nopd(cu.id, true) as nopd,
			sum(tl.amount) as setoran', false);
        $this->datatables->from('pad_terima_line tl');
        $this->datatables->join('pad_spt s', 's.id=tl.spt_id');
        $this->datatables->join('pad_customer_usaha cu', 'cu.id = s.customer_usaha_id');
        $this->datatables->join('pad_terima t', 't.id =tl.terima_id');

        $this->datatables->where('t.id', $t_id);
		$this->datatables->group_by('s.id, sptno, nopd');

        $this->datatables->rupiah_column('3');
        echo $this->datatables->generate();
	}

    private function fvalidation($jenis_penerimaan = null)
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('tahun','Tahun','required|numeric');
		$this->form_validation->set_rules('terimatgl','Tanggal','required');
		$this->form_validation->set_rules('jmlterima','Jumlah Setoran','required');
		$this->form_validation->set_rules('npwpd','NPWPD','required|trim');
		$this->form_validation->set_rules('customer_id','Subjek Pajak','required');
		// $this->form_validation->set_rules('nobukti','nobukti','required|trim');
		// $this->form_validation->set_rules('notes','notes','required|trim');
    }

    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['tahun'] = $this->input->post('tahun');
		$data['terimano'] = $this->input->post('terimano');
		$data['terimatgl'] = $this->input->post('terimatgl');
		$data['jmlterima'] = pad_to_decimal($this->input->post('jmlterima'));
		$data['npwpd'] = $this->input->post('npwpd');
		$data['customer_id'] = $this->input->post('customer_id');
		$data['nobukti'] = $this->input->post('nobukti');
		$data['notes'] = $this->input->post('notes');
		$data['enabled'] = $this->input->post('enabled');

        return $data;
    }

    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('penerimaan'));
        }

        $post_data  = $this->fpost();

        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penerimaan/add');

        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $post_data;
            $terimano   = $this->penerimaan_model->max_terimano(pad_tahun_anggaran());
            $update_data = array(
				'tahun' => pad_tahun_anggaran(),
				'terimano' => $terimano,
				'terimatgl' => empty($input_post['terimatgl']) ? NULL : date('Y-m-d', strtotime($input_post['terimatgl'])),
				'jmlterima' => empty($input_post['jmlterima']) ? 0 : $input_post['jmlterima'],
				'npwpd' => empty($input_post['npwpd']) ? NULL : $input_post['npwpd'],
				'customer_id' => empty($input_post['customer_id']) ? NULL : $input_post['customer_id'],
				'nobukti' => empty($input_post['nobukti']) ? NULL : $input_post['nobukti'],
				'notes' => empty($input_post['notes']) ? NULL : $input_post['notes'],
				'enabled' => 1,

				'created' => date('Y-m-d'),
				'create_uid' => sipkd_user_id(),
            );
            $new_id = $this->penerimaan_model->save($update_data);

            $this->session->set_flashdata('msg_success', 'Data telah utama disimpan, silahkan lakukan posting penerimaan.');
            // redirect(active_module_url('penerimaan'));
            redirect(active_module_url("penerimaan/edit/{$new_id}"));
        }

        $data['dt']               = $post_data;
        $data['dt']['tahun']      = pad_tahun_anggaran();
        $data['dt']['terimatgl']  = date('d-m-Y');
        $data['dt']['jmlterima']  = 0;

        $this->load->view('vpenerimaan_form', $data);
    }

    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('penerimaan'));
        }

        $p_id = $this->uri->segment(4);

        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("penerimaan/update/{$p_id}");

        if ($p_id && $get = $this->penerimaan_model->get($p_id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['tahun'] = empty($get->tahun) ? NULL : $get->tahun;
			$data['dt']['terimano'] = empty($get->terimano) ? NULL : $get->terimano;
			$data['dt']['terimatgl'] = empty($get->terimatgl) ? NULL : date('d-m-Y', strtotime($get->terimatgl));
			$data['dt']['jmlterima'] = empty($get->jmlterima) ? 0 : $get->jmlterima;
			$data['dt']['npwpd'] = empty($get->npwpd) ? NULL : $get->npwpd;
			$data['dt']['customer_id'] = empty($get->customer_id) ? NULL : $get->customer_id;
			$data['dt']['nobukti'] = empty($get->nobukti) ? NULL : $get->nobukti;
			$data['dt']['notes'] = empty($get->notes) ? NULL : $get->notes;
			$data['dt']['enabled'] = empty($get->enabled) ? NULL : $get->enabled;

			$data['dt']['created'] = empty($get->created) ? NULL : date('d-m-Y', strtotime($get->created));
			$data['dt']['create_uid'] = empty($get->create_uid) ? NULL : $get->create_uid;
			$data['dt']['updated'] = empty($get->updated) ? NULL : date('d-m-Y', strtotime($get->updated));
			$data['dt']['update_uid'] = empty($get->update_uid) ? NULL : $get->update_uid;

			//echo json_encode($data);
			$this->load->view('vpenerimaan_form', $data);
        } else {
            show_404();
        }
    }

    public function update()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url('penerimaan'));
        }

        $p_id       = $this->uri->segment(4);
        $post_data = $this->fpost();

        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url("penerimaan/update/{$p_id}");

        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post  = $post_data;
            $update_data = array(
				// 'tahun' => pad_tahun_anggaran(),
				// 'terimano' => empty($input_post['terimano']) ? NULL : $input_post['terimano'],
				'terimatgl' => empty($input_post['terimatgl']) ? NULL : date('Y-m-d', strtotime($input_post['terimatgl'])),
				'jmlterima' => empty($input_post['jmlterima']) ? 0 : $input_post['jmlterima'],
				'npwpd' => empty($input_post['npwpd']) ? NULL : $input_post['npwpd'],
				'customer_id' => empty($input_post['customer_id']) ? NULL : $input_post['customer_id'],
				'nobukti' => empty($input_post['nobukti']) ? NULL : $input_post['nobukti'],
				'notes' => empty($input_post['notes']) ? NULL : $input_post['notes'],
				// 'enabled' => 1,

				'updated' => date('Y-m-d'),
				'update_uid' => sipkd_user_id(),
            );

            $this->penerimaan_model->update($p_id, $update_data);

            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('penerimaan'));
        }

        $data['dt'] = $post_data;
		$this->load->view('vpenerimaan_form', $data);
    }

    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('penerimaan'));
        }

        $id = $this->uri->segment(4);
        if ($id && $this->penerimaan_model->get($id)) {
            $this->penerimaan_model->delete($id);
			
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('penerimaan'));
        } else {
            show_404();
        }
    }


	//terima line
	public function post()
	{
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url('penerimaan'));
        }

        $spt_id = $this->uri->segment(4);
        $terima_id = $this->uri->segment(5);
		$sptpd  = $this->sptpd_model->get($spt_id);

        if ($terima_id && $spt_id && $sptpd) {
			// hitung bunga (telat bayar)
			$tgl_proses = new DateTime(Date('Y-m-d'));
			$tgl_jtempo = new DateTime(Date('Y-m-d', strtotime($sptpd->jatuhtempotgl)));

			$diff = $tgl_proses->diff($tgl_jtempo);
			$nbln = (($diff->format('%y') * 12) + $diff->format('%m'));
			$nbln = $tgl_proses <= $tgl_jtempo ? 0 : $nbln;
			$nbln = $nbln > 24 ? 24 : $nbln;

			$pajak = $sptpd->pajak_terhutang;
			$bunga = (float)$pajak * (int)$nbln * (float)pad_bunga() + (float)$sptpd->bunga;
			// end hitung bunga

            $terimano  = $this->penerimaan_model->max_terimano(pad_tahun_anggaran());
			$jmlterima = (float)$sptpd->pajak_terhutang + (float)$bunga;

			// terima line - rek pajak
            $update_data = array(
				'terima_id' => $terima_id,
				'spt_id' => $spt_id,

				'rekening_id' => $sptpd->rekening_id,
				'pajak_id' => $sptpd->pajak_id,

				'amount' => $sptpd->pajak_terhutang,

				'enabled' => 1,
				'created' => date('Y-m-d'),
				'create_uid' => sipkd_user_id(),
            );
            $this->penerimaan_model->save2($update_data);

			// terima line - rek denda (bunga)
			if ($bunga > 0) {
				$update_data = array(
					'terima_id' => $terima_id,
					'spt_id' => $spt_id,

					'rekening_id' => $this->load->model('pajak_model')->get_rek_denda($sptpd->pajak_id),
					'pajak_id' => $sptpd->pajak_id,

					'amount' => $bunga,

					'enabled' => 1,
					'created' => date('Y-m-d'),
					'create_uid' => sipkd_user_id(),
				);
				$this->penerimaan_model->save2($update_data);
			}
			
			// update setoran
			$terima_id = $this->penerimaan_model->get_terima_id_by_spt($spt_id);
            $new_setoran = $this->penerimaan_model->update_setoran($terima_id);
			echo $new_setoran;
		} else echo "hmm";
	}

	public function unpost()
	{
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url('penerimaan'));
        }

        $spt_id   = $this->uri->segment(4);
        if ($spt_id && $sptpd = $this->sptpd_model->get($spt_id)) {
			$terima_id = $this->penerimaan_model->get_terima_id_by_spt($spt_id);
			
            $this->penerimaan_model->delete2_by_spt($spt_id);
            $new_setoran = $this->penerimaan_model->update_setoran($terima_id);
			echo $new_setoran;
		} else echo "hmm";
	}
}
