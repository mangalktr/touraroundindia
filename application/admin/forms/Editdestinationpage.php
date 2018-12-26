<?php
class Admin_Form_Editdestinationpage extends Zend_Form
{
	public function __construct($params = null)
	{    

            $crud = new Admin_Model_CRUD();
            $destinations = $crud->rv_select_all('tbl_regions',['title','sid'],['IsActive'=>1,'IsMarkForDel'=>0],['title'=>'ASC']);
//          echo "<pre>";print_r($test);die;
             

         /************ Traveller *****************/				
            $travel = array();
            $travel[""]="--Select Region--";
            $dataMenuarr = $travel;  
            $region_id = $this->createElement('select', 'region_id');
            foreach ($destinations as $key=>$val) {
                $dataMenuarr[$val['sid']] = $val['title'];
            }
//            $region_id->setRequired(true);
            $region_id->setMultiOptions($dataMenuarr);
            $region_id->setErrorMessages(array('Please select region/state'));
            $region_id->removeDecorator('label');
            $region_id->removeDecorator('HtmlTag');
            $region_id->class = "input-xlarge";
            
           
            /************ Title  *****************/
            $title = $this->createElement('text','title',
                                array(
                                     'value' => trim($params['title']),
                                     'class' => 'input-xlarge',
                                        'id' => 'title',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                                  'readonly' => 'readonly',
                             ))	 
                            ->setErrorMessages(array('Please enter title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Activities  *****************/
//            $activities = $this->createElement('text','activities',
//                                array(
//                                     'value' => trim($params['activities']),
//                                     'class' => 'input-xlarge',
//                                        'id' => 'activities',
//                                    'required'=>true,
//                                    'filters' => array('StringTrim'),
//                                 
//                             ))	 
//                            ->setErrorMessages(array('Please enter page activities'))
//                            ->removeDecorator('label')
//                            ->removeDecorator('HtmlTag')
//                            ->removeDecorator('DtDdWrapper');
            
            /************ Tours  *****************/
            $tours = $this->createElement('text','tours',
                                array(
                                     'value' => trim($params['tours']),
                                     'class' => 'input-xlarge',
                                        'id' => 'tours',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 
                             ))	 
                            ->setErrorMessages(array('Please enter tours'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Hotels  *****************/
//            $hotel = $this->createElement('text','hotel',
//                                array(
//                                     'value' => trim($params['hotel']),
//                                     'class' => 'input-xlarge',
//                                        'id' => 'hotel',
//                                    'required'=>true,
//                                    'filters' => array('StringTrim'),
//                                 
//                             ))	 
//                            ->setErrorMessages(array('Please enter hotels'))
//                            ->removeDecorator('label')
//                            ->removeDecorator('HtmlTag')
//                            ->removeDecorator('DtDdWrapper');
            
            /************ Countries  *****************/
            $countries = $this->createElement('text','countries',
                                array(
                                     'value' => trim($params['countries']),
                                     'class' => 'input-xlarge',
                                        'id' => 'countries',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 
                             ))	 
                            ->setErrorMessages(array('Please enter countries'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');

            /************ Image *****************/				
            $image = $this->createElement('file','image',
                        array(
                              'required'     => false,
                              'MaxFileSize' => 2097152,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'image',
                                'validators' => array(
                                array('Count', false, 1),
                                array('Size', false, 2097152),
                                array('Extension', false, 'gif,jpg,png,jpeg'),
//                                array('ImageSize', false, 
//                                         array('minwidth' => 175,
//                                                'minheight' => 175,
//                                                'maxwidth' => 360,
//                                                'maxheight' => 360)),
                              )
                            ))
                            ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');

            /************ Banner Image *****************/				
            $banner_image = $this->createElement('file','banner_image',
                        array(
                              'required'     => false,
                              'MaxFileSize' => 20971520000000000,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'banner_image',
                                'validators' => array(
                                array('Count', false, 1),
                                array('Size', false, 20971520000000000),
                                array('Extension', false, 'gif,jpg,png,jpeg'),
//                                array('ImageSize', false, 
//                                          array('minwidth' => 1200,
//                                                'minheight' => 400,
//                                                'maxwidth' => 2500,
//                                                'maxheight' => 1000)),
                              )
                            ))
                            ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Status  *****************/
            $dataStatus = array();
            $dataStatus[""]="--Select Status--";
            $dataMenuarr = array('1'=>'Activate','0'=>'Deactivate');    
            $status = $this->createElement('select', 'status');
                        foreach ($dataMenuarr as $key=>$val) {
                            $dataStatus[$key] = $val;
                        }
            $status->setRequired(true);
            $status->setMultiOptions($dataStatus);
            $status->setErrorMessages(array('Please select status'));
            $status->removeDecorator('label');
            $status->removeDecorator('HtmlTag');
            $status->class = "input-xlarge";
            
            /************ Featured  *****************/
            $dataFeatured = array();
//            $dataFeatured[""]="--Select--";
            $dataMenuarr = array('1'=>'Featured','0'=>'UnFeatured');    
            $feature = $this->createElement('select', 'feature');
                        foreach ($dataMenuarr as $key=>$val) {
                            $dataFeatured[$key] = $val;
                        }
            $feature->setRequired(true);
            $feature->setMultiOptions($dataFeatured);
            $feature->setErrorMessages(array('Please select feature'));
            $feature->removeDecorator('label');
            $feature->removeDecorator('HtmlTag');
            $feature->class = "input-xlarge";

            
            $this->addElements(array(
            $region_id,
            $title,
//            $activities,
            $tours,
//            $hotel,
            $image,
            $banner_image,
            $countries,
            $status,
            $feature    
                
            )); 
	}
}
