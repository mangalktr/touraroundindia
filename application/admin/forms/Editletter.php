<?php
class Admin_Form_Editletter extends Zend_Form
{
	public function __construct($params = null)
	{    

            
            /************ Page Id *****************/
            $id = $this->createElement('hidden','news_letter_id',
                                array(
                                     'value' => '',
                                     'class' => 'input-xlarge',
                                        'id' => 'news_letter_id',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************  Name  *****************/
            $news_letter_email = $this->createElement('text','news_letter_email',
                                array(
                                     'value' => trim($params['news_letter_email']),
                                     'class' => 'input-xlarge',
                                        'id' => 'name',
                                    'required'=> false,
                                   'filters' => array('StringTrim'),
                             ))	 
                            ->setErrorMessages(array('Please enter name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           

            /************ Status *****************/				

            $status_number = array();
            $status_number[""]="--Select Status--";
            $dataMenuarr_r = array('1'=>'Active','0'=>'Deactive');    
            $status = $this->createElement('select', 'status');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $status_number[$key] = $val;
                        }
            
            $status->setMultiOptions($status_number);
            $status->setRequired(false);            
            $status->setErrorMessages(array('Please Enter Status'));
            $status->removeDecorator('label');
            $status->removeDecorator('HtmlTag');
            $status->class = "input-xlarge";
            
            $this->addElements(array(
            $news_letter_email,   
            $status,
                
            )); 
	}
}
