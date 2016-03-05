
<?php
class Cetak extends CI_Controller {
 
    public function __construct() {
                parent::__construct();
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
 
        //table header
        $cols = array("A","B","C","D","E","F");
                $val = array("No","Member ID","Member Username","Member Address","Member Phone","Member Status");
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
 
                //simpan dalam file sample.xls
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');                
                $objWriter->save('assets/file/sample.xls');
    }
}