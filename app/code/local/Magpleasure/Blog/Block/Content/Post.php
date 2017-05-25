<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Blog
 */

class Magpleasure_Blog_Block_Content_Post extends Magpleasure_Blog_Block_Content_Abstract
{
    protected $_category;
    const CACHE_PREFIX = 'mpblog_post_';

    protected $_cacheParams = array();

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'    => 2600,
            'cache_tags'        => array(
                Magpleasure_Common_Helper_Cache::MAGPLEASURE_CACHE_KEY,
                'CONFIG',
                Magpleasure_Blog_Model_Post::CACHE_TAG."_".$this->getRequest()->getParam("id"),
            ),
            'cache_key'         => $this->getCacheKey(),
        ));

        parent::_construct();

        $this->setTemplate('mpblog/post.phtml');
    }

    public function getCacheKey()
    {
        return self::CACHE_PREFIX.md5(implode($this->_getCacheParams()));
    }

    protected function _getCacheParams()
    {
        $dynamicCommentIds = $this->_helper()->getCommon()->getCookie()->getAllFromCookie($this->_helper()->getDynamicCookieName());

        $params = array(
            Mage::app()->getStore()->getId(),
            $this->getPost()->getId(),
            implode("_", $dynamicCommentIds),
        );

        return  $params;
    }

    /**
     * Post
     * @return Magpleasure_Blog_Model_Post
     */
    public function getPost()
    {
        if (!Mage::registry('current_post')){
            if ($postId = $this->getRequest()->getParam('id')){
                /** @var Magpleasure_Blog_Model_Post $post  */
                $post = Mage::getModel('mpblog/post');
                if (!Mage::app()->isSingleStoreMode()){
                    $post->setStore(Mage::app()->getStore()->getId());
                }
                $post->load($postId);
                Mage::register('current_post', $post, true);
            } else {
                Mage::throwException($this->__("Unknown post id."));
            }
        }
        return Mage::registry('current_post');
    }

    protected function _prepareLayout()
    {
        $this->_title = $this->getPost()->getTitle();
        parent::_prepareLayout();
        return $this;
    }

    public function getMetaTitle()
    {
        return $this->getPost()->getMetaTitle() ? $this->getPost()->getMetaTitle() : $this->_helper()->checkForPrefix($this->getPost()->getTitle());
    }

    public function getDescription()
    {
        return $this->getPost()->getMetaDescription();
    }

    public function getKeywords()
    {
        return $this->getPost()->getMetaTags();
    }

    protected function _prepareBreadcrumbs()
    {
        parent::_prepareBreadcrumbs();
        $page = $this->getRequest()->getParam('p');
        if(is_null($page)){
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbs){
//            $breadcrumbs->addCrumb('blog', array(
//                'label' => $this->_helper()->getMenuLabel(),
//                'title' => $this->_helper()->getMenuLabel(),
//                'link' => $this->_helper()->_url()->getUrl(),
//            ));
                $category = $this->getCategory();
                $category_is_landing_homedecor = $category->getData('category_is_landing');
                $category_is_landing_indexproject = $category->getData('category_is_landing_project');
                $category_style = $this->getCategory()->getData('category_style');
//
                if(!$category_is_landing_homedecor && $category_style == Magpleasure_Blog_Model_Categorystyle::HOME_DECOR){
                    $parentCategorys = Mage::helper('mpblog')->getParentCategory($category_style);
                    if($parentCategorys->getSize()){
                        foreach ($parentCategorys as $parentCategory){
                            $breadcrumbs->addCrumb($parentCategory->getUrlKey(), array(
                              'label' => $parentCategory->getName(),
                              'title' => $parentCategory->getName(),
                              'link' => Mage::getBaseUrl('web').'blog/category/'.$parentCategory->getUrlKey(),
                            ));
                        }
                    }
                }
                if(!$category_is_landing_indexproject && $category_style == Magpleasure_Blog_Model_Categorystyle::INDEX_PROJECT){
                    $parentCategoryIndexprojects = Mage::helper('mpblog')->getParentCategoryIndexproject($category_style);
                    if($parentCategoryIndexprojects->getSize()){
                        foreach ($parentCategoryIndexprojects as $parentCategoryIndexproject){
                            $breadcrumbs->addCrumb($parentCategoryIndexproject->getUrlKey(), array(
                              'label' => $parentCategoryIndexproject->getName(),
                              'title' => $parentCategoryIndexproject->getName(),
                              'link' => Mage::getBaseUrl('web').'blog/category/'.$parentCategoryIndexproject->getUrlKey(),
                            ));
                        }
                    }
                }
                $breadcrumbs->addCrumb($this->getCategory()->getUrlKey(), array(
                  'label' => $this->getCategory()->getName(),
                  'title' => $this->getCategory()->getName(),
                  'link' => Mage::getBaseUrl('web').'blog/category/'.$this->getCategory()->getUrlKey(),
                ));

                $breadcrumbs->addCrumb('post', array(
                  'label' => $this->getTitle(),
                  'title' => $this->getTitle(),
                ));
            }
        }
    }

    public function getCommentsActionHtml()
    {
        return $this->getChildHtml('mpblog_comments_action');
    }

    public function getCommentsHtml()
    {
        return $this->getChildHtml('mpblog_comments_list');
    }

    public function getSocialHtml()
    {
        return $this->getChildHtml('mpblog_social');
    }

    public function getColorClass()
    {
        return $this->_helper()->getIconColorClass();
    }

    public function getShowPrintLink()
    {
        return $this->_helper()->getShowPrintLink();
    }

    public function hasThumbnailUrl()
    {
        return !!$this->getPost()->getThumbnailUrl();
    }

    public function getThumbnailUrl()
    {
        $url = $this->getPost()->getThumbnailUrl();

        $processor = Mage::getModel('cms/template_filter');
        $url = $processor->filter($url);

        return $url;
    }

    public function getCategory()
    {
        if (!$this->_category){
            /** @var Magpleasure_Blog_Model_Category $category  */
            $categoryUrl = Mage::helper('core/http')->getHttpReferer();
            $categoryUrlKey = Mage::helper('mpblog')->getCategoryName($categoryUrl, 'category/', '.html');
            $categorys = Mage::getModel('mpblog/category')->getCollection()
              ->addFieldToFilter('url_key',$categoryUrlKey);
            foreach ($categorys as $category) {
                $categoryId = $category->getData('category_id');
            }
            $category = Mage::getModel('mpblog/category')->load($categoryId);
            $this->_category = $category;
        }
        return $this->_category;
    }
}