<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mgf_KSmartpay
 * @author	   cherdchai Hinjumpa , bugcherd@gmail.com   089-003-5240
 * @website   http://magentothai.wordpress.com/
 */


class Mgf_KSmartpay_Block_Info_KSmartpay extends Mage_Payment_Block_Info
{
    /**
     * Init default template for block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('KSmartpay/info/KSmartpay.phtml');
    }

    public function getPaymenttitle(){
        return Mage::getStoreConfig('payment/KSmartpay/title');
    }

    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getKSmartpayTypeName()
    {
        $types = Mage::getSingleton('KSmartpay/config')->getKSmartpayTypes();
        if (isset($types[$this->getInfo()->getKSmartpayType()])) {
            return $types[$this->getInfo()->getKSmartpayType()];
        }
        return $this->getInfo()->getKSmartpayType();
    }
}
 ?>