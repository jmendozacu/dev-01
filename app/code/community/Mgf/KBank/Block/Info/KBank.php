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
 * @package    Mgf_KBank
 * @author	   cherdchai Hinjumpa , bugcherd@gmail.com   089-003-5240
 * @website   http://magentothai.wordpress.com/
 */


class Mgf_KBank_Block_Info_KBank extends Mage_Payment_Block_Info
{
    /**
     * Init default template for block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('KBank/info/KBank.phtml');
    }

    public function getPaymenttitle(){
        return Mage::getStoreConfig('payment/KBank/title');
    }

    public function getPlanText(){
        
        return Mage::getStoreConfig('payment/KBank/infoimage');
    }

    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getKBankTypeName()
    {
        $types = Mage::getSingleton('KBank/config')->getKBankTypes();
        if (isset($types[$this->getInfo()->getKBankType()])) {
            return $types[$this->getInfo()->getKBankType()];
        }
        return $this->getInfo()->getKBankType();
    }
}
 ?>