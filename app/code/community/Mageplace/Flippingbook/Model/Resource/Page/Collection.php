<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Page_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/page');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('page_id', 'page_name');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('page_id', 'page_name');
    }


    public function addMagazineFilter($magazine)
    {
        if ($magazine instanceof Mageplace_Flippingbook_Model_Magazine) {
            $magazine = $magazine->getId();
        } else if ($magazine instanceof Mageplace_Flippingbook_Model_Page) {
            $magazine = $magazine->getMagazineId();
        }

        $magazine = (int)$magazine;

        $select = $this->getSelect()
            ->join(
                array(
                    'magazine_table' => $this->getTable('flippingbook/magazine')
                ),
                'main_table.page_magazine_id = magazine_table.magazine_id',
                array()
            )
            ->where(
                'magazine_table.magazine_id IN (?)',
                array (
                    0,
                    $magazine
                )
            );

        return $this;
    }

    public function addIsActiveFilter()
    {
        return $this->addFilter('main_table.is_active', 1);
    }

    public function setOrderByPosition($direction = parent::SORT_ORDER_ASC)
    {
        return $this->addOrder('main_table.page_sort_order', $direction);
    }

}