<?php

class Flexsin_EmailCheckForget extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Your email address not exit in database'
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
            return true;
            
        } else {
            $this->_error(self::NOT_MATCH);
            return false;
        }
    }

}

