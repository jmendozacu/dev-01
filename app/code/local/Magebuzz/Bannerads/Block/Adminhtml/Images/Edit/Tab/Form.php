<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/
class Magebuzz_Bannerads_Block_Adminhtml_Images_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('images_form', array('legend' => Mage::helper('bannerads')->__('Item information')));
    $data = array();

    if (Mage::getSingleton('adminhtml/session')->getCategoryData()) {
      $data = Mage::getSingleton('adminhtml/session')->getCategoryData();
      Mage::getSingleton('adminhtml/session')->setCategoryData(null);
    } elseif (Mage::registry('images_data')) {
      $data = Mage::registry('images_data')->getData();
    }
		
		if (empty($data)) {
			$data['sort_order'] = 100;
		}
    $object = Mage::getModel('bannerads/images')->load($this->getRequest()->getParam('id'));

    $imgPath = Mage::getBaseUrl('media') . "bannerads/images/" . $object->getBannerImage();

    $fieldset->addField('banner_title', 'text', array('label' => Mage::helper('bannerads')->__('Title'), 'class' => 'required-entry', 'required' => TRUE, 'name' => 'banner_title',));

	  $fieldset->addField('use_video', 'select',
		  array(
			  'label' => Mage::helper('bannerads')->__('Use Video'),
			  'name' => 'use_video',
			  'values' => array(
				  array('value' => 0, 'label' => Mage::helper('bannerads')->__('No'),),
				  array('value' => 1, 'label' => Mage::helper('bannerads')->__('Yes'),),
			  ),
			  'note' => 'If use video is yes, image will not show in frontend'
		  )
	  );

	  $fieldset->addField('url_video', 'text', array(
		  'label' => Mage::helper('bannerads')->__('Video Url'),
		  'name' => 'url_video',
	  ));

	  $fieldset->addField('video_width', 'text', array('label' => Mage::helper('bannerads')->__('Video Width'), 'name' => 'video_width','note' => 'If this field is null, will use configure in template'));
	  $fieldset->addField('video_height', 'text', array('label' => Mage::helper('bannerads')->__('Video Height'), 'name' => 'video_height','note' => 'If this field is null, will use configure in template'));
	  $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
		  ->addFieldMap('use_video', 'use_video')
		  ->addFieldMap('url_video', 'url_video')
		  ->addFieldMap('video_height', 'video_height')
		  ->addFieldMap('video_width', 'video_width')
		  ->addFieldDependence(
			  'url_video',
			  'use_video',
			  '1'
		  )
		  ->addFieldDependence(
			  'video_height',
			  'use_video',
			  '1'
		  )
		  ->addFieldDependence(
			  'video_width',
			  'use_video',
			  '1'
		  )
	  );
	  if ($object->getBannerImage()) {
      $fieldset->addField('image', 'label', array('label' => Mage::helper('bannerads')->__('Banner Image'), 'name' => 'image', 'value' => $imgPath, 'after_element_html' => '<img src="' . $imgPath . '" alt=" ' . $imgPath . '" height="120" width="120" />',));
    }

    if ($object->getBannerImage()) {
      $fieldset->addField('banner_image', 'file', array('label' => Mage::helper('bannerads')->__('Change Banner'), 'required' => FALSE, 'name' => 'banner_image',));
    } else {
      $fieldset->addField('banner_image', 'file', array('label' => Mage::helper('bannerads')->__('Choose Banner'), 'required' => FALSE, 'name' => 'banner_image',));
    }


    $fieldset->addField('banner_url', 'text', array('label' => Mage::helper('bannerads')->__('Banner Url'), 'name' => 'banner_url',));
		
		$fieldset->addField('target', 'select', 
			array(
				'label' => Mage::helper('bannerads')->__('Target'), 
				'name' => 'target', 
				'values' => array(
					array('value' => 0, 'label' => Mage::helper('bannerads')->__('Open in the same window'),),
					array('value' => 1, 'label' => Mage::helper('bannerads')->__('Open in new window'),),
				),
			)
		);
		
//		$outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		
		$fieldset->addField('start_time', 'date', 
			array(
				'label' => Mage::helper('bannerads')->__('From Date'), 
				'name' => 'start_time', 
        'format' => 'yyyy-MM-dd HH:mm:ss',
        'time' => TRUE,
				'image' => $this->getSkinUrl('images/grid-cal.gif'),
			)
		);

    $fieldset->addField('end_time', 'date', 
			array(
				'label' => Mage::helper('bannerads')->__('To Date'), 
				'name' => 'end_time', 
        'format' => 'yyyy-MM-dd HH:mm:ss',
        'time' => TRUE,
				'image' => $this->getSkinUrl('images/grid-cal.gif'),
			)
		);
		
		$fieldset->addField('banner_show_desc', 'select', array('label' => Mage::helper('bannerads')->__('Show Description'), 'name' => 'banner_show_desc', 'values' => array(array('value' => 1, 'label' => Mage::helper('bannerads')->__('Yes'),),

      array('value' => 2, 'label' => Mage::helper('bannerads')->__('No'),),),));

    $fieldset->addField('banner_description', 'editor', array('name' => 'banner_description', 'label' => Mage::helper('bannerads')->__('Description'), 'title' => Mage::helper('bannerads')->__('Description'),'style'     => 'width:500px; height:300px;','config'    => Mage::getSingleton('bannerads/wysiwyg_config')->getConfig(),));
		
		$fieldset->addField('banner_description_pos', 'select', 
			array(
				'label' => Mage::helper('bannerads')->__('Description Position'), 
				'name' => 'banner_description_pos', 
				'values' => array(
					array('value' => 'top-left', 'label' => Mage::helper('bannerads')->__('Top Left'),),
					array('value' => 'top-center', 'label' => Mage::helper('bannerads')->__('Top Center'),),
					array('value' => 'top-right', 'label' => Mage::helper('bannerads')->__('Top Right'),),
					array('value' => 'middle-left', 'label' => Mage::helper('bannerads')->__('Middle Left'),),
					array('value' => 'middle-center', 'label' => Mage::helper('bannerads')->__('Middle Center'),),
					array('value' => 'middle-right', 'label' => Mage::helper('bannerads')->__('Middle Right'),),
					array('value' => 'bottom-left', 'label' => Mage::helper('bannerads')->__('Bottom Left'),),
					array('value' => 'bottom-center', 'label' => Mage::helper('bannerads')->__('Bottom Center'),),
					array('value' => 'bottom-right', 'label' => Mage::helper('bannerads')->__('Bottom Right'),),
				),
			)
		);

    $fieldset->addField('sort_order', 'text', array('name' => 'sort_order', 'label' => Mage::helper('bannerads')->__('Sort Order'), 'title' => Mage::helper('bannerads')->__('Sort Order'),));

    $fieldset->addField('status', 'select', array('label' => Mage::helper('bannerads')->__('Status'), 'name' => 'status', 'values' => array(array('value' => 1, 'label' => Mage::helper('bannerads')->__('Enabled'),),

      array('value' => 2, 'label' => Mage::helper('bannerads')->__('Disabled'),),),));

    if ($this->getRequest()->getParam('from_block_id')) {
      $fieldset->addField('from_block_id','hidden',array(
        'name'   => 'from_block_id',
      ));
      $data['from_block_id'] = $this->getRequest()->getParam('from_block_id');
    }

    $form->setValues($data);
    return parent::_prepareForm();
  }
}
