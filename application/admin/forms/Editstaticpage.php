<?php
class Admin_Form_Editstaticpage extends Zend_Form
{
    
	public function __construct($params = null)
	{    


            /************ Page Id *****************/
            $sid = $this->createElement('hidden','sid',
                                array(
                                     'value' => trim($params['sid']),
                                     'class' => 'input-xlarge',
                                        'id' => 'sid',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),
                                    //'validators' => array(array('StringLength', false, array(3, 100))),
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
              
            /************ Static Title  *****************/
            $page_title = $this->createElement('text','page_title',
                                array(
                                     'value' => trim($params['page_title']),
                                     'class' => 'input-xlarge',
                                        'id' => 'page_title',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 //'validators' => array(array("Alpha", false, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter page title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           

            /************ Meta Title *****************/				
            $meta_title = $this->createElement('text','meta_title',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'meta_title',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter meta title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Meta Keywords *****************/				
            $meta_keywords = $this->createElement('text','meta_keywords',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'meta_keywords',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter meta keywords'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Meta Description *****************/				
            $meta_description = $this->createElement('textarea','meta_description',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'meta_description',
                                      'rows' => '5',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter meta description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Background Image *****************/				
            $background_image = $this->createElement('file','background_image',
                        array(
                              'required'     => false,
                              'MaxFileSize' => 2097152,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'background_image',
                                'validators' => array(
                                array('Count', false, 1),
                                array('Size', false, 2097152),
                                array('Extension', false, 'gif,jpg,png'),
//                                array('ImageSize', false, 
//                                          array('minwidth' => 1000,
//                                                'minheight' => 570,
//                                                'maxwidth' => 1000,
//                                                'maxheight' => 570)),
                              )
                            ))
                            ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Static Description *****************/				
            $page_description = $this->createElement('textarea','page_description',
                        array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'textarea-xlarge',
                                        'id' => 'page_description',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter static description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
             
            
            /************ Status *****************/
            $dataStatus = array();
            $dataStatus[""]="--Select Status--";
            $dataMenuarr = array('Activate'=>'Activate','Deactivate'=>'Deactivate');    
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

            
            $this->addElements(array(
                $sid,
                $page_title,
                $page_description,
                $background_image,
                $meta_title,
                $meta_keywords,
                $meta_description,
                $status    
            ));

        }
        
}
