<?php
class Admin_Form_Addactivities extends Zend_Form
{
	public function __construct($params = null)
	{    

            
            /************ Page Id *****************/
            $id = $this->createElement('hidden','id',
                                array(
                                     'value' => '',
                                     'class' => 'input-xlarge',
                                        'id' => 'id',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),
                                    
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************  Title  *****************/
              $Destinations = $this->createElement('text','Destinations',
                                array(
                                     'value' => trim($params['Destinations']),
                                     'class' => 'input-xlarge',
                                        'id' => 'Destinations',
//                                    'required'=>true,
//                                   'filters' => array('StringTrim'),
//                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter Destinations'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
           
            /************ Image *****************/				
            $image = $this->createElement('file','image[]',
                        array(
                              'required'     => false,
                              'MaxFileSize' => 2097152,
                              'autocomplete' => 'off',
                            
                            'multiple' => 'multiple',
                                     'class' => 'input-xlarge',
                                        'id' => 'image',
                                'validators' => array(
//                                array('Count', false, 1),
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

           
            
            $this->addElements(array(
            $id,   
            $Destinations,
           
            $image,
           
            )); 
	}
}
