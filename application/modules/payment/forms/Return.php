<?php
class Payment_Form_Return extends Zend_Form {
    public function init() {
        $inquiry_name = $this->createElement('hidden', 'status');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag');
         $this->addElement($inquiry_name);
       
         $inquiry_name = $this->createElement('hidden', 'GUID');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag');
         $this->addElement($inquiry_name);
        
          $inquiry_name = $this->createElement('hidden', 'TpSysId');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag');
         $this->addElement($inquiry_name);
          $inquiry_name = $this->createElement('hidden', 'TrxId');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag');
         $this->addElement($inquiry_name);
        
         $this->addElement($inquiry_name);
        $inquiry_name = $this->createElement('hidden', 'udf2');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag'); 
        
         $this->addElement($inquiry_name);
        $inquiry_name = $this->createElement('hidden', 'error');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag'); 
        
         $this->addElement($inquiry_name);
        $inquiry_name = $this->createElement('hidden', 'error_Message');
        $inquiry_name->removeDecorator('label')
                ->removeDecorator('HtmlTag'); 
        
         $this->addElement($inquiry_name);
    }

}
