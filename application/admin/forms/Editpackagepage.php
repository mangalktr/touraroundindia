<?php
class Admin_Form_Editpackagepage extends Zend_Form
{
	public function __construct($params = null)
	{    

            /************ Background Image *****************/				
            $image = $this->createElement('file','image[]',
                        array(
                              'required'     => false,
                              'MaxFileSize' => 2097152,
                              'autocomplete' => 'off',
                            
                            'multiple' => 'multiple',
                                     'class' => 'input-xlarge',
                                        'id' => 'image',
                                'validators' => array(
//                                array('Count', false, ),
                                array('Size', false, 2097152),
                                array('Extension', false, 'gif,jpg,png,jpeg'),
//                                array('ImageSize', false, 
//                                         array('minwidth' => 175,
//                                                'minheight' => 175,
//                                                'maxwidth' => 360,
//                                                'maxheight' => 360)),
                              )
                            ))
                            ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');

            /************ Hot Deal *****************/				
            $hot_deal = $this->createElement('text','hot_deal',
                                array(
                                     'value' => trim($params['hot_deal']),
                                     'class' => 'input-xlarge',
                                        'id' => 'hot_deal',
//                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 
                             ))	 
                            ->setErrorMessages(array('Please enter hot deal'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Keyword *****************/
                        $keyword = $this->createElement('textarea','keyword',
                                array(
                                     'value' => trim($params['keyword']),
                                     'class' => 'textarea-xlarge-new',
                                        'id' => 'keyword',
//                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 
                             ))	 
                            ->setErrorMessages(array('Please enter keyword'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            /************ Description *****************/
                        $description = $this->createElement('textarea','description',
                                array(
                                     'value' => trim($params['description']),
                                     'class' => 'textarea-xlarge-new',
                                        'id' => 'description',
//                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 
                             ))	 
                            ->setErrorMessages(array('Please enter description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            /************ Meta Tag *****************/
                        $metatag = $this->createElement('textarea','metatag',
                                array(
                                     'value' => trim($params['metatag']),
                                     'class' => 'textarea-xlarge-new',
                                        'id' => 'metatag',
//                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 
                             ))	 
                            ->setErrorMessages(array('Please enter metatag'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');

            $this->addElements(array(
            
            $image,
            $hot_deal,
            $keyword,
            $description,
            $metatag,
                
            ));
	}
}
