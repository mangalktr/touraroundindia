<?php

class Zend_Controller_Action_Helper_Custom extends Zend_Controller_Action_Helper_Abstract {

    public function __construct() {
        $this->getTravelPlanStatusName = API_CUSTOMER_STATUS_PLAN_TYPE_LIST;
        $this->objMdl = new Admin_Model_CRUD();
    }

    /* Added by Piyush Tiwari on date 27-6-18 start */

    public function getContactDetailForFooter() {
        $this->objMdl = new Admin_Model_CRUD();
        $data = $this->objMdl->selectOne("tbl_query", ['*'], ['status' => 1]);
        return $data;
    }

    public function staticPageLink() {
        $this->objMdl = new Admin_Model_CRUD();
        $staticPage = $this->objMdl->rv_select_all('tbl_static_pages', ['identifier', 'page_title'], ['status' => 'Activate'], ['sid' => 'ASC']);
        return $staticPage;
    }

//    public function selectDestinationForFooterLink() {
//        $this->objMdl = new Admin_Model_CRUD();
//        $staticPage = $this->objMdl->rv_select_all("tb_tbb2c_destinations", ['Title','Image','DesSysId'], ['IsActive' => 1, 'IsPublish' => 1, 'IsMarkForDel' => 0, 'DisplayOnFooter' => 1], ['Tours' => 'DESC'], 6);
//        return $staticPage;
//    }
    public function selectDestinationForFooterLink() {
        $this->objMdl = new Admin_Model_CRUD();
        $staticPage = $this->objMdl->rv_select_all("tbl_regions", ['label','title'], ['IsActive' => 1], ['label' => 'ASC'], 6);
        return $staticPage;
        
//        $destinations = $this->objMdl->getDestinationsForFooter(['tbl.IsActive' => 1, 'tbl.IsPublish' => 1, 'tbl.IsMarkForDel' => 0, 'tbl.DisplayOnFooter' => 1, 'tb2.IsMarkForDel' => 0], ['tbl.DesSysId' => 'ASC'], 6);
//        $region_names = $finalDestination = [];
//        foreach ($destinations as $key => $value) {
//
//            if (($value['region_name'] != NULL) && !in_array($value['region_name'], $region_names)) {
//                $region_names[] = $value['region_name'];
//            }
//
//            $finalDestination[$value['region_name']][] = [
//                'DesSysId' => $value['DesSysId'],
//                'Title' => $value['Title'],
//                'Image' => $value['Image'],
//            ];
//        }
//        
//        $array = [
//           'region_name' => $region_names,
//           'destinations' => $finalDestination,
//        ];
//        
//        return $array;
    }

    public function selectPackTypeForFooterLink() {
        $this->objMdl = new Admin_Model_CRUD();
        $staticPage = $this->objMdl->rv_select_all("tbl_pack_type", ['Title'], ['IsActive' => 1, 'IsMarkForDel' => 0, 'DisplayOnFooter' => 1], ['packType' => 'DESC'], 10);
        return $staticPage;
    }

    public function selectSocialLinksForFooterLink() {
        $this->objMdl = new Admin_Model_CRUD();
        $staticPage = $this->objMdl->rv_select_all("tbl_social_links", ['name', 'link'], ['status' => 1], ['id' => 'DESC'], 10);
        return $staticPage;
    }

    public function destinationForHeader() {
        $destinations = $this->objMdl->getDestinationsHeader(['tbl.IsActive' => 1, 'tbl.IsPublish' => 1, 'tbl.IsMarkForDel' => 0, 'tbl.DisplayOnHeader' => 1, 'tb2.IsMarkForDel' => 0], ['tbl.DesSysId' => 'ASC'], 50);
        $region_image = $region_label = $region_names = $finalDestination = [];

        foreach ($destinations as $key => $value) {

            if (($value['region_label'] != NULL) && !in_array($value['region_label'], $region_label)) {
                $region_names[] = $value['region_name'];
                $region_label[] = $value['region_label'];
                $region_image[] = $value['region_image'];
            }
            

            $finalDestination[$value['region_label']][] = [
                'DesSysId' => $value['DesSysId'],
                'Title' => $value['Title'],
                'Image' => $value['Image'],
            ];
        }
        
        $array = [
           'region_name' => $region_names,
           'region_label' => $region_label,
           'region_image' => $region_image,
           'destinations' => $finalDestination,
        ];
        
        return $array;
    }
    
     public function getrecentpost() {
        $this->objMdl = new Admin_Model_CRUD();       
        $resullatest  = $this->objMdl->rv_select_all('tbl_travelogues', ['*'],  ['IsMarkForDel'=>0], ['TravId'=>'DESC'],'3');
        return $resullatest;
    }
    
     public function getaboutus() {
        $this->objMdl = new Admin_Model_CRUD();       
        
        $getAboutUsDetailForContactUs = $this->objMdl->rv_select_row('tbl_static_pages' , ['page_description'], ['identifier'=>'about-us' , 'status'=> 'Activate'] , ['sid'=> 'desc'] );
        $string = strip_tags($getAboutUsDetailForContactUs['page_description']);
        
        if (strlen($string) > 300) {

            // truncate string
            $stringCut = substr($string, 0, 200);
            $endPoint = strrpos($stringCut, ' ');

            //if the string doesn't contain any space then it will cut without word basis.
            $string = $endPoint? substr($stringCut, 0, $endPoint):substr($stringCut, 0);
        }        
      //  echo $string; die;
        return $string;
    }
    
     

    /* Added by Piyush Tiwari on date 27-6-18 end */
    public function exportToExcel($sheetTitle, $arrFieldLabel, $arrFieldValue) {

        /*if (count($arrFieldLabel) != count($arrFieldValue[0])) {

            throw new Exception("Column Count mismatch");
        }*/

        require_once __DIR__ . '/../PHPExcel/Classes/PHPExcel.php';
    
        $intColCount = count($arrFieldLabel);

        $intStartCol = chr(65);

        $intEndCol = chr(65 + $intColCount - 1);


        //$objPHPExcel   = new PHPExcel_Classes_PHPExcel;
        $objPHPExcel = new PHPExcel();

        // Set document properties

        $objPHPExcel->getProperties()->setCreator("Pardeep Panchal")
                ->setLastModifiedBy("Pardeep Panchal")
                ->setTitle("Pardeep Panchal")
                ->setSubject("Pardeep Panchal")
                ->setDescription("Pardeep Panchal")
                ->setKeywords("Pardeep Panchal")
                ->setCategory("Pardeep Panchal");

        $objPHPExcel->getActiveSheet()
                ->getStyle($intStartCol . '1:' . $intEndCol . '1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('A6ADA8');





        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValue('C1', $sheetTitle);



        $rowCount = 2;

        /*         * *******Setting Label value************ */

        $col = 0;

        foreach ($arrFieldLabel as $label) {

            $objPHPExcel->getActiveSheet()->getColumnDimension($intStartCol)->setAutoSize(true);

            $objPHPExcel->getActiveSheet()->getStyle($intStartCol . $rowCount . ':' . $intEndCol . $rowCount)->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount, $label);

            $col++;

            $intStartCol++;
        }



        /*         * *******Setting rowwise coloumn value************ */

        $rowCount = 3;

        while (list($key, $value) = each($arrFieldValue)) {

            $col = 0;
            //echo "<pre>";print_r($value);//exit;
            foreach ($value as $field) {

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount, $field);

                $col++;
                
            }
            $rowCount++;
            
        }



        // Rename worksheet

        $objPHPExcel->getActiveSheet()->setTitle($sheetTitle);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client�s web browser (Excel2007)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $sheetTitle . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
    exit;
        
    }
}

?>