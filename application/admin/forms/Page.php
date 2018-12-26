<?php
class Admin_Form_Page extends Zend_Form
{
	public function __construct($params = null)
	{    
            $cms = new Admin_Model_Cms();
            $allmenues = $cms->getAllMenus();
            $content_menu_list_arr = $cms->getHierarchyMenuTypeList($type='content_menu');
            $content_menu_list = $cms->getTiledMenuAndSubMenu($content_menu_list_arr);
            //echo "<pre>";print_r($content_menu_list);die;
            
            $this->setMethod("POST");
            $this->setAction("admin/staticpage/addpage");
            $this->setName("add_page");
            
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
            
            /************ Hierarchy Menu List *****************/
//            $dataMenu = array();
//	    $dataMenu[""]="--Select--";
//            $hierarchymenulist = $this->createElement('select', 'hierarchy_menu_id');
//                        foreach ($allmenues as $data) {
//                            $dataMenu[$data['id']] = $data['level'];
//                        }
//            $hierarchymenulist->setRequired(true);
//            $hierarchymenulist->setMultiOptions($dataMenu);
//            $hierarchymenulist->setErrorMessages(array('Please select hierarchy menu'));
//            $hierarchymenulist->removeDecorator('label');
//            $hierarchymenulist->removeDecorator('HtmlTag');
//            $hierarchymenulist->setAttrib('onchange', 'getSubMenu(this.value)');
//            $hierarchymenulist->class = "select-xlarge";
            
            
            /************ Page Name  *****************/
            $pageName = $this->createElement('text','pageName',
                                array(
                                     'value' => trim($params['pageName']),
                                     'class' => 'input-xlarge',
                                        'id' => 'pageName',
                                    'required'=>true,
                                   'filters' => array('StringTrim'),
                                  'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter page name'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Page Name  *****************/
//            $pageKey = $this->createElement('text','pageKey',
//                                array(
//                                     'value' => trim($params['pageKey']),
//                                     'class' => 'input-xlarge',
//                                        'id' => 'pageKey',
//                                    'required'=>true,
//                                   'filters' => array('StringTrim'),
//                                    //'validators' => array(array('StringLength', false, array(3, 100))),
//                             ))	 
//                            ->setErrorMessages(array('Please enter page alias name'))
//                            ->removeDecorator('label')
//                            ->removeDecorator('HtmlTag')
//                            ->removeDecorator('DtDdWrapper');
            
            /************ Static Title  *****************/
            $staticTitle = $this->createElement('text','staticTitle',
                                array(
                                     'value' => trim($params['staticTitle']),
                                     'class' => 'input-xlarge',
                                        'id' => 'staticTitle',
                                    'required'=>true,
                                    'filters' => array('StringTrim'),
                                 //'validators' => array(array("Alpha", true, array("allowWhiteSpace" => true))),
                             ))	 
                            ->setErrorMessages(array('Please enter page title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
                           

            /************ Meta Title *****************/				
            $metaTitle = $this->createElement('text','metaTitle',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'metaTitle',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter meta title'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Meta Keywords *****************/				
            $metaKeywords = $this->createElement('text','metaKeywords',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'metaKeywords',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter meta keywords'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Meta Description *****************/				
            $metaDescription = $this->createElement('textarea','metaDescription',
                           array(
                              'required'     => true,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'metaDescription',
                                      'rows' => '5',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter meta description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Background Image *****************/				
            $background_image = $this->createElement('file','background_image',
                        array(
                              'required'     => true,
                              'MaxFileSize' => 2097152,
                              'autocomplete' => 'off',
                                     'class' => 'input-xlarge',
                                        'id' => 'background_image',
                                'validators' => array(
                                array('Count', false, 1),
                                array('Size', false, 2097152),
                                array('Extension', false, 'gif,jpg,png'),
                                array('ImageSize', false, 
                                          array('minwidth' => 1000,
                                                'minheight' => 570,
                                                'maxwidth' => 1000,
                                                'maxheight' => 570)),
                              )
                            ))
                            ->setErrorMessages(array('Please upload (gif, jpg and png) file'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Static Description *****************/				
            $staticDescription = $this->createElement('textarea','staticDescription',
                        array(
                              'required'     => false,
                              'autocomplete' => 'off',
                                     'class' => 'textarea-xlarge',
                                        'id' => 'staticDescription',
                                  'readonly' => 'readonly',
                                  'rows' => '10',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter static description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Page description temporary *****************/				
            $page_description_temporary = $this->createElement('textarea','page_description_temporary',
                        array(
                              'required'     => false,
                              'autocomplete' => 'off',
                                     'class' => 'fckeditor',
                                        'id' => 'page_description_temporary',
                                   'filters' => array('StringTrim'),
                            ))
                            ->setErrorMessages(array('Please enter static description'))
                            ->removeDecorator('label')
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('DtDdWrapper');
            
            /************ Content Template Type *****************/
            $dataContentMenu = array();
            $dataContentMenu[""]="--Select Status--";
            $dataContentMenuArr = array('title_and_image'=>'Title and Image',
                                        'half_text_and_half_image'=>'Half Text and Half Image',
                                        'half_text_and_half_image_and_content_withlink'=>'Image and Content with link',
                                        'half_text_and_half_image_content_withlink_diffposition'=>'Half Text and Half Image Content With Link Different Position',
                                        'half_text_and_half_image_orcontentlink_withtitle_diffposition'=>'Half Text and Half Image OR Content Link and Title Different Position',
                                        'full_text_and_half_image'=>'Full Text and Half Image',
                                   'faq_and_half_image_orwithlink'=>'Faq and Half Image OR With Link',
                                                   'board_listing'=>'Board Listing'
                                       );    
            $content_template_type = $this->createElement('select', 'content_template_type');
                            foreach ($dataContentMenuArr as $key=>$val) {
                            $dataContentMenu[$key] = $val;
                        }
            $content_template_type->setRequired(true);
            $content_template_type->setMultiOptions($dataContentMenu);
            $content_template_type->setErrorMessages(array('Please select content menu type'));
            $content_template_type->removeDecorator('label');
            $content_template_type->removeDecorator('HtmlTag');
            $content_template_type->class = "input-xlarge";
            
            /************ Content Menu Link *****************/
            $contentMenu = array();
            $contentMenu[""]="--Select--";
            $content_menu_link = $this->createElement('multiselect', 'content_menu_link_id');
                            foreach($content_menu_list as $key=>$val) {
                            $contentMenu[$val['id']] = $val['level'].' (Page Url :'.$val['url'].')';
                        }
            $content_menu_link->setRequired(false);
            $content_menu_link->setMultiOptions($contentMenu);
            $content_menu_link->setErrorMessages(array('Please select status'));
            $content_menu_link->removeDecorator('label');
            $content_menu_link->removeDecorator('HtmlTag');
            $content_menu_link->addDecorators(array(
                            'ViewHelper',
                            'Errors',
                            'HtmlTag',
                            'Label'
                            ));
            $content_menu_link->class = "select-large-xlarge";
            
            /************ Status *****************/
            $dataStatus = array();
            $dataStatus[""]="--Select Status--";
            $dataMenuarr = array('Activate'=>'Activate','Deactivate'=>'Deactivate');    
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
            $id,   
            $pageName,
            //$pageKey,
            $staticTitle,
            $metaTitle,
            $metaKeywords,
            $metaDescription,
            $background_image,
            $staticDescription,
            $page_description_temporary,
            $content_template_type, 
            $content_menu_link,    
            $status    
            )); 
	}
}
