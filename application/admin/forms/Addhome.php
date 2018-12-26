<?php

class Admin_Form_Addhome extends Zend_Form {

    public function __construct($params = null) {



        /*         * ********** heading  **************** */
        $heading = $this->createElement('text', 'heading', array(
                    'value' => trim($params['heading']),
                    'class' => 'input-xlarge',
                    'id' => 'heading',
                    'required' => false,
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter heading'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

        /*         * ********** description  **************** */
        $description = $this->createElement('textarea', 'description', array(
                    'required' => false,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'description',
                    'rows' => '5',
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter description'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');


        /*         * ********** url **************** */
        $url = $this->createElement('text', 'url', array(
                    'value' => trim($params['url']),
                    'required' => false,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'url',
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter url'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

        /*         * ********** option **************** */
        $opt = $this->createElement('checkbox', 'opt');
        $opt->setRequired(false);
        $opt->setErrorMessages(array('Please select url type'));
        $opt->removeDecorator('label');
        $opt->removeDecorator('HtmlTag');
        $opt->class = "option";

        /*         * ********** Background Image **************** */
        $image = $this->createElement('file', 'image', array(
                    'required' => false,
                    'MaxFileSize' => 2097152,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'image',
//                                'validators' => array(
//                                array('Count', false, 1),
//                                array('Size', false, 2097152),
//                                array('Extension', false, 'gif,jpg,png'),
//                                array('ImageSize', false, 
//                                          array('minwidth' => 1200,
//                                                'minheight' => 400,
//                                                'maxwidth' => 1349,
//                                                'maxheight' => 511)),
//                              )
                ))
                ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

        /*         * ********** Status **************** */
        $dataStatus = array();
        $dataStatus[""] = "--Select Status--";
        $dataMenuarr = array('1' => 'Activate', '0' => 'Deactivate');
        $status = $this->createElement('select', 'status');
        foreach ($dataMenuarr as $key => $val) {
            $dataStatus[$key] = $val;
        }
        $status->setRequired(FALSE);
        $status->setMultiOptions($dataStatus);
        $status->setErrorMessages(array('Please select status'));
        $status->removeDecorator('label');
        $status->removeDecorator('HtmlTag');
        $status->class = "input-xlarge";

        $this->addElements(array(
            $banner_id,
            $heading,
            $description,
            $url,
            $opt,
            $image,
            $status
        ));
    }

}
