<?php
class Admin_Form_Editexplore extends Zend_Form
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
                                    //'validators' => array(array('StringLength', false, array(3, 100))),
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************  Name  *****************/
            $name = $this->createElement('text','title',
                                array(
                                     'value' => trim($params['title']),
                                     'class' => 'input-xlarge',
                                        'id' => 'title',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
//                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Link  *****************/
            $url= $this->createElement('text','url',
                                array(
                                     'value' => trim($params['url']),
                                     'class' => 'input-xlarge',
                                        'id' => 'url',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 //'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter url'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           
            $status_number = array();
            $status_number[""]="--Select --";
            $dataMenuarr_r = array('1'=>'Active','0'=>'Deactive');    
            $status = $this->createElement('select', 'status_number');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $status_number[$key] = $val;
                        }
            
            $status->setMultiOptions($status_number);
            $status->setRequired(true);            
            $status->setErrorMessages(array('Please Enter status'));
            $status->removeDecorator('label');
            $status->removeDecorator('HtmlTag');
            $status->class = "input-xlarge";
            
            /************ Status *****************/				

            $open_link = array();
            $open_link[""]="--Select --";
            $dataMenuarr_r = array('1'=>'New Tab','0'=>'Same tab');    
            $open = $this->createElement('select', 'open_link');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $open_link[$key] = $val;
                        }
            
            $open->setMultiOptions($open_link);
            $open->setRequired(true);            
            $open->setErrorMessages(array('Please Enter open type'));
            $open->removeDecorator('label');
            $open->removeDecorator('HtmlTag');
            $open->class = "input-xlarge";
            
            $this->addElements(array(
            $id,   
            $name,
            $url,
            $status,
            $open,
                
            )); 
	}
}
