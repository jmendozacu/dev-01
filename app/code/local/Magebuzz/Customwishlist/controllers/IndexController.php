<?php
/*
* Copyright (c) 2016 www.magebuzz.com 
*/
require_once(Mage::getModuleDir('controllers','Mage_Wishlist').DS.'IndexController.php');
class Magebuzz_Customwishlist_IndexController extends Mage_Wishlist_IndexController {
	/**
	 * Adding new item
	 *
	 * @return Mage_Core_Controller_Varien_Action|void
	 */
	public function printAction()
	{
		$this->loadLayout('print');
		$this->renderLayout();
	}
	public function shareAction()
	{

		$this->_getWishlist();
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('wishlist/session');
		$this->renderLayout();
	}
	public function checkcartAction()
    {
        if ($this->_isCheckFormKey && !$this->_validateFormKey()) {
            $this->_forward('noRoute');
            return;
        }

        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noRoute');
            return;
        }
        $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $itemIds = array();
        $itemIdsString = $this->getRequest()->getParam('itemids');
        if (isset($itemIdsString)) {
            $itemIds = array_filter(json_decode($itemIdsString), 'strlen');
        }

        $cart       = Mage::getSingleton('checkout/cart');
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter()
                ->addFieldToFilter('wishlist_item_id', array('in' => $itemIds));

        $qtysString = $this->getRequest()->getParam('qty');
        if (isset($qtysString)) {
            $qtys = array_filter(json_decode($qtysString), 'strlen');
        }

        foreach ($collection as $item) {
            /** @var Mage_Wishlist_Model_Item */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }

            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }

                $cartItem = $cart->getQuote()->getItemByProduct($item->getProduct());
                if ($cartItem) {
                    $cart->getQuote()->deleteItem($cartItem);
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
            }
        }

        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
        }
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', join(', ', $products));
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('wishlist/session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                $redirectUrl = $indexUrl;
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            }
            catch (Exception $e) {
                Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                $redirectUrl = $indexUrl;
            }

            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            Mage::getSingleton('checkout/session')->addSuccess(
                Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
            );

            // save cart and collect totals
            $cart->save()->getQuote()->collectTotals();
        }

        Mage::helper('wishlist')->calculate();

        $this->_redirectUrl($redirectUrl);
    }

	protected function _getWishlist()
	{
		$wishlist = Mage::registry('wishlist');
		if ($wishlist) {
			return $wishlist;
		}

		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
			Mage::register('wishlist', $wishlist);
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
		} catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addException($e,
				Mage::helper('wishlist')->__('Cannot create wishlist.')
			);
			return false;
		}

		return $wishlist;
	}

	public function addAction() {
		$response = array();
		$wlitemadded = array();

		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Please Login First');
		}

		if (empty($response)) {
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();

			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Unable to Create Wishlist');
			} else {
				$productId = (int) $this->getRequest()->getParam('product');
				if (!$productId) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Product Not Found');
				} else {
					$product = Mage::getModel('catalog/product')->load($productId);
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['status'] = 'ERROR';
						$response['message'] = $this->__('Cannot specify product.');
					} else {
						try {
							$requestParams = $this->getRequest()->getParams();
							$buyRequest = new Varien_Object($requestParams);

							Mage::helper('wishlist')->calculate();

							if (Mage::helper('customwishlist')->checkItemInWishlist($productId)) {
								$message = $this->__('This product has been added to your wishlist already');
								$response['status'] = 'SUCCESS';
								$response['message'] = $message;
							} else {
								$message = $this->__('%1$s has been added to your wishlist.', $product->getName());
								$response['status'] = 'SUCCESS';
								$response['message'] = $message;
							}

							$result = $wishlist->addNewItem($product, $buyRequest);
							if (is_string($result)) {
								Mage::throwException($result);
							}
							$wishlist->save();
							// Get all wishlist item of current customer
							$collection = Mage::getModel('wishlist/item')->getCollection()->addFieldToFilter('wishlist_id', $wishlist->getId());
							$wlitemadded = $collection->getColumnValues('product_id');							
							Mage::dispatchEvent(
								'wishlist_add_product',
								array(
									'wishlist'  => $wishlist,
									'product'   => $product,
									'item'      => $result
								)
							);
							$response['wlitemadded'] = $wlitemadded;
						}
						catch (Mage_Core_Exception $e) {
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						}
						catch (Exception $e) {
							mage::log($e->getMessage());
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}

		}
		$html = 
		'<div class="block" id="ajaxcart_content_option_product">
			<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
			<div class="ajaxcart-heading">
				<p class="added-success-message">' 		
				. $response['message'] .
				'</p>
			</div>
		</div>
		';
		$response['html'] = $html;
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}

	/**
	 * Add the item to wish list
	 *
	 * @return Mage_Core_Controller_Varien_Action|void
	 */
	protected function _addItemToWishList()
	{
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$wishlist = $this->_getWishlist();
		if (!$wishlist) {
				return $this->norouteAction();
		}

		$session = Mage::getSingleton('core/session');

		$productId = (int)$this->getRequest()->getParam('product');
		if (!$productId) {
				$this->_redirect('*/');
				return;
		}

		$product = Mage::getModel('catalog/product')->load($productId);
		if (!$product->getId() || !$product->isVisibleInCatalog()) {
				$session->addError($this->__('Cannot specify product.'));
				$this->_redirect('*/');
				return;
		}

		try {
				$requestParams = $this->getRequest()->getParams();
				if ($session->getBeforeWishlistRequest()) {
						$requestParams = $session->getBeforeWishlistRequest();
						$session->unsBeforeWishlistRequest();
				}
				$buyRequest = new Varien_Object($requestParams);

				$result = $wishlist->addNewItem($product, $buyRequest);
				if (is_string($result)) {
						Mage::throwException($result);
				}
				$wishlist->save();

				Mage::dispatchEvent(
						'wishlist_add_product',
						array(
								'wishlist' => $wishlist,
								'product' => $product,
								'item' => $result
						)
				);

				$referer = $session->getBeforeWishlistUrl();
				if ($referer) {
						$session->setBeforeWishlistUrl(null);
				} else {
						$referer = $this->_getRefererUrl();
				}

				/**
				 *  Set referer to avoid referring to the compare popup window
				 */
				$session->setAddActionReferer($referer);

				Mage::helper('wishlist')->calculate();

				$message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.',
						$product->getName(), Mage::helper('core')->escapeUrl($referer));
				$session->addSuccess($message);
		} catch (Mage_Core_Exception $e) {
				$session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
		}
		catch (Exception $e) {
				$session->addError($this->__('An error occurred while adding item to wishlist.'));
		}
		$this->_redirectReferer($currentUrl);
	}
  public function removeallAction() {
		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		$itemCollection = Mage::getModel('wishlist/item')->getCollection()->addCustomerIdFilter($customerId);
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$isRemoveAll = false;
		foreach($itemCollection as $item) {
			if (!$item->getId()) {
				return $this->norouteAction();
			}
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
				return $this->norouteAction();
			}
			try {
				$item->delete();
				$wishlist->save();
				$isRemoveAll = true;
			} catch (Mage_Core_Exception $e) {
				$isRemoveAll = false;
			}
		}
		if($isRemoveAll) {
			Mage::getSingleton('core/session')->addSuccess($this->__('The wishlist was cleared.'));
		}else{
			Mage::getSingleton('customer/session')->addError($this->__('An error occurred while deleting all items in your wishlist: %s', $e->getMessage()));
		}
		Mage::helper('wishlist')->calculate();
		$this->_redirectReferer($currentUrl);
  }
	/**
	 * Remove item
	 */
	public function removeAction()
	{
			$response = array();
			$id = (int) $this->getRequest()->getParam('item');
			$item = Mage::getModel('wishlist/item')->load($id);
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			if (!$item->getId()) {
					return $this->norouteAction();
			}
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
					return $this->norouteAction();
			}
			try {
					$item->delete();
					$wishlist->save();
					$response['status'] = 'SUCCESS';
					$response['message'] = 'Product has been removed to your wishlist.';
			} catch (Mage_Core_Exception $e) {
					Mage::getSingleton('core/session')->addError(
							$this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
					);
			} catch (Exception $e) {
					Mage::getSingleton('core/session')->addError(
							$this->__('An error occurred while deleting the item from wishlist.')
					);
			}

			Mage::helper('wishlist')->calculate();

		$html =
			'<div class="block" id="ajaxcart_content_option_product">
			<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
			<div class="ajaxcart-heading">
				<p class="added-success-message">'
			. $response['message'] .
			'</p>
			</div>
		</div>
		';
		$response['html'] = $html;

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	public function sendAction()
	{
		if (!$this->_validateFormKey()) {
			return $this->_redirect('*/*/');
		}

		$wishlist = $this->_getWishlist();
		if (!$wishlist) {
			return $this->norouteAction();
		}
		$pdffile = $this->generatePdf();
		$emails  = explode(',', $this->getRequest()->getPost('emails'));
		$message = nl2br(htmlspecialchars((string) $this->getRequest()->getPost('message')));
		$error   = false;
		if (empty($emails)) {
			$error = $this->__('Email address can\'t be empty.');
		}
		else {
			foreach ($emails as $index => $email) {
				$email = trim($email);
				if (!Zend_Validate::is($email, 'EmailAddress')) {
					$error = $this->__('Please input a valid email address.');
					break;
				}
				$emails[$index] = $email;
			}
		}
		if ($error) {
			Mage::getSingleton('wishlist/session')->addError($error);
			Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
			$this->_redirect('*/*/share');
			return;
		}

		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		try {
			$customer = Mage::getSingleton('customer/session')->getCustomer();

			/*if share rss added rss feed to email template*/
			if ($this->getRequest()->getParam('rss_url')) {
				$rss_url = $this->getLayout()
					->createBlock('wishlist/share_email_rss')
					->setWishlistId($wishlist->getId())
					->toHtml();
				$message .= $rss_url;
			}
			$wishlistBlock = $this->getLayout()->createBlock('wishlist/share_email_items')->toHtml();

			$emails = array_unique($emails);
			/* @var $emailModel Mage_Core_Model_Email_Template */
			$emailModel = Mage::getModel('core/email_template');
			$emailModel->getMail()->createAttachment(
				file_get_contents(Mage::getBaseDir('media').'/wishlistpdf/my_wishlist_'.$wishlist->getId().'.pdf'),
				Zend_Mime::TYPE_OCTETSTREAM,
				Zend_Mime::DISPOSITION_ATTACHMENT,
				Zend_Mime::ENCODING_BASE64,
				'my_wishlist_'.$wishlist->getId().'.pdf'
			);
			$sharingCode = $wishlist->getSharingCode();
				foreach ($emails as $email) {

				$emailModel->sendTransactional(
					Mage::getStoreConfig('wishlist/email/email_template'),
					Mage::getStoreConfig('wishlist/email/email_identity'),
					$email,
					null,
					array(
						'customer'       => $customer,
						'salable'        => $wishlist->isSalable() ? 'yes' : '',
						'items'          => $wishlistBlock,
						'addAllLink'     => Mage::getUrl('*/shared/allcart', array('code' => $sharingCode)),
						'viewOnSiteLink' => Mage::getUrl('*/shared/index', array('code' => $sharingCode)),
						'message'        => $message
					)
				);
			}

			$wishlist->setShared(1);
			$wishlist->save();

			$translate->setTranslateInline(true);

			Mage::dispatchEvent('wishlist_share', array('wishlist' => $wishlist));
			Mage::getSingleton('customer/session')->addSuccess(
				$this->__('Your Wishlist has been shared.')
			);
			$this->_redirect('*/*/share', array('wishlist_id' => $wishlist->getId()));
		}
		catch (Exception $e) {
			$translate->setTranslateInline(true);

			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
			$this->_redirect('*/*/share');
		}
	}
	public function generatePdf(){
		$wishlist = $this->_getWishlist();
		$html = $this->getLayout()->createBlock('wishlist/customer_wishlist_items')->setTemplate('wishlist/printwishlist.phtml')->toHtml();
		$pdf = new TCPDF_TCPDF();
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->AddPage();
		$pdf->SetTitle('My Wishlist');
		$pdf->writeHTML($html,true,false,true,false,'');
		$pdf->lastPage();
		if (!file_exists(Mage::getBaseDir('media').'/wishlistpdf/')) {
			mkdir(Mage::getBaseDir('media').'/wishlistpdf/', 0777, true);
		}
		$path = Mage::getBaseDir('media').'/wishlistpdf/my_wishlist_'.$wishlist->getId().'.pdf';
		$pdf->Output($path,'F');
		return $pdf;
	}
}