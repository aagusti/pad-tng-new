<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Convert extends CI_Controller {
 
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
		$inputFileType = 'HTML';
		$inputFileName = 'assets/file/sample4.html';
		$outputFileType = 'Excel2007';
		$outputFileName = 'assets/file/sample4.xls';
		$filename = "DownloadReport.xls";

		$objPHPExcelReader = IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);
		
		$objget = $objPHPExcel->getActiveSheet();
		$objget->getColumnDimension('A')->setWidth(5);
		$objget->getColumnDimension('B')->setWidth(5);
		$objget->getColumnDimension('C')->setWidth(5);
		$objget->getColumnDimension('D')->setWidth(20);
		$objget->getColumnDimension('E')->setWidth(50);
		$objget->getColumnDimension('F')->setWidth(50);
		$objget->getColumnDimension('G')->setWidth(20);
	
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
}