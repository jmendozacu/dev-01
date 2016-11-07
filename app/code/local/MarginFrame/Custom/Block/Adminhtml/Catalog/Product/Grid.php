<?php
class MarginFrame_Custom_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('ecommerce')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId() != null) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
          array(
            'header'=> Mage::helper('catalog')->__('ID'),
            'width' => '50px',
            'type'  => 'number',
            'index' => 'entity_id',
            'position' => '1',
          ));

        $this->addColumn('image', array(
          'header' => Mage::helper('catalog')->__('Image'),
          'align' => 'left',
          'index' => 'image',
          'width'     => '97',
          'renderer' => 'MarginFrame_Custom_Block_Adminhtml_Catalog_Product_Renderer_Image',
            //'sortable' => false,
            //'filter'   => false,
          'type'  => 'options',
          'options' => array(
            'Yes'   => 'Has Image', //Mage::helper('catalog')->__('Yes'),
            'No'    => 'No Image', //Mage::helper('catalog')->__('No')
          ),
          'filter_condition_callback' => array($this, '_hasimageFilter'),
        ));

        $this->addColumnAfter('category_list', array(
          'header'    => Mage::helper('catalog')->__('Category'),
          'index'     => 'category_list',
          'sortable'  => false,
          'width' => '200px',
          'type'  => 'options',
          'options'   => Mage::getSingleton('MarginFrame_Custom/System_Config_Source_Category')->toOptionArray(),
          'renderer'  => 'MarginFrame_Custom_Block_Adminhtml_Catalog_Product_Renderer_Category',
          'filter_condition_callback' => array($this, 'filterCallback'),
        ),'image');

        $this->addColumn('sku',
          array(
            'header'=> Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
          ));

        $this->addColumn('name',
          array(
            'header'=> Mage::helper('catalog')->__('Name'),
            'index' => 'name',
          ));

        $store = $this->_getStore();
        $this->addColumn('price',
          array(
            'header'=> Mage::helper('catalog')->__('Price'),
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index' => 'price',
          ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
              array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type'  => 'number',
                'index' => 'qty',
              ));
        }

        $this->addColumn('visibility',
          array(
            'header'=> Mage::helper('catalog')->__('Visibility'),
            'width' => '70px',
            'index' => 'visibility',
            'type'  => 'options',
            'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
          ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumnAfter('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ),'visibility');

      $this->addColumnAfter('ecommerce',
        array(
          'header'=> Mage::helper('catalog')->__('Ecommerce Attrib.'),
          'width' => '50px',
          'index' => 'ecommerce',
          'type'  => 'options',
          'options' => array(
            '1'   =>  Mage::helper('catalog')->__('Yes'),
            '0'    => Mage::helper('catalog')->__('No'),
          ),
        ),'status');

      $this->addColumn('type',
        array(
          'header'=> Mage::helper('catalog')->__('Type'),
          'width' => '60px',
          'index' => 'type_id',
          'type'  => 'options',
          'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

      $this->addColumn('set_name',
        array(
          'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
          'width' => '100px',
          'index' => 'attribute_set_id',
          'type'  => 'options',
          'options' => $sets,
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _hasimageFilter($collection, $column)
    {
        $this->getCollection()->removeAttributeToSelect('image');
        $value = $column->getFilter()->getValue();
        if($value == 'No'){       
            $this->getCollection()->addAttributeToFilter('image', 
                array(
                        array('null' => true),
                        array('eq'   => 'no_selection'),
                )
                , 'left');
            //var_dump((string)$this->getCollection()->getSelect()); die;
        }
        elseif($value == 'Yes'){
            $this->getCollection()
                ->addAttributeToFilter('image', array('neq' => 'NULL'))
                ->addAttributeToFilter('image', array('neq' => 'no_selection'))
            ;
        }
        return $this;
    }

    public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $_category = Mage::getModel('catalog/category')->load($value);
        $collection->addCategoryFilter($_category);

        return $collection;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
            ));
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
}

?>