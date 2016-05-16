<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_OrdersPro_Helper_Data extends Mage_Core_Helper_Abstract {
    
    const XML_ENABLED = 'mageworx_sales/orderspro/enabled';
    const XML_ENABLE_ORDERS_EDITION = 'mageworx_sales/orderspro/enable_orders_edition';
    
    const XML_ENABLE_INVOICE_ORDERS = 'mageworx_sales/orderspro/enable_invoice_orders';
    const XML_SEND_INVOICE_EMAIL = 'mageworx_sales/orderspro/send_invoice_email';    
    const XML_ENABLE_SHIP_ORDERS = 'mageworx_sales/orderspro/enable_ship_orders';
    const XML_SEND_SHIPMENT_EMAIL = 'mageworx_sales/orderspro/send_shipment_email';
    
    const XML_ENABLE_ARCHIVE_ORDERS = 'mageworx_sales/orderspro/enable_archive_orders';
    const XML_ENABLE_DELETE_ORDERS = 'mageworx_sales/orderspro/enable_delete_orders';
    const XML_HIDE_DELETED_ORDERS_FOR_CUSTOMERS = 'mageworx_sales/orderspro/hide_deleted_orders_for_customers';
    const XML_ENABLE_DELETE_ORDERS_COMPLETLY = 'mageworx_sales/orderspro/enable_delete_orders_completely';
    
    const XML_GRID_COLUMNS = 'mageworx_sales/orderspro/grid_columns';
    const XML_CUSTOMER_GRID_COLUMNS = 'mageworx_sales/orderspro/customer_grid_columns';
    
    
    protected $_contentType = 'application/octet-stream';
    protected $_resourceFile = null;
    protected $_handle = null;
    
    
    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_ENABLED);        
    }
    
    public function isEditEnabled() {
        return Mage::getStoreConfig(self::XML_ENABLE_ORDERS_EDITION);        
    }
    
    public function isShippingPriceEditEnabled() {
        return Mage::getStoreConfig('mageworx_sales/orderspro/enable_shipping_price_edition');
    }    
    
    public function isKeepPurchasePrice() {
        return Mage::getStoreConfig('mageworx_sales/orderspro/keep_purchase_price');        
    }
    
    public function isKeepPurchaseDiscount() {
        return Mage::getStoreConfig('mageworx_sales/orderspro/keep_purchase_discount');        
    }    
    
    public function isEnableInvoiceOrders() {
        return Mage::getStoreConfig(self::XML_ENABLE_INVOICE_ORDERS);
    }
    
    public function isSendInvoiceEmail() {
        return Mage::getStoreConfig(self::XML_SEND_INVOICE_EMAIL);
    }
    
    public function isEnableShipOrders() {
        return Mage::getStoreConfig(self::XML_ENABLE_SHIP_ORDERS);
    }
    
    public function isSendShipmentEmail() {
        return Mage::getStoreConfig(self::XML_SEND_SHIPMENT_EMAIL);
    }
    
    public function isEnableArchiveOrders() {
        return Mage::getStoreConfig(self::XML_ENABLE_ARCHIVE_ORDERS);        
    }
        
    public function isEnableDeleteOrders() {
        return Mage::getStoreConfig(self::XML_ENABLE_DELETE_ORDERS);        
    }
    
    public function isHideDeletedOrdersForCustomers() {
        return Mage::getStoreConfig(self::XML_HIDE_DELETED_ORDERS_FOR_CUSTOMERS);        
    }
    
    public function isEnableDeleteOrdersCompletely() {
        return Mage::getStoreConfig(self::XML_ENABLE_DELETE_ORDERS_COMPLETLY);        
    }    
    
    public function getGridColumns() {
        $listColumns = Mage::getStoreConfig(self::XML_GRID_COLUMNS);
        $listColumns = explode(',', $listColumns);
        return $listColumns;
    }
    
    public function getCustomerGridColumns() {
        $listColumns = Mage::getStoreConfig(self::XML_CUSTOMER_GRID_COLUMNS);
        $listColumns = explode(',', $listColumns);
        return $listColumns;
    }    
        
    public function isShowThumbnails() {
        return Mage::getStoreConfig('mageworx_sales/orderspro/show_thumbnails');        
    }
    
    public function getThumbnailHeight() {
        return Mage::getStoreConfig('mageworx_sales/orderspro/thumbnail_height');        
    }
    
    public function addToOrderGroup($orderIds, $orderGroupId)
    {                        
        foreach ($orderIds as $orderId) {
            if ($orderGroupId!=0) {
                Mage::getModel('orderspro/order_item_group')->load($orderId, 'order_id')
                    ->setOrderId(intval($orderId))->setOrderGroupId($orderGroupId)->save();
            } else {
                Mage::getModel('orderspro/order_item_group')->load($orderId, 'order_id')->delete();
            }
        }
        return count($orderIds);
    }
    
    public function deleteOrderCompletely($orderIds) {        
        foreach ($orderIds as $orderId) {
            $this->deleteOrderCompletelyById($orderId);
        }
        return count($orderIds);
    }
    
    public function deleteOrderCompletelyById($order) {
        $coreResource = Mage::getSingleton('core/resource');
        $write = $coreResource->getConnection('core_write');
        if (is_object($order)) {
            $orderId = $order->getId();
        } else {
            $orderId = intval($order);
            $order = Mage::getModel('sales/order')->load($orderId, 'entity_id');
        }        
        if ($orderId) {
            if ($order->getQuoteId()) {
                $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_quote')."` WHERE `entity_id`=".$order->getQuoteId());
                $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_quote_address')."` WHERE `quote_id`=".$order->getQuoteId());
                $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_quote_item')."` WHERE `quote_id`=".$order->getQuoteId());
                $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_quote_payment')."` WHERE `quote_id`=".$order->getQuoteId());
            }
            $order->delete();        
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_order_grid')."` WHERE `entity_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_order_address')."` WHERE `parent_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_order_item')."` WHERE `order_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_order_payment')."` WHERE `parent_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_order_status_history')."` WHERE `parent_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('orderspro_order_item_group')."` WHERE `order_id`=".$orderId);
            
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_invoice')."` WHERE `order_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_creditmemo')."` WHERE `order_id`=".$orderId);
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_shipment')."` WHERE `order_id`=".$orderId);            
        }
    }
    
    
    
    public function getUploadFilesPath($fileId, $createFolder=false) {
        // 3 byte -> 8 chars
        $fileId = '00000000' . $fileId;
        $fileId = substr($fileId, strlen($fileId)-8, 8);
        $dir = substr($fileId, 0, 5);
        $file = substr($fileId, 5);
        
        $catalog = Mage::getBaseDir('media') . DS . 'orderspro' . DS;
        
        if ($createFolder && !file_exists($catalog)) {
            mkdir($catalog);
            //chmod($catalog, 777);
        }
        
        if ($createFolder && !file_exists($catalog . $dir . DS)) {            
            mkdir($catalog . $dir . DS);
            //chmod($catalog . $dir . DS, 777);
        }
        
        return $catalog . $dir . DS . $file;
    }
    
    public function isUploadFile($fileId) {
        $file = $this->getUploadFilesPath($fileId, false);
        if (file_exists($file)) return $file; else return null;
    }
    
    public function getUploadFilesUrl($fileId, $fileName) {        
        // orderspro/dl/file/id/1/file.png
        return $this->_getUrl('orderspro/dl/').'file/id/'.$fileId.'/'.$fileName;               
    }
    
    
    public function prepareFileSize($size) {
        
        if ($size>=1048576) {
            return round($size/1048576, 2).' '.$this->__('MB');
        } elseif ($size>=1024) {
            return round($size/1024, 2).' '.$this->__('KB');
        } else {
            return $size.' '.$this->__('B');
        }                
    }
    
    
    public function processDownload($resource, $fileName) {
        $this->_resourceFile = $resource;

        $response = Mage::app()->getResponse();
        $response->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $this->getContentType($fileName), true);

        if ($fileSize = $this->_getHandle()->streamStat('size')) {
            $response->setHeader('Content-Length', $fileSize);
        }
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->clearBody();
        $response->sendHeaders();

        $this->output();
    }
    
    protected function _getHandle() {
        if (!$this->_resourceFile) {
            Mage::throwException($this->__('Please set resource file and link type'));
        }
        if (is_null($this->_handle)) {
            $this->_handle = new Varien_Io_File();
            $this->_handle->open(array('path' => Mage::getBaseDir('var')));
            if (!$this->_handle->fileExists($this->_resourceFile, true)) {
                Mage::throwException($this->__('File does not exist'));
            }
            $this->_handle->streamOpen($this->_resourceFile, 'r');
        }
        return $this->_handle;
    }
   

    public function getContentType() {
        $this->_getHandle();
        if (function_exists('mime_content_type')) {
            return mime_content_type($this->_resourceFile);
        } else {
            return $this->getFileType($this->_resourceFile);
        }
        return $this->_contentType;
    }
    
    
    public function getFileType($fileName) {
        $ext = substr($fileName, strrpos($fileName, '.') + 1);
        $type = Mage::getConfig()->getNode('global/mime/types/x' . $ext);
        if ($type) {
            return $type;
        }
        return $this->_contentType;
    }

    public function output() {
        $handle = $this->_getHandle();
        while ($buffer = $handle->streamRead()) {
            print $buffer;
        }
    }
    
    
    
    public function sendOrderUpdateEmail($orders, $notifyCustomer = true, $comment = '', $filePath = null, $fileName = null) {
        $storeId = $orders->getStore()->getId();
        
        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails('sales_email/order_comment/copy_to', $storeId);                        
        $copyMethod = Mage::getStoreConfig('sales_email/order_comment/copy_method', $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }                
        
        // Retrieve corresponding email template id and customer name
        if ($orders->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig('sales_email/order_comment/guest_template', $storeId);
            $customerName = $orders->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig('sales_email/order_comment/template', $storeId);
            $customerName = $orders->getCustomerName();
        }        
        
        $mailer = Mage::getModel('orderspro/core_email_template_mailer');
                
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($orders->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig('sales_email/order_comment/identity', $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'   => $orders,
                'comment' => $comment,
                'billing' => $orders->getBillingAddress()
            )
        );
        $mailer->send($filePath, $fileName);

        return $this;
    }
    
    protected function _getEmails($configPath, $storeId) {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
    
    public function invoiceOrder($orderIds) {                                
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId>0) {
                
                $order = Mage::getModel('sales/order')->load($orderId);
                if (!$order->getId()) continue;
                if (!$order->canInvoice()) continue;
                
                //echo count($order->getItemsCollection());
                $savedQtys = array();
                foreach ($order->getAllItems() as $orderItem) {
                    $savedQtys[$orderItem->getId()] = $orderItem->getQtyToInvoice();
                }
                
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
                if (!$invoice->getTotalQty()) continue;
                //Mage::register('current_invoice', $invoice);                
                
                $invoice->register();
                
                // if send email          
                $sendEmailFlag = $this->isSendInvoiceEmail();
                if ($sendEmailFlag) {
                    $invoice->setEmailSent(true);
                }
                
                $invoice->getOrder()->setCustomerNoteNotify($sendEmailFlag);
                $invoice->getOrder()->setIsInProcess(true);
                
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();                
                // if send email
                $invoice->sendEmail($sendEmailFlag, '');
                $count++;
            }            
        }
        return $count;        
    }
    
    public function shipOrder($orderIds) {                                
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId>0) {
                try {
                    $order = Mage::getModel('sales/order')->load($orderId);
                    if (!$order->getId()) continue;
                    if ($order->getForcedDoShipmentWithInvoice()) continue;                    
                    if (!$order->canShip()) continue;

                    $savedQtys = array();
                    foreach ($order->getAllItems() as $orderItem) {
                        $savedQtys[$orderItem->getId()] = $orderItem->getQtyToShip();
                    }

                    $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);                                                            
                    //Mage::register('current_shipment', $shipment);                
                    if (!$shipment) continue;
                    if (!$shipment->getTotalQty()) continue;
                    
                    $shipment->register();

                    // if send email          
                    $sendEmailFlag = $this->isSendShipmentEmail();
                    if ($sendEmailFlag) {
                        $shipment->setEmailSent(true);
                    }
                    
                    $shipment->getOrder()->setCustomerNoteNotify($sendEmailFlag);                
                    $shipment->getOrder()->setIsInProcess(true);                    
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
                    // if send email
                    $shipment->sendEmail($sendEmailFlag, '');
                    $count++;
                } catch (Exception $e) {}   
            }            
        }
        return $count;        
    }
    
    public function changeStatusOrder($orderIds, $status, $comment = '', $isVisibleOnFront = 1, $isCustomerNotified = false) {                                
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId>0) {
                try {
                    $order = Mage::getModel('sales/order')->load($orderId);
                    if (!$order->getId()) continue;
                    $response = false;                    

                    $order->addStatusHistoryComment($comment, $status)
                        ->setIsVisibleOnFront($isVisibleOnFront)
                        ->setIsCustomerNotified($isCustomerNotified);

                    if ($isCustomerNotified) {
                        $comment = trim(strip_tags($comment));                    
                        $order->sendOrderUpdateEmail($isCustomerNotified, $comment);
                    }    
                    

                    $order->save();
                    $count++;
                } catch (Exception $e) {}   
            }            
        }
        return $count;        
    }
    
    // translate and QuoteEscape
    public function __js($str) {
        return $this->jsQuoteEscape(str_replace("\'", "'", $this->__($str)));
    } 
}