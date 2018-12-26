<?php
 /**
 * @name		Readexcel
 * @version		1.0
 * @author		Ranvir Singh
 * @Created		10 Nov 2016
 * @Updated		08 Mar 2017
 * @copyright           Catabatic Technology
 * Handle Email functionality
 */

//class Zend_Controller_Action_Helper_CreateExcel extends Zend_Controller_Action_Helper_Abstract

class PHPExcel_Classes_Readexcel extends Zend_Controller_Action_Helper_Abstract
{
    public $filename;
    public $folder_path;
    
    
    public function importFromExcel($file)
    {
        require_once 'PHPExcel.php';
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $Reader->setReadDataOnly(true); // set this, to not read all excel properties, just data

        $objXLS = $Reader->load($file);
//        $value  = $objXLS->getSheet(0)->getCell('A2')->getValue();
//        $value  = $objXLS->getSheet(0)->getCell('A1')->getCalculatedValue();
        
        //  Get worksheet dimensions
        $sheet          = $objXLS->getSheet(0);
        $highestRow     = $sheet->getHighestRow();
        $highestColumn  = $sheet->getHighestColumn();
        
        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $finalData[] = $rowData[0];
        }

        return $finalData; // return final data array
    }

    
}
