<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : Rvadmin.php
 * File Desc.    : Base Admin controller managed all activity
 * Created By    : Ranvir Singh <twitter @ranvir2012>
 * Created Date  : 21 Dec 2017
 * Updated Date  : 09 Jan 2018
 * ************************************************************* */


class Catabatic_Rvadmin extends Zend_Controller_Action {


    public $dbAdapter;
    public $perPageLimit;
    public $siteurl;
    public $DIR_WRITE_MODE;
    
    public $crud;
    public $img_w_dynamic;
    public $img_h_dynamic;


    public function init() {
        /* Initialize db and session access */
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $this->siteurl = $aConfig['bootstrap']['siteUrl'];
        $this->appmode = $aConfig['bootstrap']['appmode'];

        $this->per_page_record = 20;
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage()->read();
        $this->username = $authStorage->username;
        $this->admin_type = $authStorage->role;


        $this->current_time = time();

        $this->img_w_thumb = 85;
        $this->img_h_thumb = 62;

        $this->img_w_medium = 220;
        $this->img_h_medium = 180;
        
        $this->img_w_large = 470;
        $this->img_h_large = 341;
        
        $this->img_w_small = 230;
        $this->img_h_small = 152;
        
        $this->img_w_banner = 800;
        $this->img_h_banner = 300;

        $this->DIR_WRITE_MODE = 0777;
        $this->tablename = 'tb_tbb2c_packages_master';
        $this->crud = new Admin_Model_CRUD();
        
    }

    
    /*
     * Params ( PkgSysId , Source URL , destination , ['thumb','small','medium','large'] )
     */
    public function downloadImagesFromServer( $PkgSysId , $sourceURL , $destination , array $clonesArray = ['thumb'] ) {
        
        $defaultImage = $sourceURL;
        
        if (isset($defaultImage) && !empty($defaultImage) &&  empty($result['Image'])) {
            
            try {
                $ImgThumbnailContent = file_get_contents($defaultImage);
                $fileExt = $this->_helper->General->getFileExtension($defaultImage);
                $ImgThumbnail = end(explode('_', $defaultImage));
                $fileName = $PkgSysId . '_' . $ImgThumbnail;
                $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $destination . $PkgSysId . "/images/";

                if (!file_exists($orignalFolderName)) {
                    mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                }

                /*
                 * create directories here
                 */
                $directoryname = '';
                foreach ($clonesArray as $key => $value) {
                    $directoryname = $orignalFolderName . "/{$value}";

                    if (!file_exists($directoryname)) {
                        mkdir($directoryname, $this->DIR_WRITE_MODE, true);
                    }

                    file_put_contents( $orignalFolderName . $fileName , $ImgThumbnailContent ); // save image here
                    copy( $orignalFolderName . '/' . $fileName , $directoryname . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize4 = new Catabatic_Imageresize( $directoryname . '/' . $fileName );

                    $getDimensions = $this->getDimensions($value); // get dimensions of images
                    $this->img_w_dynamic = ($getDimensions['w']) ? $getDimensions['w'] : 100;
                    $this->img_h_dynamic = ($getDimensions['h']) ? $getDimensions['h'] : 100;

                    $objImageResize4->resizeImage( $this->img_w_dynamic , $this->img_h_dynamic , 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize4->saveImage( $directoryname . '/' . $fileName);
                }
                $status = TRUE;
                $message= 'Downloaded successfully.';
            }
            catch (Exception $ex) {
                $status = FALSE;
                $message = $ex->getMessage();
                $fileName = '';
            }

            return ['status' => $status, 'message' => $message , 'img'=> $fileName ];
        }
                
                
    }
                
                
    public function getDimensions( $param ) {
        
        switch ($param) {
            case 'thumb':
            $w = $this->img_w_thumb;
            $h = $this->img_h_thumb;
                break;

            case 'small':
            $w = $this->img_w_small;
            $h = $this->img_h_small;
                break;

            case 'medium':
            $w = $this->img_w_medium;
            $h = $this->img_h_medium;
                break;

            case 'large':
            $w = $this->img_w_large;
            $h = $this->img_h_large;
                break;

            default:
                break;
        }
        
        return ['w'=> $w , 'h'=> $h];
    }
    
    
     
}
