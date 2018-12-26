<?php
class Admin_Form_Editblog extends Zend_Form
{
	public function __construct($params = null)
	{    

            /************ Page Id *****************/
            $BlogId = $this->createElement('hidden','BlogId',
                                array(
                                     'value' => '',
                                     'class' => 'input-xlarge',
                                        'id' => 'BlogId',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),
                                    //'validators' => array(array('StringLength', false, array(3, 100))),
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                                     
            
            /************ Page Name  *****************/
            $BlogTitle = $this->createElement('text','BlogTitle',
                                array(
                                     'value' => trim($params['BlogTitle']),
                                     'class' => 'input-xlarge',
                                        'id' => 'BlogTitle',
                                    'required'=>true,
                                    'autocomplete'=>'off',
                                   'filters' => array('StringTrim'),
                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter Title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
           
 
            
        
                        
 
      /************ Date *****************/				
            $BlogDate = $this->createElement('text','BlogDate',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'BlogDate',
                               'autocomplete'=>'off',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter date '))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');      
 
             /************ Uploaded By *****************/				
            $PostedBy = $this->createElement('text','PostedBy',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'PostedBy',
                               'autocomplete'=>'off',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter Uploader name '))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');    
        

      
   /************ Image *****************/				
            $BlogImage = $this->createElement('file','BlogImage',
                        array(
                              'required'     => false,
                              'MaxFileSize' => 2097152,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'BlogImage',
                                'validators' => array(
                                array('Count', false, 1),
                                array('Size', false, 2097152),
                                array('Extension', false, 'gif,jpg,png'),
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
   
          
            /************Description *****************/				
            $BlogDescription = $this->createElement('textarea','BlogDescription',
                        array(
                              'required'     => false,
                              'autocomplete' => 'off',
                                     'class' => 'textarea-xlarge',
                                        'id' => 'BlogDescription',
                               'autocomplete'=>'off',
//                                  'readonly' => 'readonly',
                                  'rows' => '10',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
             /************ Status *****************/
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
            $BlogId,   
            $BlogTitle,
            $BlogDate,
            $PostedBy,
            $BlogImage,
            $BlogDescription,
            $status,
           
            
            )); 
	}
}
