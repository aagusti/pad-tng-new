<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cetak extends CI_Controller {
 
    public function __construct() {
        parent::__construct();
        if (!is_login()) {
        echo "<script>window.location.replace('" . base_url() . "');</script>";
        exit;
        }
        $this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
    }

    function index()
    {
        $objPHPExcel = new PHPExcel();
 
                // Set properties
        $objPHPExcel->getProperties()
                  ->setCreator("SMA Insan Cendekia Alkautsar") //creator
                    ->setTitle("Jadwal pelajaran");  //file title
 
        $objset = $objPHPExcel->setActiveSheetIndex(0); //inisiasi set object
        $objget = $objPHPExcel->getActiveSheet();  //inisiasi get object
 
        $objget->setTitle('Sample Sheet'); //sheet title
        $objset->setCellValue('A1',"This is Sample Excel File"); //insert cell value
        $objget->getStyle('A1')->getFont()->setBold(true)  // set font weight
                ->setSize(15);    //set font size
        
        //query
        $query = $this->db->query("select get_npwpd(c.id, true) as npwpd,c.nama as customernm,c.alamat, c.telphone,c.kodepos,
                    cd.reg_date as createdatecd, cd.notes as ket, kec.kode as kecamatankd,kec.nama as kecamatannm,
                    kel.kode as kelurahankd, kel.nama as kelurahannm, cd.customer_status_id, u.nama as usahanm,
                    cd.usaha_id, cd.konterid, cd.notes, kel2.kode as kel2kd, kel2.nama as kel2nm,
                    kec2.kode as kec2kd, kec2.nama as kec2nm,
                    c.kukuhtgl, c.kukuhno, cd.reg_date as reg_datecd
                    ,(select count(*) FROM pad_customer_usaha cd2
                         INNER JOIN pad_kelurahan k2 ON cd2.kelurahan_id=k2.id
                         INNER JOIN pad_kecamatan k3 ON k2.kecamatan_id=k3.id
                         WHERE cd2.customer_id=c.id and usaha_id=cd.usaha_id and k3.id=kec2.id) as jml
                    from pad_customer c
                    inner join pad_customer_usaha cd on c.id=cd.customer_id
                    inner join pad_kelurahan kel on c.kelurahan_id=kel.id
                    inner join pad_kecamatan kec on kel.kecamatan_id=kec.id
                    inner join pad_usaha u on cd.usaha_id=u.id
                    inner join pad_kelurahan kel2 on cd.kelurahan_id=kel2.id
                    inner join pad_kecamatan kec2 on kel2.kecamatan_id=kec2.id
                    order by c.npwpd");


        //table header
        $cols = array("A","B","C","D","E","F");
                $val = array("NPWPD","NAMA","ALAMAT","KETERANGAN","KECAMATAN","KELURAHAN");
        for ($a=0;$a<6;$a++) 
                {
                    $objset->setCellValue($cols[$a].'3', $val[$a]);
                        //set borders
            $objget->getStyle($cols[$a].'3')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objget->getStyle($cols[$a].'3')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objget->getStyle($cols[$a].'3')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objget->getStyle($cols[$a].'3')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
 
            //set alignment
            $objget->getStyle($cols[$a].'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //set font weight
            $objget->getStyle($cols[$a].'3')->getFont()->setBold(true) ;
        }
                
               //taruh baris data disini
        $colsb = array("A","B","C","D","E","F");
        $i = 4 ; // baris ke
        foreach ($query->result() as $row)
        {
            $valb = array($row->npwpd,
                          $row->customernm,
                          $row->alamat,
                          $row->ket,
                          $row->kecamatannm,
                          $row->kelurahannm,);
            for ($b=0;$b<6;$b++) 
            {
                $objset->setCellValue($colsb[$b].$i, $valb[$b]);
            }
            $i=$i+1;
        }
                //simpan dalam file sample.xls
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');                
                $objWriter->save('assets/file/sample2.xls');
    }
}