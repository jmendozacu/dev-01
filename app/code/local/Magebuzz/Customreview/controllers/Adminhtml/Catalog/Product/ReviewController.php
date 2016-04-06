<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 4/6/2016
 * Time: 4:43 PM
 */
require_once "Mage/Adminhtml/controllers/Catalog/Product/ReviewController.php";
class Magebuzz_Customreview_Adminhtml_Catalog_Product_ReviewController extends Mage_Adminhtml_Catalog_Product_ReviewController{
    public function exportCsvAction()
    {
        $fileName   = 'customer_review.csv';
        $grid       = $this->getLayout()->createBlock('customreview/adminhtml_review_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'customer_review.xml';
        $grid       = $this->getLayout()->createBlock('customreview/adminhtml_review_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}