<?php
require_once(Mage::getModuleDir('controllers','Mage_Review').DS.'ProductController.php');

class Magebuzz_Customreview_ProductController extends Mage_Review_ProductController
{
		public function autouploadAction(){
			try {
				$file = $_FILES['attachment'];
				$image_name = $_FILES['attachment']['name'];    
				$uploader = new Varien_File_Uploader('attachment');
				$uploader->setAllowedExtensions(array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP"));
				$uploader->setAllowRenameFiles(true);                    
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS . 'review';
				if (!is_dir($path)) {
					mkdir($path, 0777, true);
				}        
				$rs = $uploader->save($path, $_FILES['attachment']['name']);        
				$new_image_name = $uploader->getUploadedFileName();            

				$location = $path. DS . $new_image_name;
				//check if valid image size
				$imageObj = new Varien_Image($location);

				// resize for image

				$resizeWidth = '150';
				$resizeHeight = '150';     
				
				$helper = Mage::helper('customreview/image') ;
				$urlResize = $helper->resize($new_image_name,$resizeWidth,$resizeHeight,null);

				//end      
				$result = array(
				'location' => $location,
				'imageUrl' => $urlResize,
				'imageName' => $new_image_name
				);  
			}
			catch (Exception $e) {
				$result = array(
				'error' => true,
				'message' => $e->getMessage()
				);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
    public function postAction() {
			if (!$this->_validateFormKey()) {
				// returns to the product item page
				$this->_redirectReferer();
				return;
			}

			if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
				$rating = array();
				if (isset($data['ratings']) && is_array($data['ratings'])) {
					$rating = $data['ratings'];
				}
			} else {
				$data   = $this->getRequest()->getParams();
				$rating = $this->getRequest()->getParam('ratings', array());
			}

			if (($product = $this->_initProduct()) && !empty($data)) {
				$session    = Mage::getSingleton('core/session');
				$review     = Mage::getModel('review/review')->setData($data);
				$validate = $review->validate();

				if ($validate === true) {
					try {
						$review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
							->setEntityPkValue($product->getId())
							->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
							->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
							->setStoreId(Mage::app()->getStore()->getId())
							->setStores(array(Mage::app()->getStore()->getId()))
							->save();

						foreach ($rating as $ratingId => $optionId) {
							Mage::getModel('rating/rating')
								->setRatingId($ratingId)
								->setReviewId($review->getId())
								->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
								->addOptionVote($optionId, $product->getId());
						}

						$review->aggregate();
						$reviewId = $review->getId();
						Mage::getModel('review/review')->load($reviewId)
							->setImg($data['image-name'])->save();

						$result['success'] = true;
						$result['message'] = $this->__('Your review has been accepted for moderation.');
					}
					catch (Exception $e) {
						$session->setFormData($data);
						$result['success'] = false;
						$result['error'] = true;
						$result['message'] = $this->__('Unable to post the review.');
					}
				}
				else {
					$session->setFormData($data);
					$result['success'] = false;
					$result['error'] = true;
					$result['message'] = $this->__('Unable to post the review.');
				}
			}
			$html = 
			'<div class="block" id="ajaxcart_content_option_product">
				<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
				<div class="ajaxcart-heading">';
			if ($result['success']) {
				$html .= '<p class="added-success-message">';
			}
			else {
				$html .= '<p class="added-error-message">';
			}
			$html .= $result['message'] .
					'</p>
				</div>
			</div>
			';
			$result['html'] = $html;
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
    }
}