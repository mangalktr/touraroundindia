<?php
class Admin_Form_Editcomment extends Zend_Form
{
	public function __construct($params = null)
	{    

            $crud = new Admin_Model_CRUD();
            $test = $crud->rv_select_all('tbl_travelogues',['TravId','TravTitle'],[],['TravTitle'=>'ASC']);
            //echo "<pre>"; print_r($test);
           foreach ($test as $key => $value) {
              $Comarray[$value['TravId']] = $value['TravTitle'];
           }  
            //echo "<pre>"; print_r($Comarray); die;
            /************ Page Id *****************/
            $TravId = $this->createElement('hidden','commentId',
                                array(
                                     'value' => '',
                                     'class' => 'input-xlarge',
                                        'id' => 'commentId',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),
                                    //'validators' => array(array('StringLength', false, array(3, 100))),
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                                     
            
            /************ Blog  *****************/
            $travel = array();
            $travel[""]="--Select Blog Title--";
            $dataMenuarr = $Comarray;  
            $blog = $this->createElement('select', 'blogId');
                        foreach ($dataMenuarr as $key=>$val) {                                                                            
                               $travel[$key] = $val;                            
                        } 
            $blog->setRequired(false);
            $blog->setMultiOptions($travel);
            $blog->setErrorMessages(array('Please select no of travellers'));
            $blog->removeDecorator('label');
            $blog->removeDecorator('HtmlTag');
            $blog->class = "input-xlarge";      
            
           
         
             
            
    /************ Name By *****************/				
            $name = $this->createElement('text','name',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'name',
                               'autocomplete'=>'off',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter Uploader name '))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');         
                        
 
      /************ email *****************/				
            $email = $this->createElement('text','emailId',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'emailId',
                               'autocomplete'=>'off',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter date '))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');      
 
            
         /************ Phone *****************/				
           
           			
            $phone = $this->createElement('text','phone',
                           array(
                              'required'     => false,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'phone',
                               'autocomplete'=>'off',
                               'value'=>'0',
//                                   'filters' => array('StringTrim'),
                            ))
                             ->addFilter('StringTrim')
                            ->addFilter('StripTags')
                            ->addValidator('Digits')
                            ->setErrorMessages(array('Please enter cost per person'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper'); 
 
  
            
            /************comment *****************/				
            $comment = $this->createElement('textarea','comment',
                        array(
                              'required'     => false,
                              'autocomplete' => 'off',
                                     'class' => 'textarea-xlarge',
                                        'id' => 'comment',
                               'autocomplete'=>'off',
//                                  'readonly' => 'readonly',
                                  'rows' => '10',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
             /************ Status *****************/
            $dataStatus = array();
            $dataStatus[""]="--Select Status--";
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
            $TravId,   
            $blog,
            $name,
            $email,
            $phone,
            $comment,            
            $status,            
            )); 
	}
}
