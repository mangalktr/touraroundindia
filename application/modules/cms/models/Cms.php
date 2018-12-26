<?php

class Static_Model_Cms {

    protected $_id;
    protected $_mapper;
    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    public function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    public function __set($name, $value) {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw Exception('Invalid property specified');
        }
        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw Exception('Invalid property specified');
        }
        return $this->$method();
    }

    public function setId($text) {

        $this->_id = (string) $text;
        return $this;
    }

    public function getId() {

        return $this->_id;
    }

    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Customer_Model_RegisterMapper());
        }

        return $this->_mapper;
    }

    function getRegistrationDate() {
        $date = new Zend_Date();
        $entry_date = $date->get('YYYY-MM-dd HH:mm:ss');
        return $entry_date;
    }

    public function save() {

        $this->getMapper()->save($this);
    }

}