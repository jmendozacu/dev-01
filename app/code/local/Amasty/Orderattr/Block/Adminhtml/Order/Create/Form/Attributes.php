<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Block_Adminhtml_Order_Create_Form_Attributes extends Mage_Adminhtml_Block_Template
{
    protected $_entityTypeId;
    
    protected $_formElements = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/amorderattr/create_attributes.phtml');
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType('order')->getTypeId();
    }
    
    public function getFormElements()
    {
        if ($this->_formElements)
        {
            return $this->_formElements;
        }

        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        
        $collection->addFieldToFilter('checkout_step', array(2,3,4,5,6));
        $collection->addFieldToFilter('entity_type_id', $this->_entityTypeId);
        
        $collection->getSelect()->order('sorting_order');
        $attributes = $collection->load();
        
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('amorderattr', array());
        $fieldset->addType('checkboxes' , 'Amasty_Orderattr_Block_Adminhtml_Data_Form_Element_Checkboxes');
        $attributeValues = null;

        if ( ($reorderId = Mage::getSingleton('adminhtml/session_quote')->getOrderId())  ||  ($reorderId = Mage::getSingleton('adminhtml/session_quote')->getReordered())  )
        {
            $attributeValues = Mage::getModel('amorderattr/attribute')->load($reorderId, 'order_id');
        }
        
        $orderData = null;
        if ($attributeValues) {
            $orderData = $attributeValues->getData();
        }
        
        if (Mage::getSingleton('adminhtml/session')->getAmastyOrderAttributes()) {
            $orderData = Mage::getSingleton('adminhtml/session')->getAmastyOrderAttributes();
        }
        
        foreach ($attributes as $attribute)
        {
            $currentStore = Mage::getSingleton('adminhtml/session_quote')->getStore()->getId();
            $storeIds = explode(',', $attribute->getData('store_ids'));
            if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds))
            {
                continue;
            }
            
            if ($inputType = $attribute->getFrontend()->getInputType())
            {
                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                // global name space, will merge from all steps
                $fieldName = 'amorderattr[' . $attribute->getAttributeCode(). ']';
                                    
                // default_value
                
                $attributeValue = '';
                if ($attribute->getData('default_value'))
                {
                    $attributeValue = $attribute->getData('default_value');
                }
                if ($attributeValues && $attributeValues->hasData($attribute->getAttributeCode()))
                {
                    $attributeValue = $attributeValues->getData($attribute->getAttributeCode());
                }
                
                // applying translations
                $attributeLabel = $attribute->getFrontendLabel();
        
                $elementOptions=  array(
                    'name'      => $fieldName,
                    'label'     => $attributeLabel,
                    'class'     => $attribute->getFrontend()->getClass(),
                    'required'  => $attribute->getIsRequired(),
                );
                    
                if('checkboxes'==$fieldType || 'radios'==$fieldType) {
                    $elementOptions['name']  .= '[]';
                    if(isset($orderData[$attribute->getAttributeCode()])){
                        $orderData[$attribute->getAttributeCode()] = explode(',', $orderData[$attribute->getAttributeCode()]);
                    }else{
                        $orderData[$attribute->getAttributeCode()] = array();
                    }

                    $elementOptions['values'] = $attribute->getSource()->getAllOptions(false, true);
                    if($attribute->getIsRequired()){
                        $elementOptions['required'] = 0;
                        $elementOptions['class'] = ' validate-checkboxgroup-required';
                    }

                }

                $afterElementHtml = '';
                if ('date' == $fieldType) {
                    $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                    $elementOptions= array(
                        'name' => $fieldName,
                        'label' => $attributeLabel,
                        'title' => $attributeLabel,
                        'image' => $this->getSkinUrl('images/grid-cal.gif'),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                        'format' => $dateFormatIso,
                        'readonly' => 1,
                        'onclick' => 'amorderattr_trig(' . $attribute->getAttributeCode() . '_trig)',
                    );
                    if ('time' == $attribute->getNote()) {
                        $elementOptions['time'] = true;
                        $elementOptions['format'] .= ' HH:mm';
                    }
                    $afterElementHtml .= '<script type="text/javascript">'
                        . 'function amorderattr_trig(id)'
                        . '{ $(id).click(); }'
                        . '</script>';
                }
                
                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType, $elementOptions)
                                    ->setEntityAttribute($attribute);
                
                if ( $inputType == 'select' || $inputType == 'multiselect' || $inputType == 'boolean') {
                    // getting values translations
                    $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setAttributeFilter($attribute->getId())
                        ->setStoreFilter($currentStore, false)
                        ->load();
                        
                    foreach ($valuesCollection as $item) {
                        $values[$item->getId()] = $item->getValue();
                    }
                    
                    // applying translations
                    $options = $attribute->getSource()->getAllOptions(true, true);
                    foreach ($options as $i => $option)
                    {
                        if (isset($values[$option['value']]))
                        {
                            $options[$i]['label'] = $values[$option['value']];
                        }
                    }

                    $element->setAfterElementHtml($afterElementHtml);
                    $element->setValues($options);
                }
            }
        }
        
        $form->setValues($orderData);
 
        $this->_formElements = $form->getElements();
        return $this->_formElements; 
    }

    public function _toHtml()
    {
            $html = parent::_toHtml(); // TODO: Change the autogenerated stub
        $js = "<script type=\"text/javascript\">

           Validation.addAllThese([
        ['validate-checkboxgroup-required', 'Please select an option.', function(v, elm) {
            id = elm.id.slice(0, elm.id.lastIndexOf(\"_\"));
            /*if (h.get(id)) {
             return true;
             }*/
            //h.set(id, true);
            checkboxGroupChecked = false;
            $$('input[id^=' + id + ']').each(function(checkbox){
                if (checkbox.checked || checkbox.hasClassName('validation-failed'))
                {
                    checkboxGroupChecked = true;
                }
            });
            return checkboxGroupChecked;
        }]
    ]);
</script>";
        return $html.$js;

    }
}
