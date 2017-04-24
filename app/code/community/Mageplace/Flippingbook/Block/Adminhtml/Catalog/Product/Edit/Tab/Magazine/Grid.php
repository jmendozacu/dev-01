<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */



class Mageplace_Flippingbook_Block_Adminhtml_Catalog_Product_Edit_Tab_Magazine_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_productId;

	public function __construct()
	{
		$this->_productId = $this->getRequest()->getParam('id');

		parent::__construct();

		$this->setId('flippingbook_magazine_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('magazine_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}


	protected function _prepareCollection()
	{
		/* @var $collection Mageplace_Flippingbook_Model_Mysql4_Magazine_Collection */
		$collection = Mage::getResourceModel('flippingbook/magazine_collection');
		if (!Mage::app()->isSingleStoreMode()) {
			if ($this->getRequest()->getParam('website')) {
				$storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
				$storeId = array_pop($storeIds);
			} else if ($this->getRequest()->getParam('group')) {
				$storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
				$storeId = array_pop($storeIds);
			} else if ($this->getRequest()->getParam('store')) {
				$storeId = (int)$this->getRequest()->getParam('store');
			} else {
				$storeId = '';
			}

			$collection->addStoreFilter($storeId);
		}

		if($this->_productId) {
			$collection->setProductId($this->_productId);
		}

		$this->setCollection($collection);

		parent::_prepareCollection();

		return $this;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('magazine_id',
			array(
				'type'		=> 'number',
				'header'	=> $this->__('Book ID'),
				'width'		=> '80px',
				'index'		=> 'magazine_id',
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineIdCondition'
				)
			)
		);

		$this->addColumn(
			'magazine_name',
			array(
				'type'		=> 'text',
				'header'	=> $this->__('Book Name'),
				'index'		=> 'magazine_title',
			)
		);

		$this->addColumn('magazine_category_id',
			array(
				'type'						=> 'options',
				'header'					=> $this->__('Book Category'),
				'index'						=> 'magazine_category_id',
				'options'					=> $this->_getMagazineCategories(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineCategoryCondition'
				)
			)
		);

		$this->addColumn('magazine_template_id',
			array(
				'type'						=> 'options',
				'header'					=> $this->__('Book Template'),
				'index'						=> 'magazine_template_id',
				'options'					=> $this->_getMagazineTemplates(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineTemplateCondition'
				)
			)
		);

		$this->addColumn('magazine_resolution_id',
			array(
				'type'						=> 'options',
				'header'					=> $this->__('Book Resolution'),
				'index'						=> 'magazine_resolution_id',
				'options'					=> $this->_getMagazineResolutions(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineResolutionCondition'
				)
			)
		);

		$this->addColumn('sort_order',
			array(
				'type'		=> 'number',
				'header'	=> $this->__('Position'),
				'index'		=> 'magazine_sort_order',
//				'editable'	=> true
			)
		);

		$this->addColumn('is_active',
			array(
				'type'		=> 'options',
				'header'	=> Mage::helper('cms')->__('Active'),
				'index'		=> 'is_active',
				'width'		=> '70px',
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);

		$this->addColumn('product_attached',
			array(
				'type'		=> 'options',
				'header'	=> Mage::helper('cms')->__('Attached'),
				'index'		=> 'product_attached',
				'width'		=> '70px',
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				),
				'filter_condition_callback'	=> array(
					$this,
					'_filterProductAttachedCondition'
				)
			)
		);

		$this->addColumn('action',
			array(
				'type'		=> 'action',
				'header'	=> Mage::helper('adminhtml')->__('Action'),
				'align'     => 'center',
				'width'		=> '50px',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Attach/Detach'),
						'onClick'	=> 'flippingbookJs.setRelation($magazine_id, $product_attached);',
						'url'		=> 'javascript:void(0);',
					),
				),
				'filter'	=> false,
				'sortable'	=> false,
			)
		);

		return parent::_prepareColumns();
	}

	protected function _filterMagazineIdCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addMagazineIdFilter($value);
	}

	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addStoreFilter($value);
	}

	protected function _getMagazineCategories()
	{
		return Mage::getResourceModel('flippingbook/category_collection')->toOptionHash();
	}


	protected function _filterMagazineCategoryCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addCategoryFilter($value);
	}

	protected function _getMagazineTemplates()
	{
		return Mage::getResourceModel('flippingbook/template_collection')->toOptionHash();
	}


	protected function _filterMagazineTemplateCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addTemplateFilter($value);
	}


	protected function _getMagazineResolutions()
	{
		return Mage::getResourceModel('flippingbook/resolution_collection')->toOptionHash();
	}


	protected function _filterMagazineResolutionCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addResolutionFilter($value);
	}


	protected function _filterProductAttachedCondition($collection, $column)
	{
		$value = $column->getFilter()->getValue();
		if(is_null($value)) {
			return;
		}

		$this->getCollection()->addProductAttachedFilter($value);
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/flippingbook/product', array('_current' => true));
	}

	public function getRowUrl($row)
	{
		return '';
	}

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $productId = Mage::app()->getRequest()->getParam('id');
        if($productId){
            $html .= "
            <script type=\"text/javascript\">
            //<![CDATA[
                MageplaceFlippingbook = {};
                MageplaceFlippingbook.Magazine = Class.create();
                MageplaceFlippingbook.Magazine.prototype = {
                    productId : {$productId},

                    initialize : function() {},

                    setRelation : function(magazineId, productAttached) {
                        new Ajax.Request('{$this->getUrl('*/flippingbook/attach')}',
                        {
                            parameters: {
                                product_id: this.productId,
                                magazine_id: magazineId,
                                product_attached: productAttached,
                            },
                            evalScripts: true,
                            onSuccess: function(response) {
                                this.complete(response);
                            }.bind(this)
                        });
                    },

                    complete : function(transport) {
                        flippingbook_magazine_gridJsObject.doFilter();
                    }
                }

                var flippingbookJs = new MageplaceFlippingbook.Magazine();
                //]]>
            </script>
            ";
        }


		return $html;
    }

}
