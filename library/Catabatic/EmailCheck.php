<?php

class Flexsin_EmailCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Email already exists:'
    );

    public function isValid($value, $context = null) {
        $value = (string) $value;
        $validator = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => 'tbl_user',
                            'field' => 'email'
                        )
        );
        if ($validator->isValid($value)) {
            $this->_error(self::NOT_MATCH);
            return false;
        } else {
            return true;
        }
    }

}

