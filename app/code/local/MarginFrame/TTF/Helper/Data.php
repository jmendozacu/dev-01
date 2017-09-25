<?php

class MarginFrame_TTF_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	public function getPoVendorType($po_id){
		$qurey = "select 
                    CASE WHEN p.udropship_vendor = 1 THEN 'STD'
                    WHEN oi.product_type = 'packprice' or oi.product_type = 'voucher' THEN 'VOUCHER'
                    ELSE 'MKP' END po_vendor_type
                    from udropship_po p 
                    join udropship_po_item pi on p.entity_id = pi.parent_id
                    join sales_flat_order_item oi on oi.item_id = pi.order_item_id
                    where p.entity_id = {$po_id}
                    limit 1;";
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $po_type = $read->fetchOne($qurey);
        return $po_type;
	}

	public function getAccStatusMappingByPo($po_id, $po_status){
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$query = "select a.*
			from udropship_po p 
			join sales_flat_order o on p.order_id = o.entity_id 
			join acc_status_mapping a on 
			a.state_type = (CASE WHEN o.payment_model = 'COD' THEN 'COD' WHEN p.udropship_vendor = 1 THEN 'PBS' ELSE 'MKP' END)
			where p.entity_id = $po_id and a.po_status = $po_status limit 1;";
		$res = $read->fetchAll($query);
		if($res){
			return $res[0];
		}
        return false;
	}

	public function removeBOM($str=""){
		//if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
		//	$str=substr($str, 3);
		//}
		$bom = pack("CCC", 0xef, 0xbb, 0xbf);
		if (0 === strncmp($str, $bom, 3)) {
			//echo "BOM detected - file is UTF-8\n";
			$str = substr($str, 3);
		}
		return $str;
	}
		

    public function createCreditmemoFromPo($po, $qtys = array(), $shipping_amount = null, $urma_id = null, $reason_code = 0){

		try {
			
			if(!$po->getId()){
				return false;
			}
			$invoice = Mage::getModel('sales/order_invoice')
				->getCollection()
				->addAttributeToFilter('udpo_id', $po->getId())
				->getFirstItem();
			;
			if(!$invoice->getId()){
				return false;
			}

			$order = $po->getOrder();
		
			$qtys_cn = array();
			$all_qty_invoiced = 0;
			$all_qty_refunded = 0;
			foreach ($order->getAllItems() as $item) {
	            if($item['udropship_vendor'] == $po['udropship_vendor']){
		            $oitem_id = $item['item_id'];
		            $qty_invoiced = (double)$item['qty_invoiced'] ? $item['qty_invoiced'] : 0;
		            $qty_refunded = (double)$item['qty_refunded'] ? $item['qty_refunded'] : 0;
		            $qty_left = $qty_invoiced - $qty_refunded;
		            $all_qty_invoiced += $qty_invoiced;
		            $all_qty_refunded += $qty_refunded;
		            if(empty($qtys)){
		            	if ($qty_left > 0) {
			                $qtys_cn[$oitem_id] = $qty_left;
			            }
		            }
		            elseif(isset($qtys[$oitem_id])){
		            	if ($qty_left > 0 && $qty_left >= $qtys[$oitem_id]) {
		            		$qtys_cn[$oitem_id] = $qtys[$oitem_id];
		            	}
		            }

		           	//IT-1932
			        if(isset($qtys_cn[$oitem_id])){
			        	$all_qty_refunded += $qtys_cn[$oitem_id];
			        }
	            }
	         
	            // if (isset($itemData['back_to_stock'])) {
	            //     $backToStock[$orderItemId] = true;
	            // }
	        }

	        if(!empty($qtys_cn)){
	        	$data['qtys'] = $qtys_cn;
		        
		        //IT-1932
	        	if($all_qty_invoiced - $all_qty_refunded == 0){
	        		$data['shipping_amount'] = $po->getBaseShippingAmountIncl();
	        	}
	        	else {
	        		$data['shipping_amount'] = 0;
	        	}
		        
		        //$data['do_offline'] = 1;
                //$data['comment_text'] = '';
                $data['adjustment_positive'] = 0;
                $data['adjustment_negative'] = 0;

                $service = Mage::getModel('sales/service_order', $order);

			    $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
				$creditmemo->setData('udpo_id', $po->getId());
				if($urma_id){
					$creditmemo->setData('urma_id', $urma_id);
				}
				$creditmemo->setData('reason_code', $reason_code);
				//$creditmemo->setRefundRequested(true);
				//$creditmemo->setOfflineRequested(false); // request to refund online

				$creditmemo->register();
				//$creditmemo->refund();
				
				Mage::getModel('core/resource_transaction')
				     ->addObject($creditmemo)
				     ->addObject($creditmemo->getOrder())
				     ->addObject($creditmemo->getInvoice())
				->save();

				if(empty($urma_id) && $po->getData('po_returntype') == 'cancel_before_paid'){
					$creditmemo->setState(8)->save();
				}

				$po->addComment("created creditmemo # " .$creditmemo->getIncrementId()." for invoice # ".$invoice->getIncrementId())
				->setOrigData('udropship_status', $po->getData('udropship_status'))
				->save();
				
				return $creditmemo->getIncrementId();
	        }
	        else{
	        	//throw new Exception('No Item for CN');
	        }

        }
        catch (Exception $e) {
            //throw $e;
            //var_dump($e->getMessage());
        }

        return false;

	}


	public function createAttribute($code, $label, $attribute_type, $product_type)
	{
		$_attribute_data = array(
				'attribute_code' => 'gold_'.(($product_type) ? $product_type : 'joint').'_'.$code,
				'is_global' => '1',
				'frontend_input' => $attribute_type, //'boolean',
				'default_value_text' => '',
				'default_value_yesno' => '0',
				'default_value_date' => '',
				'default_value_textarea' => '',
				'is_unique' => '0',
				'is_required' => '0',
				'apply_to' => array($product_type), //array('grouped')
				'is_configurable' => '0',
				'is_searchable' => '0',
				'is_visible_in_advanced_search' => '0',
				'is_comparable' => '0',
				'is_used_for_price_rules' => '0',
				'is_wysiwyg_enabled' => '0',
				'is_html_allowed_on_front' => '1',
				'is_visible_on_front' => '0',
				'used_in_product_listing' => '0',
				'used_for_sort_by' => '0',
				'frontend_label' => array('Gold Site Attribute '.(($product_type) ? $product_type : 'joint').' '.$label)
		);
		$model = Mage::getModel('catalog/resource_eav_attribute');
		if (!isset($_attribute_data['is_configurable'])) {
			$_attribute_data['is_configurable'] = 0;
		}
		if (!isset($_attribute_data['is_filterable'])) {
			$_attribute_data['is_filterable'] = 0;
		}
		if (!isset($_attribute_data['is_filterable_in_search'])) {
			$_attribute_data['is_filterable_in_search'] = 0;
		}
		if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
			$_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
		}
		$defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
		if ($defaultValueField) {
			//$_attribute_data['default_value'] = $this->getRequest()->getParam($defaultValueField);
		}
		$model->addData($_attribute_data);
		$model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
		$model->setIsUserDefined(1);
		try {
			$model->save();
		} catch (Exception $e) { echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; }
	}

    /**
     * Send Log to Amazon RDS.
     *
     * @param $actionId
     * @param $ref1
     * @param $ref2
     * @return bool
     */
    public function sendLogToRds($actionId, $ref1 = null, $ref2 = null)
    {
        $host1      = "cdiscount-marketplace.caeqrwiydi5t.ap-southeast-1.rds.amazonaws.com";
        $user1      = "log_user";
        $passwd1    = "8asbib4rdGB";

        $con1       = mysql_connect($host1, $user1, $passwd1, true);
        $db1        = mysql_select_db("cdt_log");
        mysql_query("SET time_zone = 'Asia/Bangkok';", $con1);

        $actionId = (int)$actionId;

        $ref2 = "[ ". getmypid() ." ] => " . $ref2;

        $strSQL = "INSERT INTO action_log SET action_id=".$actionId.", ref1='".$ref1."', ref2='".$ref2."', create_timestamp=NOW()";
        $res1   = mysql_query($strSQL, $con1);

        mysql_close($con1);

        return true;
    }

    public function sendEmail($subject, $body, $toEmailList = '', $from_name = '')
    {
        if(empty($toEmailList)) return false;

        $message = array (
            'subject'       => $subject,
            'html'          => $body,
            'from_email'    => 'no-reply@cdiscount.co.th',
            'from_name'     => 'Cdiscount Thailand (Monitor)',
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
	
}

?>