<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Blog
 */

class Magpleasure_Blog_Block_Adminhtml_Category_Edit_Tab_Categorystyle extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Helper
     * @return Magpleasure_Blog_Helper_Data
     */
    protected function _helper() {
        return Mage::helper('mpblog');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('blog_form', array('legend' => $this->_helper()->__('Category Style')));

        $style_id = $fieldset->addField('category_style', 'select', array(
            'label' => $this->_helper()->__('Category Style'),
            'class' => 'required-entry',
            'required' => false,
            'name' => 'category_style',
            'values' => Mage::getModel('mpblog/categorystyle')->getOptionArray(),
        ));
      $show_landing_field = false;
        $collection_cat = Mage::getModel('mpblog/category')->getCollection()
          ->addFieldToFilter('category_is_landing', 1);
        if(!$collection_cat->getData()){
          $show_landing_field = true;
        }else{
          if(Mage::registry('current_category')->getId()){
            $collection_cat = Mage::getModel('mpblog/category')->getCollection()
              ->addFieldToFilter('category_is_landing', 1)
              ->addFieldToFilter('category_id', Mage::registry('current_category')->getId());
            if($collection_cat->getData()){
              $show_landing_field = true;
            }
          }
        }
      if($show_landing_field == true){
        $value_landing = $fieldset->addField('category_is_landing', 'select', array(
          'label' => $this->_helper()->__('Is Landing Category'),
          'name' => 'category_is_landing',
          'values'    => array(
            '0' => 'No',
            '1'   => 'Yes'
          )
        ));
      }

      $show_landing_project = false;
      $collection_project = Mage::getModel('mpblog/category')->getCollection()
        ->addFieldToFilter('category_is_landing_project', 1);
      if(!$collection_project->getData()){
        $show_landing_project = true;
      }else{
        if(Mage::registry('current_category')->getId()){
          $collection_project = Mage::getModel('mpblog/category')->getCollection()
            ->addFieldToFilter('category_is_landing_project', 1)
            ->addFieldToFilter('category_id', Mage::registry('current_category')->getId());
          if($collection_project->getData()){
            $show_landing_project = true;
          }
        }
      }
      if($show_landing_project == true){
        $value_landing_project = $fieldset->addField('category_is_landing_project', 'select', array(
          'label' => $this->_helper()->__('Is Landing Category Index Project'),
          'name' => 'category_is_landing_project',
          'values'    => array(
            '0' => 'No',
            '1'   => 'Yes'
          )
        ));
      }

        if (Mage::getSingleton('adminhtml/session')->getPostData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->getPostData(null);
        } elseif (Mage::registry('current_category')) {
            $form->setValues(Mage::registry('current_category')->getData());
        }
      if($show_landing_field == true && $show_landing_project == true){
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($style_id->getHtmlId(), $style_id->getName())
            ->addFieldMap($value_landing->getHtmlId(), $value_landing->getName())
            ->addFieldMap($value_landing_project->getHtmlId(), $value_landing_project->getName())
            ->addFieldDependence(
              $value_landing->getName(),
              $style_id->getName(),
              Magpleasure_Blog_Model_Categorystyle::HOME_DECOR
            )
            ->addFieldDependence(
              $value_landing_project->getName(),
              $style_id->getName(),
              Magpleasure_Blog_Model_Categorystyle::INDEX_PROJECT
            )
        );
      }elseif($show_landing_field == true && $show_landing_project == false){
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($style_id->getHtmlId(), $style_id->getName())
            ->addFieldMap($value_landing->getHtmlId(), $value_landing->getName())
            ->addFieldDependence(
              $value_landing->getName(),
              $style_id->getName(),
              Magpleasure_Blog_Model_Categorystyle::HOME_DECOR
            )
        );
      }elseif($show_landing_field == false && $show_landing_project == true){
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($style_id->getHtmlId(), $style_id->getName())
            ->addFieldMap($value_landing_project->getHtmlId(), $value_landing_project->getName())
            ->addFieldDependence(
              $value_landing_project->getName(),
              $style_id->getName(),
              Magpleasure_Blog_Model_Categorystyle::INDEX_PROJECT
            )
        );
      }



        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->_helper()->__("Category Style");
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->_helper()->__("Category Style");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }
}