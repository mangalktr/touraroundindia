<?php

class Admin_Form_Editpack extends Zend_Form {

    public function __construct($params = null) {


        /*         * ********** Page Id **************** */
        $packType = $this->createElement('hidden', 'packType', array(
                    'value' => '',
                    'class' => 'input-xlarge',
                    'id' => 'packType',
                    'required' => false,
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter level name'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

        
        /************ Background Image *****************/
        $banner_image = $this->createElement('file', 'banner_image', array(
                    'required' => false,
                    'MaxFileSize' => 2097152,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'image',
                    'validators' => array(
                        array('Count', false, 1),
                        array('Size', false, 2097152),
                        array('Extension', false, 'gif,jpg,png'),
                        array('ImageSize', false,
                            array('minwidth' => 1200,
                                'minheight' => 400,
                                'maxwidth' => 1349,
                                'maxheight' => 511)),
                    )
                ))
                ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');


        $this->addElements(array(
            $packType,
            $banner_image,
        ));
    }

}
