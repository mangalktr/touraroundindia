<?php

class Flexsin_UserCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Login name already exists:'
    );

    public function isValid($value, $context = null) {
        $value = (string) $value;
        $validator = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => 'tbl_user',
                            'field' => 'username'
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

