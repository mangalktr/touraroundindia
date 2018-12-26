<?php
class Admin_Form_Forgot extends Zend_Form
{
    public function __construct($params = null)
    {
        /************ Email  *****************/
        $email = $this->createElement('text','email',
                array('value' => $params['email'],
                        'class' => 'span12',
                           'id' => 'email'			
                        ))
               //->setValidators(array(array("Alpha", true, array("allowWhiteSpace" => false))))	 
               ->setRequired(true)
               //->setErrorMessages(array('Please enter valid email addess'))
               ->addDecorators(
                               array(
                               'ViewHelper',
                               'Errors',
                               array('HtmlTag', array('tag' => 'div')),
                               array('Label', array('tag' => '')),
                       ));

        $this->addElements(array(
        $email
        )); 
    }
}
