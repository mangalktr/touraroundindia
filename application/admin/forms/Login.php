<?php
class Admin_Form_Login extends Zend_Form
{
	public function __construct($params = null)
	{    
            /************ username  *****************/
            $username = $this->createElement('text','username',
                                array('value' => trim($params['username']),
                                     'class' => 'span12',
                                        'id' => 'username'
                             ))

                            
                            ->setRequired(true)
                            ->setErrorMessages(array('Enter Username'))

//                            ->setValidators(array(array("Alnum", true, array("allowWhiteSpace" => false))))	 
                            ->setRequired(true)
//                            ->setErrorMessages(array('Please enter username and white space not allowed'))

                            ->addDecorators(
                                            array(
                                            'ViewHelper',
                                            'Errors',
                                            array('HtmlTag', array('tag' => 'div')),
                                            array('Label', array('tag' => '')),
                                    ));

            /************ Password *****************/				
            $password = $this->createElement('password','password',
                                            array('autocomplete' => 'off',
                                                     'class' => 'span12',
                                                        'id' => 'password'	
                                            ))

                            ->setRequired(true)

                           ->setErrorMessages(array('Enter Password'))

//                            ->setValidators(array(array("Alnum", true, array("allowWhiteSpace" => false))))	
//                            ->setErrorMessages(array('Please enter password and white space not allowed'))

                            ->addDecorators(array(
                            'ViewHelper',
                            'Errors',
                            array('HtmlTag', array('tag' => 'div')),
                            array('Label', array('tag' => '')),
                            ));

            
            /************ Captcha Code  *****************/
            $captcha = $this->createElement('text','captcha',
                                array(
                                     'value' => trim($params['captcha']),
                                     'class' => 'text_field',
                                    'style' => 'width:100px;',
                                        'id' => 'captcha',
                                    'placeholder' => 'Capcha code*',
                                    'tabindex' => '10',
                                    'autocomplete' => 'off',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                   'validators' => array(array("Alnum", true, array("allowWhiteSpace" => false))),
                             ))	 
                            ->setErrorMessages(array('Enter captcha Code'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            $this->addElements(array(
            $username,
            $password,
                $captcha
            )); 
	}
}
