<?php
/**
 * This is the part of 'Highlight' module for Magento,
 * which allows easy access to product collection
 * with flexible filters
 *
 * @author Templates-Master
 * @copyright Templates Master www.templates-master.com
 */

class TM_Highlight_Block_Product_List
    extends TM_Highlight_Block_Product_List_Abstract
    implements Mage_Widget_Block_Interface
{
    const DEFAULT_PRODUCTS_COUNT    = 4;
    const DEFAULT_COLUMN_COUNT      = 4;
    const PAGE_TYPE = false;

    protected $_attributeCode;
    protected $_className;
    protected $_priceSuffix;
    protected $_title;
    protected $_categoryFilter = array();
    protected $_priceFilter = array();
    protected $_productTypeFilter = array();
    protected $_sortRules = array();
    protected $_addBundlePriceBlock = true;
    protected $_productCollection;
    protected $_defaultToolbarBlock = 'highlight/product_list_toolbar';
    protected $_toolbarBlock;
		
		/**
     * Price template
     *
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';

    protected static $_productUrlModel = null;

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $categoryFilter = $this->getCategoryFilter();
        if (array_key_exists('current', $categoryFilter)) {
            unset($categoryFilter['current']);
            $this->getCurrentCategory();
            /*
            if ($category = Mage::registry('current_category')) {
                $categoryFilter[] = $category->getId();
            }
            */
        }

        $priceFilter = $this->getPriceFilter();
        $priceFilterString = '';
        foreach ($priceFilter as $filter) {
            $priceFilterString .= implode(',', $filter);
        }

        return array(
           'CATALOG_PRODUCT_HIGHLIGHT',
           Mage::app()->getStore()->getId(),
           Mage::app()->getStore()->getCurrentCurrency()->getCode(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           $this->getTemplate(),
           $this->getProductsCount(),
           $this->getColumnCount(),
           implode(',', $categoryFilter),
           $priceFilterString,
           implode(',', $this->getProductTypeFilter()),
           implode(',', $this->getSortRules()),
           $this->getTitle(),
           $this->getClassName(),
           $this->getAttributeCode(),
           $this->getPriceSuffix(),
           $this->getNameInLayout()
        );
    }
		/**
		 * Prepares and returns block to render some product type
		 *
		 * @param string $productType
		 * @return Mage_Core_Block_Template
		 */
		public function _preparePriceRenderer($productType)
		{
				return $this->_getPriceBlock($productType)
						->setTemplate('catalog/product/price.phtml')
						->setUseLinkForAsLowAs($this->_useLinkForAsLowAs);
		}

    /**
     * Process cached form_key and uenc params
     *
     * @param   string $html
     * @return  string
     */
    protected function _loadCache()
    {
        $cacheData = parent::_loadCache();
        if ($cacheData) {
            $search = array(
                '{{tm_highlight uenc}}'
            );
            $replace = array(
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED
                    . '/' . Mage::helper('core/url')->getEncodedUrl()
            );

            if (defined('Mage_Core_Model_Url::FORM_KEY')) {
                $formKey = Mage::getSingleton('core/session')->getFormKey();
                $search = array_merge($search, array(
                    '{{tm_highlight form_key_url}}',
                    '{{tm_highlight form_key_hidden}}'
                ));
                $replace = array_merge($replace, array(
                    Mage_Core_Model_Url::FORM_KEY . '/' . $formKey,
                    'value="' . $formKey . '"'
                ));
            }

            $cacheData = str_replace($search, $replace, $cacheData);
        }
        return $cacheData;
    }

    /**
     * Replace form_key and uenc with placeholders
     *
     * @param string $data
     * @return Mage_Core_Block_Abstract
     */
    protected function _saveCache($data)
    {
        if (is_null($this->getCacheLifetime())
            || !$this->getMageApp()->useCache(self::CACHE_GROUP)) {

            return false;
        }

        $search = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED
                . '/' . Mage::helper('core/url')->getEncodedUrl()
        );
        $replace = array(
            '{{tm_highlight uenc}}'
        );

        if (defined('Mage_Core_Model_Url::FORM_KEY')) {
            $formKey = Mage::getSingleton('core/session')->getFormKey();
            $search = array_merge($search, array(
                Mage_Core_Model_Url::FORM_KEY . '/' . $formKey,
                'value="' . $formKey . '"'
            ));
            $replace = array_merge($replace, array(
                '{{tm_highlight form_key_url}}',
                '{{tm_highlight form_key_hidden}}'
            ));
        }

        $data = str_replace($search, $replace, $data);
        return parent::_saveCache($data);
    }

    /**
     * EE compatibility
     *
     * @return Mage_Core_Model_App
     */
    public function getMageApp()
    {
        if (method_exists($this, '_getApp')) {
            return $this->_getApp();
        }
        return Mage::app();
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (empty($this->_template)) {
            $this->_template = $this->getCustomTemplate();
        }
        return $this->_template;
    }

    public function getCollection($collection = 'highlight/catalog_product_collection')
    {
        if (null === $this->_productCollection) {
            if (version_compare(Mage::getVersion(), '1.6.0.0') < 0) {
                if (!$collection instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection) {
                    $collection = Mage::getResourceModel($collection);
                }
            } else {
                if (!$collection instanceof Mage_Catalog_Model_Resource_Product_Collection) {
                    $collection = Mage::getResourceModel($collection);
                }
            }
						Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
						if(!Mage::helper('highlight')->showOutStockProduct()) {
							Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
						}
           
            $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

            $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter(Mage::app()->getStore()->getId()); // Mage 1.5.0.1 fix

            if (!$this->getToolbarBlockName()) {
                $this->getToolbarBlock()->setData('_current_limit', $this->getProductsCount());
            }

            $this->applyDefaultPriceBlock();
            $this->applySkuFilter($collection);
            $this->applyPriceFilter($collection);
            $this->applyCategoryFilter($collection);
            $this->applyProductTypeFilter($collection);
            $this->applySortRules($collection);

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if (null === $this->_toolbarBlock) {
            $this->_toolbarBlock = parent::getToolbarBlock();
        }
        return $this->_toolbarBlock;
    }

    /**
     * @return TM_Highlight_Model_Resource_Eav_Mysql4_Catalog_Product_Collection
     */
    public function getLoadedProductCollection()
    {
        return $this->getCollection();
    }

    /**
     * @return TM_Highlight_Model_Resource_Eav_Mysql4_Catalog_Product_Collection
     */
    public function getProductCollection()
    {
        return $this->getCollection();
    }

    protected function _getProductCollection()
    {
        return $this->getCollection();
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        if (!isset($this->_data['products_count'])) {
            $this->_data['products_count'] = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_data['products_count'];
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        if (!isset($this->_data['column_count'])) {
            $this->_data['column_count'] = self::DEFAULT_COLUMN_COUNT;
        }
        return $this->_data['column_count'];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if (!isset($this->_data['title'])) {
            $this->_data['title'] = $this->_title;
        }
        return $this->_data['title'];
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        if (!isset($this->_data['class_name'])) {
            $this->_data['class_name'] = $this->_className;
        }
        return $this->_data['class_name'];
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        if (!isset($this->_data['attribute_code'])) {
            $this->_data['attribute_code'] = $this->_attributeCode;
        }
        return $this->_data['attribute_code'];
    }

    /**
     * @return string
     */
    public function getPriceSuffix()
    {
        if (!isset($this->_data['price_suffix'])) {
            $this->_data['price_suffix'] = $this->_priceSuffix;
        }
        return $this->_data['price_suffix'];
    }

    /**
     * @param string $where The text with a placeholder.
     * @param float $value
     */
    public function addPriceFilter($where = 'special_price >= ?', $value = 0)
    {
        $this->_priceFilter[] = array(
            'where' => $where,
            'value' => $value
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getPriceFilter()
    {
        if (empty($this->_priceFilter) && $this->hasData('price_filter')) {
            $parts = explode(',', $this->getData('price_filter'));
            $this->addPriceFilter('max_price >= ?', (int)$parts[0]);
            if (!empty($parts[1])) {
                $this->addPriceFilter('min_price <= ?', (int)$parts[1]);
            }
        }
        return $this->_priceFilter;
    }

    public function addCategoryFilter($category)
    {
        $this->_categoryFilter[$category] = $category;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategoryFilter()
    {
        if (empty($this->_categoryFilter) && $this->hasData('category_filter')) {
            foreach (explode(',', $this->getData('category_filter')) as $categoryId) {
                $this->_categoryFilter[$categoryId] = $categoryId;
            }
            if (array_key_exists('current', $this->_categoryFilter)) {
                $this->getCurrentCategory();
            }
        }
        return $this->_categoryFilter;
    }

    function getCurrentCategory(){
        if (array_key_exists('current', $this->_categoryFilter)) {
            unset($this->_categoryFilter['current']);
            if ($product = Mage::registry('current_product')) {
                $categories = $product->getCategoryCollection()
                    ->setPage(1, 1)
                    ->addAttributeToSelect('name')
                    ->addFieldToFilter('level', array('gteq' => 4))
                    
                    /*
                    //excluded promotion catalog and all sub
                    //->addFieldToFilter('parent_id',array('neq' => 862))
                    //->addFieldToFilter('entity_id',array('neq' => 862))
                    //only select cat
                    ->addFieldToFilter('path',  array(
                    array('like' => '1/44/289/%'),
                    array('like' => '1/44/398/%'),
                    array('like' => '1/44/290/%'),
                    array('like' => '1/44/798/%'),
                    array('like' => '1/44/942/%'),
                    )
                    )
                    //exclude Princess Pa Foundation (ID: 1065)
                    ->addFieldToFilter('parent_id',array('neq' => 1065))
                    ->addFieldToFilter('entity_id',array('neq' => 1065)) 
                    */

                    ->setOrder('level', 'desc')
                    //->load()
                ;
                foreach ($categories as $cat) {
                    if(isset($_GET['debug'])){
                        var_dump('current_product:'.$cat->getId().':'.$cat->getName().':'.$cat->getLevel().':'.$cat->getPath());
                    }
                    $this->_categoryFilter[] = $cat->getId();
                }
            }
            else if ($category = Mage::registry('current_category')) {
                $this->_categoryFilter[] = $category->getId();
            }
        }
    }

    public function addProductTypeFilter($type)
    {
        $this->_productTypeFilter[$type] = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function getProductTypeFilter()
    {
        if (empty($this->_productTypeFilter) && $this->hasData('product_type_filter')) {
            foreach (explode(',', $this->getData('product_type_filter')) as $type) {
                $this->_productTypeFilter[$type] = $type;
            }
        }
        return $this->_productTypeFilter;
    }

    public function setAddBundlePriceBlock($status)
    {
        $this->_addBundlePriceBlock = $status;
    }

    public function addSortRule($rule)
    {
        $this->_sortRules[] = $rule;
        return $this;
    }

    public function getSortRules()
    {
        if (empty($this->_sortRules) && $this->hasData('order')) {
            foreach (explode(',', $this->getData('order')) as $rule) {
                $this->_sortRules[] = $rule;
            }
        }
        return $this->_sortRules;
    }

    public function applyDefaultPriceBlock()
    {
        if ($this->_addBundlePriceBlock) {
            $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        }
    }

    public function applySkuFilter($collection)
    {
        $filter = $this->getSkuFilter();
        if (!$filter) {
            return;
        }
        if (!is_array($filter)) {
            $filter = explode(',', $filter);
        }
        $filter = array_filter($filter);
        $collection->addFieldToFilter('sku', $filter);
    }

    /**
     * @param TM_Highlight_Model_Resource_Eav_Mysql4_Catalog_Product_Collection $collection
     */
    public function applyPriceFilter($collection)
    {
        foreach ($this->getPriceFilter() as $values) {
            $collection->getSelect()->where($values['where'], $values['value']);
        }
    }

    /**
     * @param TM_Highlight_Model_Resource_Eav_Mysql4_Catalog_Product_Collection $collection
     */
    public function applyCategoryFilter($collection)
    {
        if (count($this->getCategoryFilter())) {
            foreach ($this->getCategoryFilter() as $categoryId) {
                if ($categoryId != 'current') {
                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    if ($category->getId()) {
                        $collection->addCategoryFilter($category);
                    }
                } elseif ($category = Mage::registry('current_category')) {
                    $collection->addCategoryFilter($category);
                }
            }
        }
    }

    /**
     * @param TM_Highlight_Model_Resource_Eav_Mysql4_Catalog_Product_Collection $collection
     */
    public function applyProductTypeFilter($collection)
    {
        $filter = $this->getProductTypeFilter();
        $filter = array_filter($filter);
        if (count($filter)) {
            $collection->addFieldToFilter(
                'type_id',
                array('in' => $filter)
            );
        }
    }

    /**
     * @param TM_Highlight_Model_Resource_Eav_Mysql4_Catalog_Product_Collection $collection
     */
    public function applySortRules($collection)
    {
        $rules = $this->getSortRules();
        if (in_array('RAND()', $rules)) {
            $collection->getSelect()->order('RAND()');
            return;
        }
        foreach ($this->getSortRules() as $rule) {
            $collection->getSelect()->order($rule);
        }
    }

    /**
     * Overriden to include the category path in the products url on the homepage.
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  array  $additional
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        if (!Mage::getStoreConfig('catalog/seo/product_use_categories')) {
            return parent::getProductUrl($product, $additional);
        }
        if (self::$_productUrlModel === null) {
            self::$_productUrlModel = Mage::getSingleton('highlight/product_url');
        }
        return self::$_productUrlModel->getUrl($product, $additional);
    }

    public function beforeToHtml()
    {
        return $this->_beforeToHtml();
    }

    public function getPageTitle()
    {
        $oldTitle = $this->getAllTitle(); // old argento compatibility
        if ($oldTitle) {
            return $oldTitle;
        }
        return $this->getData('page_title');
    }

    public function getPageUrl()
    {
        if (!static::PAGE_TYPE) {
            return false;
        }
        return Mage::getModel('core/url')->getDirectUrl(
            Mage::helper('highlight')->getPageUrlKey(static::PAGE_TYPE)
        );
    }
		
		public function getConfigurableHtmlListBlock($product) {
			$html = '';
			if(Mage::getStoreConfig('amconf/list/enable_list') == 1 && $product->isConfigurable()){
				$html .= '<div class="options-list">';
				$html .= Mage::helper('amconf')->getHtmlBlock($product, '');
				$html .= '</div>';
			}
			return $html;
		}
}
