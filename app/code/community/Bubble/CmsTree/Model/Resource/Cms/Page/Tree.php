<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Resource_Cms_Page_Tree extends Varien_Data_Tree_Dbp
{
    /**
     * @var Bubble_CmsTree_Model_Resource_Cms_Page_Collection
     */
    protected $_collection;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * Initialization
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('write'),
            $resource->getTableName('bubble_cms_page_tree'),
            array(
                Varien_Data_Tree_Dbp::ID_FIELD       => 'page_id',
                Varien_Data_Tree_Dbp::PATH_FIELD     => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD    => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD    => 'level',
            )
        );
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int) $storeId;
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->_storeId = Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * @param null|Bubble_CmsTree_Model_Resource_Cms_Page_Collection $collection
     * @param bool|false $sorted
     * @param array $exclude
     * @param bool|true $toLoad
     * @param bool|false $onlyActive
     * @return $this
     */
    public function addCollectionData($collection = null, $sorted = false, $exclude = array(), $toLoad = true, $onlyActive = false)
    {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }

        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }

        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('page_id', array('nin' => $disabledIds));
            }
        }

        if ($toLoad) {
            $collection->load();
            foreach ($collection as $page) {
                if ($this->getNodeById($page->getId())) {
                    $this->getNodeById($page->getId())
                        ->addData($page->getData());
                }
            }

            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }

        return $this;
    }

    /**
     * @param int $id
     * @return bool
     */
    protected function _getItemIsActive($id)
    {
        return false;
    }

    /**
     * @param bool|false $sorted
     * @return Bubble_CmsTree_Model_Resource_Cms_Page_Collection
     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }

        return $this->_collection;
    }

    /**
     * @param Bubble_CmsTree_Model_Resource_Cms_Page_Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;

        return $this;
    }

    /**
     * @param bool|false $sorted
     * @return Bubble_CmsTree_Model_Resource_Cms_Page_Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        /* @var Bubble_CmsTree_Model_Resource_Cms_Page_Collection $collection */
        $collection = Mage::getModel('cms/page')->getCollection();

        if ($sorted) {
            if (is_string($sorted)) {
                // $sorted is supposed to be attribute name
                $collection->addAttributeToSort($sorted);
            } else {
                $collection->addAttributeToSort('name');
            }
        }

        return $collection;
    }

    /**
     * @param string $name
     * @param bool|false $value
     * @param string $id
     * @return string
     */
    public function toSelectHtml($name = '', $value = false, $id = '')
    {
        $html = '<select name="' . $name . '"' . ($id ? ' id="' . $id . '"' : '')  . '>';
        $html .= '<option value="">' . Mage::helper('cms')->__('Select Page...') . '</option>';
        $currentStoreId = null;
        foreach ($this->getNodes() as $node) {
            if ($node->getStoreId() != $currentStoreId) {
                $store = Mage::app()->getStore($node->getStoreId());
                $html .= '<optgroup label="' . $store->getWebsite()->getName() . '"></optgroup>';
                $html .= '<optgroup label="&nbsp;&nbsp;&nbsp;'. $store->getName() . '">';
                $currentStoreId = $node->getStoreId();
                if (null !== $currentStoreId) {
                    $html .= '</optgroup>';
                }
            }
            $selected = ($node->getPageId() == $value) ? 'selected="selected"' : '';
            $html .= '<option value="'. $node->getPageId() . '" ' . $selected . '>' .
                str_repeat('&nbsp;&nbsp;&nbsp;', $node->getLevel() + 1) . $node->getTitle() . '</option>';
        }
        $html .= '</select>';

        return $html;
    }
}