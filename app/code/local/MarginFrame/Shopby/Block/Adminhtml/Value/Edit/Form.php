<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
class MarginFrame_Shopby_Block_Adminhtml_Value_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /** @var  MarginFrame_Shopby_Model_Value */
    protected $model;

    protected function _prepareForm()
    {
        //create form structure
        $form = new Varien_Data_Form(array(
          'id' => 'edit_form',
          'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
          'method' => 'post',
          'enctype' => 'multipart/form-data')
         );

        $form->setUseContainer(true);
        $this->setForm($form);

        $this->model = Mage::registry('amshopby_value');

        $this->prepareFieldsetFeatured();

        $this->prepareFieldsetMeta();

        $this->prepareFieldsetMain();

        $this->prepareFieldsetProductView();

        $this->prepareFieldsetNavigation();

        if($this->model->getFilterId()==2){
            $this->prepareFieldsetAtributeColor();
        }

        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($this->model) {
            $form->setValues($this->model->getData());
            $optionId = $this->model->getOptionId();
            $swatchModel = Mage::getModel('amconf/swatch')->load($optionId);
            if($swatchModel->getColor()){
                $form->getElement('color_swatch')->setValue($swatchModel->getColor());
                $form->getElement('use_color_picker')->setValue(1);
            }
        }

        return parent::_prepareForm();
    }

    protected function prepareFieldsetFeatured()
    {
        $fldSet = $this->_form->addFieldset('set', array('legend'=> $this->__('General')));
        $fldSet->addField('url_alias', 'text', array(
            'label'     => $this->__('URL Alias'),
            'name'      => 'url_alias',
            'required'  => false,
            'note'      => 'Write preferred SEO-friendly URL key to be added to category URL when filtered by this value.',
        ));
        $fldSet->addField('is_featured', 'select', array(
            'label'     => $this->__('Featured'),
            'name'      => 'is_featured',
            'values'    => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('catalog')->__('No')
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('catalog')->__('Yes')
                ),
            ),
            'onchange'  => 'featured(this)',
        ));

        $fldSet->addField('featured_order', 'text', array(
            'label'     => $this->__('Featured Order'),
            'name'      => 'featured_order',
        ));
    }

    protected function prepareFieldsetMeta()
    {
        $fldMeta = $this->_form->addFieldset('meta', array('legend'=> $this->__('Meta Data')));
        $fldMeta->addType('multistoreinput','MarginFrame_Shopby_Lib_Varien_Data_Form_Element_Multistoreinput');
        $fldMeta->addField('meta_title', 'multistoreinput', array(
            'label'     => $this->__('Page Title Tag'),
            'name'      => 'meta_title',
        ));
        $fldMeta->addField('meta_descr', 'multistoreinput', array(
            'label'     => $this->__('Meta-Description Tag'),
            'name'      => 'meta_descr',
        ));
        $fldMeta->addField('meta_kw', 'multistoreinput', array(
            'label' => $this->__('Meta-Keyword Tag'),
            'name' => 'meta_kw',
        ));
    }

    protected function prepareFieldsetMain()
    {
        $fldMain = $this->_form->addFieldset('main', array('legend'=> $this->__('Products List Page')));

        $note = new Varien_Data_Form_Element_Note(array(
            'text' => $this->__('You need to enable option <b>Show on List</b> on filter edit page in order to make these settings work on non-brand pages.'),
        ));
        $note->setId('product_list_note');
        $fldMain->addElement($note);

        $fldMain->addType('multistoreinput','MarginFrame_Shopby_Lib_Varien_Data_Form_Element_Multistoreinput');
        $fldMain->addType('multistoretext','MarginFrame_Shopby_Lib_Varien_Data_Form_Element_Multistoretext');

        $fldMain->addField('title', 'multistoreinput', array(
            'label'     => $this->__('Title'),
            'name'      => 'title',
            )
        );

        $fldMain->addField('descr', 'multistoretext', array(
            'label'     => $this->__('Description'),
            'name'      => 'descr',
        ));

        $cmsBlocks = Mage::getResourceModel('cms/block_collection')->load()->toOptionArray();
        array_unshift($cmsBlocks, array('value' => null, 'label' => $this->__('Please select a static block ...')));
        $fldMain->addField('cms_block_id', 'select', array(
            'label'     => $this->__('Top CMS Block'),
            'name'      => 'cms_block_id',
            'values'    => $cmsBlocks,
        ));
        $fldMain->addField('cms_block_bottom_id', 'select', array(
            'label'     => $this->__('Bottom CMS Block'),
            'name'      => 'cms_block_bottom_id',
            'values'    => $cmsBlocks,
        ));

        $fldMain->addField('img_big', 'file', array(
            'label'     => $this->__('Category Image'),
            'name'      => 'img_big',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($this->model->getImgBig().'?'.strtotime(date('H:i:s'))),
        ));
        $fldMain->addField('remove_img_big', 'checkbox', array(
            'label'     => $this->__('Remove Image'),
            'name'      => 'remove_img_big',
            'value'     => 1,
        ));
    }

    protected function prepareFieldsetProductView()
    {
        $fldView = $this->_form->addFieldset('view', array('legend'=> $this->__('Product Related Image')));
        $note = new Varien_Data_Form_Element_Note(array(
            'text' => $this->__('This image will be displayed near the products having current attribute option selected. You need to perform some modifications in theme template, please see MarginFrame Improved Navigation User Guide on pages 16-17.'),
        ));
        $note->setId('product_view_note');
        $fldView->addElement($note);

        $fldView->addField('img_medium', 'file', array(
            'label'     => $this->__('Image'),
            'name'      => 'img_medium',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($this->model->getImgMedium()),
        ));
        $fldView->addField('remove_img_medium', 'checkbox', array(
            'label'     => $this->__('Remove Image'),
            'name'      => 'remove_img_medium',
            'value'     => 1,
        ));
    }

    protected function prepareFieldsetNavigation()
    {
        $fldNav = $this->_form->addFieldset('navigation', array('legend'=> $this->__('Layered Navigation')));
        $note = new Varien_Data_Form_Element_Note(array(
            'text' => $this->__('Please set filter display type to <b>Images only</b> or <b>Images and labels</b> in order to make these settings work.'),
        ));
        $note->setId('layered_navigation_note');
        $fldNav->addElement($note);

        $fldNav->addField('img_small', 'file', array(
            'label'     => $this->__('Image'),
            'name'      => 'img_small',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($this->model->getImgSmall()),
        ));
        $fldNav->addField('remove_img_small', 'checkbox', array(
            'label'     => $this->__('Remove Image'),
            'name'      => 'remove_img_small',
            'value'     => 1,
        ));
        $fldNav->addField('img_small_hover', 'file', array(
            'label'     => $this->__('Active & Hover Image'),
            'name'      => 'img_small_hover',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($this->model->getImgSmallHover()),
        ));
        $fldNav->addField('remove_img_small_hover', 'checkbox', array(
            'label'     => $this->__('Remove Image'),
            'name'      => 'remove_img_small_hover',
            'value'     => 1,
        ));

    }

    protected function prepareFieldsetAtributeColor()
    {
        $fldNav = $this->_form->addFieldset('attribute_color', array('legend'=> $this->__('Atribute Image')));
        $fldNav->addType('color', 'MarginFrame_Shopby_Block_Adminhtml_Value_Renderer_Color');
        $fldNav->addType('preview', 'MarginFrame_Shopby_Block_Adminhtml_Value_Renderer_Preview');

        $fldNav->addField('use_color_picker', 'select', array(
          'label'     => $this->__('Use Color Picker'),
          'name'      => 'use_color_picker',
          'values'    => array(
            1 => 'Yes',
            0 => 'No - Upload image'
          )
        ));

        $fldNav->addField('amconf_icon', 'file', array(
          'label'     => $this->__('Image'),
          'name'      => 'amconf_icon',
          'required'  => false,
          'after_element_html' => $this->getImageColorHtml($this->model->getOptionId()),
        ));
        $fldNav->addField('amconf_icon_delete', 'checkbox', array(
          'label'     => $this->__('Remove Image'),
          'name'      => 'amconf_icon_delete',
          'onclick'   => 'this.value = this.checked ? 1 : 0;',
        ));

        $fldNav->addField('color_swatch', 'color', array(
          'name'      => 'color_swatch',
          'label'     => $this->__('Color Swatches Picker'),
        ));

        $fldNav->addField('preview', 'preview', array(
          'name'      => 'preview',
          'label'     => $this->__('Preview'),
        ));

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
          ->addFieldMap('use_color_picker', 'use_color_picker')
          ->addFieldMap('color_swatch', 'color_swatch')
          ->addFieldMap('preview', 'preview')
          ->addFieldMap('amconf_icon', 'amconf_icon')
          ->addFieldMap('amconf_icon_delete','amconf_icon_delete')
          ->addFieldDependence('color_swatch', 'use_color_picker', 1)
          ->addFieldDependence('preview', 'use_color_picker', 1)
          ->addFieldDependence('amconf_icon', 'use_color_picker', 0)
          ->addFieldDependence('amconf_icon_delete', 'use_color_picker', 0)
        );

    }

    private function getImageHtml($img)
    {
        $html = '';
        if ($img){
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img src="'.Mage::getBaseUrl('media') . 'amshopby/' . $img .'" />';
            $html .= '</p>';
        }
        return $html;
    }

    private function getImageColorHtml($optionId)
    {
        $uploadDir = Mage::getBaseUrl('media') . 'amconf' . '/' . 'images' . '/';
        $swatchModel = Mage::getModel('amconf/swatch')->load($optionId);
        $html = '';
        if ($optionId && $swatchModel->getExtension()){
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img src="'.$uploadDir . $optionId .'.'.$swatchModel->getExtension() .'" />';
            $html .= '</p>';
        }
        return $html;
    }
}