<?php
 /**
 * @name		Createexcel
 * @version		1.0
 * @author		Ranvir Singh
 * @Created		30 Aug 2016
 * @Updated		28 Oct 2016
 * @copyright           Catabatic Technology
 * Handle Email functionality
 */

//class Zend_Controller_Action_Helper_CreateExcel extends Zend_Controller_Action_Helper_Abstract

class PHPExcel_Classes_Createexcel extends Zend_Controller_Action_Helper_Abstract
{
    public $filename;
    public $folder_path;
    
    public function exportToExcel($WorkSheetTitle='', $sheetTitle, $arrFieldLabel, $arrFieldValue){
       
        if(count($arrFieldLabel)!=count($arrFieldValue[0])) {
//            throw new Exception ("Column Count mismatch");
        }
        
        require_once 'PHPExcel.php';
        $intColCount = count($arrFieldLabel);
        $intStartCol = chr(65);

        # if column count is greater than 26 
        if($intColCount<=26)
          $intEndCol = chr(65+$intColCount-1);
        else
          $intEndCol = $intStartCol.chr(65+($intColCount-26)-1);

//echo $intEndCol;        die;
        $objPHPExcel = new PHPExcel();
        // Set document properties
//        $objPHPExcel->getProperties()->setCreator("Ranvir Singh")
//                                     ->setLastModifiedBy("Ranvir Singh")
//                                     ->setTitle("Ranvir Singh")
//                                     ->setSubject("Ranvir Singh")
//                                     ->setDescription("Ranvir Singh")
//                                     ->setKeywords("Ranvir Singh")
//                                     ->setCategory("Ranvir Singh");
//        $objPHPExcel->getActiveSheet()
//                                    ->getStyle($intStartCol.'1:'.$intEndCol.'1')
//                                    ->getFill()
//                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
//                                    ->getStartColor()
//                                    ->setARGB('A6ADA8');
//        
//        
//        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
//        $objPHPExcel->getActiveSheet()->setCellValue('C1', $styleSheetTitle);
    
        $rowCount = 1; // row to print the column names
        /*********Setting Label value*************/
        $col = 0;
// echo       count($arrFieldLabel);die;
        foreach($arrFieldLabel as $label){
            $objPHPExcel->getActiveSheet()->getColumnDimension($intStartCol)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($intStartCol.$rowCount.':'.$intEndCol.$rowCount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,$rowCount, $label);
            $col++;
            $intStartCol++;
        }
        
        /*********Setting rowwise coloumn value*************/
        $rowCount = 2; // row to print the data
        while(list($key, $value) = each($arrFieldValue)){
            $col = 0;
            foreach($value as $field) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount, $field);
                $col++;
            }
            $rowCount++;
        }
      
        // Rename worksheet
        $WorkSheetTitle = ($WorkSheetTitle) ? $WorkSheetTitle : 'Report';
        $objPHPExcel->getActiveSheet()->setTitle($WorkSheetTitle);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Save Excel 2007 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace('.php', '.xls', __FILE__));
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$sheetTitle.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
//        $this->SaveViaTempFile($objWriter);
    }
    
    
    # for saving file to attach while sending daily policy
    public function saveExcel($WorkSheetTitle='', $sheetTitle, $arrFieldLabel, $arrFieldValue){

        if(count($arrFieldLabel)!=count($arrFieldValue[0])) {
//            throw new Exception ("Column Count mismatch");
        }
        
        require_once 'PHPExcel.php';
        $intColCount = count($arrFieldLabel);
        $intStartCol = chr(65);
        $intEndCol = chr(65+$intColCount-1);
        
        # if column count is greater than 26 
        if($intColCount<=26)
          $intEndCol = chr(65+$intColCount-1);
        else
          $intEndCol = $intStartCol.chr(65+($intColCount-26)-1);

        $objPHPExcel = new PHPExcel();

        $rowCount = 1; // row to print the column names
        /*********Setting Label value*************/
        $col = 0;
        foreach($arrFieldLabel as $label){
            $objPHPExcel->getActiveSheet()->getColumnDimension($intStartCol)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($intStartCol.$rowCount.':'.$intEndCol.$rowCount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,$rowCount, $label);
            $col++;
            $intStartCol++;
        }
        
        /*********Setting rowwise coloumn value*************/
        $rowCount = 2; // row to print the data
        while(list($key, $value) = each($arrFieldValue)){
            $col = 0;
            foreach($value as $field) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount, $field);
                $col++;
            }
            $rowCount++;
        }
      
        // Rename worksheet
        $WorkSheetTitle = ($WorkSheetTitle) ? $WorkSheetTitle : 'Report';
        $objPHPExcel->getActiveSheet()->setTitle($WorkSheetTitle);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Save Excel 2007 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save( $this->folder_path. $this->filename );
    }
    
    static function SaveViaTempFile($objWriter){
        die('here');
        $filePath = '' . rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
        $objWriter->save($filePath);
        readfile($filePath);
        unlink($filePath);
    }
    
}
