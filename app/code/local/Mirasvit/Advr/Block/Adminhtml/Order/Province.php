<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.13
 * @build     657
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Block_Adminhtml_Order_Province extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
  public function _prepareLayout()
  {
    parent::_prepareLayout();

    $this->setHeaderText(Mage::helper('advr')->__('Sales By Province'));

    return $this;
  }

  protected function prepareChart()
  {
// Can choose chart type pie or column
//    $this->setChartType('pie');
//
//    $this->initChart()
//      ->setNameField('region')
//      ->setValueField('sum_grand_total');

    $this->setChartType('column');

    $this->initChart()
      ->setXAxisType('sum_grand_total')
      ->setXAxisField('region');
    return $this;
  }

  protected function prepareGrid()
  {
    $this->initGrid()
      ->setDefaultSort('sum_grand_total')
      ->setDefaultDir('desc')
      ->setDefaultLimit(200)
      ->setPagerVisibility(true);

    return $this;
  }

  public function _prepareCollection()
  {
    $collection = Mage::getModel('advr/report_sales')
      ->setBaseTable('sales/order')
      ->setFilterData($this->getFilterData())
//      ->selectColumns('sales_order_address_table.region')
      ->selectColumns($this->getVisibleColumns())
      ->groupByColumn('region');
    return $collection;
  }

  public function getColumns()
  {
    $optionRegion = Mage::helper('advr')->getAllRegion();

    $columns = array(
      'region' => array(
        'header' => Mage::helper('advr')->__('Province'),
        'type' => 'options',
        'options' => $optionRegion,
        'column_css_class' => 'nobr',
        'filter_index' => 'sales_order_address_table.region_id',
      ),
    );

    $columns += $this->getOrderTableColumns(true);

    return $columns;
  }
}
