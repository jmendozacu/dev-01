<?php
class Magebuzz_Customreview_Helper_Image extends Mage_Core_Helper_Abstract {
	public function resize($imageFile, $width, $height, $keepFrame = TRUE, $ds = '') {
    if (!$imageFile) {
      $imageFile = "default.png";
    }
    $imagePath = Mage::getBaseDir('media') . DS . "review" . DS . $ds . DS . $imageFile;
    $imageResized = Mage::getBaseDir('media') . DS . "review" . DS . "resized" . DS . $width . "x" . $height . "/" . $imageFile; 
    try {
      if (file_exists($imageResized)) {
        $imageUrl = Mage::getBaseUrl('media') . "review/resized/" . $width . "x" . $height . "/" . $imageFile;
      } else {
        try {
          if (!file_exists($imagePath)) {
            $imageUrl = Mage::getBaseUrl('media') . "review/default.png";
          } else {
            $fileImg = new Varien_Image($imagePath);
            $fileImg->keepAspectRatio(TRUE);
            $fileImg->keepFrame($keepFrame);
            $fileImg->keepTransparency(TRUE);
            $fileImg->constrainOnly(FALSE);
            $fileImg->backgroundColor(array(255, 255, 255));
            $fileImg->resize($width, $height);
            $fileImg->save($imageResized, null);


            $imageUrl = Mage::getBaseUrl('media') . "review/resized/" . $width . "x" . $height . "/" . $imageFile;
          }
        } catch (Exception $e) {

        }
      }
    } catch (Exception $e) {

    }
    return $imageUrl;
  }
}