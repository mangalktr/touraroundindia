<?php
class Admin_Form_Editquery extends Zend_Form
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
            
            
            /************ Full Name  *****************/
            $name = $this->createElement('text','location',
                                array(
                                     'value' => trim($params['location']),
                                     'class' => 'input-xlarge',
                                        'id' => 'name',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
                             ))	 
                            ->setErrorMessages(array('Please enter location'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ email id  *****************/
            $email = $this->createElement('text','email',
                                array(
                                     'value' => trim($params['email']),
                                     'class' => 'input-xlarge',
                                        'id' => 'email',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),                       
                             ))	 
                            ->setErrorMessages(array('Please enter email'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Second email id  *****************/
            $secondEmail = $this->createElement('text','secondEmail',
                                array(
                                     'value' => trim($params['secondEmail']),
                                     'class' => 'input-xlarge',
                                        'id' => 'secondEmail',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),                       
                             ))	 
                            ->setErrorMessages(array('Please enter email'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           

            /************ phone *****************/				
            $phone = $this->createElement('text','phone',
                           array(
                               'value' => trim($params['phone']),
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'phone',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter phone number'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
                        /************ mobile *****************/				
            $mobile = $this->createElement('text','mobile',
                           array(
                               'value' => trim($params['mobile']),
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'mobile',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter mobile number'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            $whatsappNo = $this->createElement('text','whatsapp_no',
                           array(
                               'value' => trim($params['whatsapp_no']),
//                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'whatsapp_no',
                                  
                            ))
                            ->setErrorMessages(array('Please enter whatsapp number'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Status *****************/
            $dataStatus = array();
            //$dataStatus[""]="--Select Status--";
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
            $id,   
            $name,
            $email,
            $secondEmail,
            $phone,
            $mobile,
            $whatsappNo,
            $status    
            )); 
	}
}
