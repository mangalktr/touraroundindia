<?php
class Admin_Form_Editregion extends Zend_Form
{
	public function __construct($params = null)
	{    

            /************ Title  *****************/
            $title = $this->createElement('text','title',
                                array(
                                     'value' => trim($params['title']),
                                     'class' => 'input-xlarge',
                                        'id' => 'title',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
             
            /************ Blog Image *****************/				
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
                                array('Extension', false, 'gif,jpg,png'),
//                                array('ImageSize', false, 
//                                          array('minwidth' => 1600,
//                                                'minheight' => 420,
//                                                'maxwidth' => 2500,
//                                                'maxheight' => 480)),
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
            
            
            
            $this->addElements(array(
            $title,
            $image,
            $status,
            )); 
	}
}
