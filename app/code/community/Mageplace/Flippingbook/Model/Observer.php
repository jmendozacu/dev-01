<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Observer
{
    public function processAdminhtmlBlockHtmlBefore($observer)
    {
        $productId = Mage::app()->getRequest()->getParam('id');
        $controller = $observer->getAction();
        if ($productId && $controller instanceof Mage_Adminhtml_Catalog_ProductController){
            $layout = $controller->getLayout();
            $block = $layout->createBlock('core/text');
            $url = Mage::helper("adminhtml")->getUrl('*/flippingbook/attach');
            $block->setText(
                "<script type=\"text/javascript\">
                    //<![CDATA[
                    MageplaceFlippingbook = {};
                    MageplaceFlippingbook.Magazine = Class.create();
                    MageplaceFlippingbook.Magazine.prototype = {
                        productId : {$productId},

                        initialize : function() {},

                        setRelation : function(magazineId, productAttached) {
                            new Ajax.Request('{$url}',
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
                </script>"
            );
            if ($jsBlock = $layout->getBlock('js')) {
                $jsBlock->append($block);
            }
        }

    }

    public function processCoreBlockAbstractToHtmlAfter($observer)
    {
        $nameInLayout = $observer->getBlock()->getNameInLayout();
        if($nameInLayout == 'product.info.media' && Mage::getSingleton('core/layout')->getBlock('product.info.flippingbook')){
            $html = $observer->getTransport()->getHtml();
            $html = $html . Mage::getSingleton('core/layout')->getBlock('product.info.flippingbook')->toHtml();
            $observer->getTransport()->setHtml($html);
        }
    }
}
