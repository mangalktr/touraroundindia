<?php

class Flexsin_PasswordCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Invalid current passowrd'
    );

    public function isValid($value, $context = null) {
        $value = (string) $value;

        $validator = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => 'admin_user',
                            'field' => 'password'
                        )
        );

       if (($validator->isValid($value))){
            return true;
         }else{
            $this->_error(self::NOT_MATCH);
            return false;

        }
    }
}

