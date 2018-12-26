<?php

class Flexsin_MessageStack {

    private $_storage;
    private $messages;

    public function __construct() {
        $this->_storage = new Zend_Session_Namespace("message");
        $this->messages = array();
    }

    public function add($message, $type = 'error', $class='') {
        $message = trim($message);
        if (strlen($message) > 0) {
            if ($type == 'error') {
                $this->messages[] = array('params' => 'style="height:20px;padding:3px;margin:5px 3px 5px 3px;" class="ui-state-error ui-corner-all"', 'class' => $class, 'text' => '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> ' . $message);
            } elseif ($type == 'warning') {
                $this->messages[] = array('params' => 'style="height:20px;padding:3px;margin:5px 3px 5px 3px;" class="ui-state-highlight ui-corner-all"', 'class' => $class, 'text' => '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>  ' . $message);
            } elseif ($type == 'success') {
                $this->messages[] = array('params' => 'style="height:20px;padding:3px;margin:5px 3px 5px 3px;" class="ui-state-highlight ui-corner-all"', 'class' => $class, 'text' => '<span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span>  ' . $message);
            } elseif ($type == 'caution') {
                $this->messages[] = array('params' => 'style="height:20px;padding:3px;margin:5px 3px 5px 3px;" class="ui-state-highlight ui-corner-all"', 'class' => $class, 'text' => '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>  ' . $message);
            } elseif ($type == 'home') {
             
                $this->messages[] = array('params' => '', 'class' =>$class, 'text' => '' . $message);
            } else {
                $this->messages[] = array('params' => ' style="height:20px;padding:3px;margin:5px 3px 5px 3px;" class="ui-state-error ui-corner-all"', 'class' => $class, 'text' => $message);
            }
        }
        $this->_storage->__set("message", $this->messages);
    }

    public static function output() {
        $storage = new Zend_Session_Namespace("message");
        if (isset($storage->message)) {
            $messages = $storage->message;
        } else {
            $messages = array();
        }
        for ($i = 0, $n = count($messages); $i < $n; $i++) {
            $output[] = $messages[$i];
            echo '<div ' . $output[$i]['params'] . '>' . $output[$i]['text'] . '</div>';
        }
        unset($storage->message);
    }

    public static function outputerror() {
        $storage = new Zend_Session_Namespace("message");
        if (isset($storage->message)) {
            $messages = $storage->message;
        } else {
            $messages = array();
        }
        for ($i = 0, $n = count($messages); $i < $n; $i++) {
            $output[] = $messages[$i];
          echo '<div align="center" class= "'.$output[$i]['class'].'" >' . $output[$i]['text'] . '</div>';
        }
        unset($storage->message);
    }

}

?>