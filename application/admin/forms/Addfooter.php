<?php
class Admin_Form_Addfooter extends Zend_Form
{
	public function __construct($params = null)
	{    

        $crud = new Admin_Model_CRUD();
        $test = $crud->rv_select_all('tbl_static_pages',['page_title'],['status'=>'Activate','page_type'=>'2'],['sid'=>'DESC']);

        foreach ($test as $key => $value) {
          $previous[$value['page_title']] = $value['page_title'];  
        }
            
          
            /************ Page Id *****************/
            $id = $this->createElement('hidden','id',
                                array(
                                     'value' => '',
                                     'class' => 'input-xlarge',
                                        'id' => 'id',
                                    'required'=>false,
                                    'filters' => array('StringTrim'),
                                    //'validators' => array(array('StringLength', false, array(3, 100))),
                             ))	 
                            ->setErrorMessages(array('Please enter level name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************  Name  *****************/
            $name = $this->createElement('text','name',
                                array(
                                     'value' => trim($params['name']),
                                     'class' => 'input-xlarge',
                                        'id' => 'name',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            
            /************ Link  *****************/
            $link = $this->createElement('text','link',
                                array(
                                     'value' => trim($params['link']),
                                     'class' => 'input-xlarge',
                                        'id' => 'link',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 //'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	
                    
                            ->setErrorMessages(array('Please enter link'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           

            /************ Column No *****************/				

            $column_number = array();
            $column_number[""]="--Select Column--";
            $dataMenuarr_r = unserialize(CONST_FOOTER_COL);    
            $column = $this->createElement('select', 'column_number');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $column_number[$key] = $val;
                        }
            
            $column->setMultiOptions($column_number);
            $column->setRequired(true);  
            $column->setErrorMessages(array('Please Enter Column Number'));
            $column->removeDecorator('label');
            $column->removeDecorator('HtmlTag');
            $column->class = "input-xlarge";
            
            /*****************Previous Links*********************/
            
            $prelink = array();
            $prelink[""]="--Select Links--";
            $dataMenuarr_r = $previous;    
            $preLink = $this->createElement('select', 'prelink');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $prelink[$key] = $val;
                        }
            
            $preLink->setMultiOptions($prelink);
//            $preLink->setRequired(true);  
            $preLink->setErrorMessages(array('Please Enter link Number'));
            $preLink->removeDecorator('label');
            $preLink->removeDecorator('HtmlTag');
            $preLink->class = "input-xlarge";
            
            
            
             /************ Status *****************/	
            
            $status_number = array();
            $status_number[""]="--Select Status--";
            $dataMenuarr_r = array('1'=>'Active','0'=>'Deactive');    
            $status = $this->createElement('select', 'status_number');
                        foreach ($dataMenuarr_r as $key=>$val) {
                            $status_number[$key] = $val;
                        }
            
            $status->setMultiOptions($status_number);
            $status->setRequired(true);            
            $status->setErrorMessages(array('Please Enter Status'));
            $status->removeDecorator('label');
            $status->removeDecorator('HtmlTag');
            $status->class = "input-xlarge";
            
            
            
            $this->addElements(array(
            $id,   
            $name,
            $link,
            $preLink,
            $column,
            $status,
                
            )); 
	}
}
