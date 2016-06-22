<?php

class MarginFrame_Sync_Helper_Data extends Mage_Core_Helper_Abstract
{
	
    /*
    public function sendEmail($subject, $body, $toEmailList = '', $from_name = '')
    {
        if(empty($toEmailList)) return false;

        $message = array (
            'subject'       => $subject,
            'html'          => $body,
            'from_email'    => 'no-reply@cdiscount.co.th',
            'from_name'     => 'Sync Monitor',
            'to_email'      => array_values($toEmailList),
            'to_name'       => array_values($toEmailList),
        );

        if(!empty($from_name)) {
            $message['from_name']  = $from_name;
        }

        try {
            $mandrill = Mage::helper('mandrill')->api();
            $mandrill->setApiKey(Mage::helper('mandrill')->getApiKey());
            $mandrill->sendEmail($message);
            if ($mandrill->errorCode) {
                echo "sendEmail() error: \$mandrill->errorCode = [{$mandrill->errorCode}]\n";
            }
        } catch (Exception $e) {
            echo "sendEmail() error: [{$e->getMessage()}]\n";
        }
    }
    */

    public function logSync($check='', $sync_type='', $message=array(), $filenamecsv='')
    {
        if($filenamecsv != ''){
            $resource = Mage::getSingleton('core/resource');
            // $readConnection = $resource->getConnection('core_read');
            $writeConnection = $resource->getConnection('core_write');

            //
            if(!$check){
                //success
                 $query = "
                        INSERT INTO `tbl_sync_log` (sync_type, created_at, log,message,filename)
                        VALUES ('".$sync_type."', NOW(), 'Success','','".$filenamecsv."')
                    ";
            } else {
                //fail 
                $query = "
                        INSERT INTO `tbl_sync_log` (sync_type, created_at, log,message,filename)
                        VALUES ('".$sync_type."', NOW(), 'Fail','".json_encode($message)."','".$filenamecsv."')
                    ";
            }
            $writeConnection->query($query);
        }
    }
	
    public function reindex(){
        $indexCollection = Mage::getModel('index/process')->getCollection();
        $arr = array();
        foreach ($indexCollection as $index) {
            // $arr[$value->getData('process_id')] = $value->getData('indexer_code');
            // Array
            // (
            //     [1] => catalog_product_attribute
            //     [2] => catalog_product_price
            //     [3] => catalog_url
            //     [4] => catalog_product_flat
            //     [5] => catalog_category_flat
            //     [6] => catalog_category_product
            //     [7] => catalogsearch_fulltext
            //     [8] => cataloginventory_stock
            //     [9] => tag_summary
            //     [11] => magpleasure_searchcore
            //     [17] => super
            //     [18] => products_attributes_for_orders
            //     [20] => amsorting_summary
            //     [21] => amsegemnts_indexer
            // )
            $index->reindexAll();
        }
        return $arr;
    }
}

?>