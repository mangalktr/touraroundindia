<?php
class Admin_Form_Addsocial extends Zend_Form
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
            $name = $this->createElement('text','name',
                                array(
                                     'value' => trim($params['name']),
                                     'class' => 'input-xlarge',
                                        'id' => 'name',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Link  *****************/
            $link = $this->createElement('text','link',
                                array(
                                     'value' => trim($params['link']),
                                     'class' => 'input-xlarge',
                                        'id' => 'link',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 //'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter link'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           

            /************ Status *****************/				

            $status_number = array();
            $status_number[""]="--Select Status--";
            $dataMenuarr_r = array('1'=>'Active','0'=>'Deactive');    
            $status = $this->createElement('select', 'status_number');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $status_number[$key] = $val;
                        }
            
            $status->setMultiOptions($status_number);
            $status->setRequired(true);            
            $status->setErrorMessages(array('Please Enter Status'));
            $status->removeDecorator('label');
            $status->removeDecorator('HtmlTag');
            $status->class = "input-xlarge";
            
            $this->addElements(array(
            $id,   
            $name,
            $link,
            $status,
                
            )); 
	}
}
