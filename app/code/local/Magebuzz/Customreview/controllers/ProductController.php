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
    public function postAction()
    {
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
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }

        if (($product = $this->_initProduct()) && !empty($data)) {
            $session    = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $review     = Mage::getModel('review/review')->setData($data);
            /* @var $review Mage_Review_Model_Review */

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
                    $session->addSuccess($this->__('Your review has been accepted for moderation.'));
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
            else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                }
                else {
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
        }
        $fileName = '';
        if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {
            try {
                $fileName       = $_FILES['attachment']['name'];
                $fileName   = rtrim($fileName);

                $uploader       = new Varien_File_Uploader('attachment');
                $uploader->setAllowedExtensions(array('jpg', 'png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('media') . DS . 'review';

                $imagename =  $_FILES['attachment']['name'];
                $imagename = $uploader->getNewFileName($fileName);

                if(!is_dir($path)){
                    mkdir($path, 0777, true);
                }
                $uploader->save($path . DS, $fileName );

                //Save Image
                $reviewId = $review->getId();
                $collection = Mage::getModel('review/review')->load($reviewId)
                    ->setImg($imagename)->save();

            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
                $error = true;
            }
        }

        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }
}