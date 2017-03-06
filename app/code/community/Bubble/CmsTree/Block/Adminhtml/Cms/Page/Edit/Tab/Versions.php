<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsPageVersionsGrid');
        $this->setDefaultSort('version_version_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('cms_page_versions_filter');
        $this->setUseAjax(true);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }

        return $this->getUrl('*/*/versions', $params);
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'cmsPageVersionsGrid';
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Versions');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Versions');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $page = Mage::registry('cms_page');
        $collection = Mage::getModel('bubble_cmstree/cms_page_version')
            ->getCollection()
            ->addFieldToFilter('page_id', $page->getId())
            ->addFieldToFilter('is_draft', 0);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('version_version_id', array(
            'header'            => $this->__('Version Id'),
            'index'             => 'version_id',
            'type'              => 'number',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_username', array(
            'width'             => '100px',
            'header'            => Mage::helper('cms')->__('Username'),
            'index'             => 'username',
            'type'              => 'text',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_title', array(
            'header'            => Mage::helper('cms')->__('Page Title'),
            'index'             => 'title',
            'type'              => 'text',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_root_template', array(
            'header'            => Mage::helper('cms')->__('Layout'),
            'index'             => 'root_template',
            'type'              => 'text',
            'frame_callback'    => array($this, 'decorateLayout'),
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_creation_time', array(
            'header'            => Mage::helper('adminhtml')->__('Created At'),
            'align'             => 'right',
            'index'             => 'creation_time',
            'type'              => 'datetime',
            'width'             => '180px',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_action',
            array(
                'header'            => Mage::helper('adminhtml')->__('Action'),
                'width'             => '50px',
                'type'              => 'action',
                'getter'            => 'getId',
                'align'             => 'center',
                'filter'            => false,
                'sortable'          => false,
                'renderer'          => 'bubble_cmstree/adminhtml_widget_grid_column_renderer_action_version',
                'column_css_class'  => 'v-middle',
                'actions'       => array(
                    array(
                        'caption' => $this->__('Restore'),
                        'url'     => array(
                            'base'   => '*/*/restoreVersion',
                        ),
                        'field'   => 'id',
                        'confirm' => $this->__('This will load this version into current page without saving it. Continue?')
                    ),
                    array(
                        'caption' => $this->__('Preview'),
                        'url'     => array(
                            'base'   => '*/*/previewVersion',
                        ),
                        'field'   => 'id',
                        'target' => '_blank',
                    ),
                    array(
                        'caption' => $this->__('Delete'),
                        'url'     => array(
                            'base'   => '*/*/deleteVersion',
                        ),
                        'field'   => 'id',
                        'confirm' => $this->__('Are you sure?')
                    )
                ),
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * @param string $value
     * @return string
     */
    public function decorateLayout($value)
    {
        $text = $value;
        $options = Mage::getModel('page/source_layout')->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                $text = $option['label'];
            }
        }

        return $text;
    }
}
