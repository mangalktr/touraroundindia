<?php

class Admin_Form_Addtravelogues extends Zend_Form {

    public function __construct($params = null) {

        $crud = new Admin_Model_CRUD();
        $test = $crud->rv_select_all('tb_tbb2c_destinations', ['Title', 'DesSysId'], ['DesSysId'], ['DesSysId' => 'DESC']);
//         echo "<pre>";print_r($test);die;
        foreach ($test as $key => $value) {
            $array[] = array($value['DesSysId'] => $value['Title']);
        }

        $noofdays = 10;
        for ($i = 1; $i <= $noofdays; $i++) {
            $arr_days[] = $i;
        }

        $nooftravel = 10;
        for ($j = 1; $j <= $nooftravel; $j++) {
            $arr_travel[] = $j;
        }

        /*         * ********** Page Id **************** */
        $TravId = $this->createElement('hidden', 'TravId', array(
                    'value' => '',
                    'class' => 'input-xlarge',
                    'id' => 'TravId',
                    'required' => false,
                    'filters' => array('StringTrim'),
                        //'validators' => array(array('StringLength', false, array(3, 100))),
                ))
                ->setErrorMessages(array('Please enter level name'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');


        /*         * ********** Page Name  **************** */
        $TravTitle = $this->createElement('text', 'TravTitle', array(
                    'value' => trim($params['TravTitle']),
                    'class' => 'input-xlarge',
                    'id' => 'TravTitle',
                    'required' => true,
                    'autocomplete' => 'off',
//                    'filters' => array('StringTrim'),
//                    'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                ))
                ->setErrorMessages(array('Please enter Title'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');





        /*         * ********** Uploaded By **************** */
        $TravUploadedBy = $this->createElement('text', 'TravUploadedBy', array(
                    'required' => true,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'TravUploadedBy',
                    'autocomplete' => 'off',
//                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter uploader name'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

















        /*         * ********** Banner Image **************** */
        $TravBannerImage = $this->createElement('file', 'TravBannerImage', array(
                    'required' => true,
                    'MaxFileSize' => 2097152,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'TravBannerImage',
                    'validators' => array(
                        array('Count', false, 1),
                        array('Size', false, 2097152),
                        array('Extension', false, 'gif,jpg,png'),
//                                array('ImageSize', false, 
//                                          array('minwidth' => 1600,
//                                                'minheight' => 420,
//                                                'maxwidth' => 2500,
//                                                'maxheight' => 480)),
                    )
                ))
                ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

        /*         * ********** Blog Image **************** */
        $TravImage = $this->createElement('file', 'TravBlogImage', array(
                    'required' => true,
                    'MaxFileSize' => 2097152,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'TravBlogImage',
                    'validators' => array(
                        array('Count', false, 1),
                        array('Size', false, 2097152),
                        array('Extension', false, 'gif,jpg,png'),
//                                array('ImageSize', false, 
//                                          array('minwidth' => 1600,
//                                                'minheight' => 420,
//                                                'maxwidth' => 2500,
//                                                'maxheight' => 480)),
                    )
                ))
                ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');


        /*         * **********Description **************** */
        $TravDescription = $this->createElement('textarea', 'TravDescription', array(
                    'required' => false,
                    'autocomplete' => 'off',
                    'class' => 'textarea-xlarge',
                    'id' => 'TravDescription',
                    'autocomplete' => 'off',
//                                  'readonly' => 'readonly',
                    'rows' => '10',
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter description'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');


        /*         * ********** Keyword **************** */
        $keyword = $this->createElement('textarea', 'keyword', array(
//                                     'value' => trim($params['Blogkeyword']),
                    'class' => 'textarea-xlarge-new',
                    'id' => 'keyword',
//                                    'required'=>true,
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter keyword'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');
        /*         * ********** Description **************** */
        $description = $this->createElement('textarea', 'description', array(
//                                     'value' => trim($params['Blogdescription']),
                    'class' => 'textarea-xlarge-new',
                    'id' => 'description',
//                                    'required'=>true,
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter description'))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');
        /*         * ********** Meta Tag **************** */
        $metatag = $this->createElement('textarea', 'metatag', array(
//                                     'value' => trim($params['Blogmetatag']),
                    'class' => 'textarea-xlarge-new',
                    'id' => 'metatag',
//                                    'required'=>true,
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter metatag'))
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
        $status->setRequired(true);
        $status->setMultiOptions($dataStatus);
        $status->setErrorMessages(array('Please select status'));
        $status->removeDecorator('label');
        $status->removeDecorator('HtmlTag');
        $status->class = "input-xlarge";

        /*         * ********** Tags **************** */
        $TravTag = $this->createElement('text', 'TravTags', array(
                    'required' => false,
                    'autocomplete' => 'off',
                    'class' => 'input-xlarge',
                    'id' => 'TravTags',
                    'autocomplete' => 'off',
                    'filters' => array('StringTrim'),
                ))
                ->setErrorMessages(array('Please enter Uploader name '))
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->removeDecorator('DtDdWrapper');

        $this->addElements(array(
            $TravId,
            $TravTitle,
            $TravImage,
            $TravBannerImage,
            $TravUploadedBy,
            $TravDescription,
            $status,
            $keyword,
            $description,
            $metatag,
            $TravTag
        ));
    }

}
