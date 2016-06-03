<?php
class MarginFrame_Sync_Model_Cron_Productmaster extends Mage_Core_Model_Abstract
{
	public function Run() {
		
		$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'product_master'.DS;
		$dirprepare = $dir.'prepare'.DS;
		$filelogName = "mgfsync_productmaster.log";
		
		// Create folder
		if (!file_exists($dir)) {
			$file = new Varien_Io_File();
			$file->mkdir($dir);
		}
		//create prepare for fast bluk import
		if (!file_exists($dirprepare)) {

			$file_prepare = new Varien_Io_File();
			$file_prepare->mkdir($dirprepare);
		} 

		$dh  = opendir($dir);

		$map_attribute = array(
			'Article_CODE' 			=> 'sku',
			'﻿LGROUP' 				=> 'lgroup',
			'ECOMMERCE' 			=> 'ecommerce',
			'Installation' 			=> 'installtion',
			'MC-SAP1' 				=> array('multiselect'=> array('categories'=>'lv1')),
			'MC-SAP2' 				=> array('multiselect'=> array('categories'=>'lv2')),
			'MC-SAP3' 				=> array('multiselect'=> array('categories'=>'lv3')),
			'SERIES' 				=> 'series',
			'NAME_TH' 				=> 'name',
			'NAME_EN' 				=> 'name',
			'DESC_TH' 				=> 'description',
			'DESC_EN' 				=> 'description',
			'MAIN_MATERIAL_TH' 		=> 'material',
			'MAIN_MATERIAL_EN' 		=> 'material',
			'MATERIAL_TH' 			=> 'material_description',
			'MATERIAL_EN' 			=> 'material_description',
			'COLOR_TH' 				=> 'color',
			'COLOR_EN' 				=> 'color',
			'SIZE' 					=> 'size',
			'KEYFEATURE_TH' 		=> 'key_feature',
			'KEYFEATURE_EN' 		=> 'key_feature',
			'GOODKNOW_TH' 			=> 'good_to_know',
			'GOODKNOW_EN' 			=> 'good_to_know',
			'INSTRUCTION_TH' 		=> 'care_instruction',
			'INSTRUCTION_EN' 		=> 'care_instruction',
			'PRICE' 				=> 'price',
			'ItalianVeneer' 		=> 'italianveneer',
			'JoyPrice' 				=> 'price_label',//joyprice
			'DontMiss' 				=> 'price_label',//dontmiss
			'GermanMelamine' 		=> array('multiselect'=> array('selling_point'=>'germanmelamine')),
			'E1' 					=> array('multiselect'=> array('selling_point'=>'eyes')),
			'SuperFlex' 			=> array('multiselect'=> array('selling_point'=>'superflex')),
			'Crystallized' 			=> array('multiselect'=> array('selling_point'=>'crystallized')),
			'Warranty25' 			=> array('multiselect'=> array('selling_point'=>'warranty25')),
			'PianoHi' 				=> array('multiselect'=> array('selling_point'=>'pianohi')),
			'V-Series'				=> array('multiselect'=> array('selling_point'=>'v-series')),
			'CowLeather' 			=> array('multiselect'=> array('selling_point'=>'cowleather')),
			'HiQuanlity' 			=> array('multiselect'=> array('selling_point'=>'hiquanlity')),
			'TemperedGlass' 		=> array('multiselect'=> array('selling_point'=>'temperedglass')),
			'SafetyGlassw' 			=> array('multiselect'=> array('selling_point'=>'safetyglass')),
			'PICTURE'				=> 'image',
			
		);
		$multiselect = array(
			'GermanMelamine' 		=> 'germanmelamine',
			'E1' 					=> 'eyes',
			'SuperFlex' 			=> 'superflex',
			'Crystallized' 			=> 'crystallized',
			'Warranty25' 			=> 'warranty25',
			'PianoHi' 				=> 'pianohi',
			'ItalianVeneer' 		=> 'v-series',
			'CowLeather' 			=> 'cowleather',
			'HiQuanlity' 			=> 'hiquanlity',
			'TemperedGlass' 		=> 'temperedglass',
			'SafetyGlassw' 			=> 'safetyglass',
		);

		$product = Mage::getModel('catalog/product');
		$collections = Mage::getModel('mgfsync/catcode')->getCollection();
		$catcodes = array();
		foreach ($collections->getData() as $catcode) {
			$catcodes[$catcode['catalog_code']] = $catcode['entity_id'];
		}
		
		while (false !== ($filename = readdir($dh))) {
		    $files[] = $filename;

		    // find .trg file
		    if(strrpos($filename, '.trg')) {

		    	// rename file .trg to .csv
		    	$filenamecsv = str_replace('.trg', '.txt', $filename);
				$rowNum = 0;

				$data = file_get_contents($dir.$filenamecsv);

				$data = iconv('UTF-16LE', 'UTF-8', $data);
				//$data = iconv('ASCII//TRANSLIT', 'UTF-8', $data);
				$data = preg_replace('~\R~u', "\r\n", $data);
				$data = explode("\r\n", $data);
				$col = array();
				$header = array();
				$index_header = array();
				// $index_magento = array();
				$sku_index = 0; 

				$import_TH = array();
				$import_EN = array();
				$indexTH = array();
				$indexEN = array();
				echo $filenamecsv;
				foreach ($data as $row) {		
					if($rowNum ==0 ){
						//first row is attribute name
						$header = explode("|", $row);
						$i = 0;
						foreach ($header as $key) {
							$index_header[$key] = $i;
							if(is_array($map_attribute[$key])){
								foreach ($map_attribute[$key] as $h => $v) {
									$index_magento[$i] = $h;
								}
							} else {
								$index_magento[$i] = $map_attribute[$key];	
							}
							
							if(preg_match( "/_TH$/i", $key, $matches)){
								$import_TH[] = $key;
							} else {
								$import_EN[] = $key;
								if($key == "Article_CODE"){
									$import_TH[] = $key;
								}
							}
							$i++;
						}

						//create header file for put content with type csv
						
						$file = fopen($dirprepare."Import_Produce_TH.csv","w+");
						$str = "";
						foreach ($import_TH as $value) {
							if($index_magento[$index_header[$value]]=='multiselect'){
								foreach ($map_attribute[$value]['multiselect'] as $k => $v) {
									if(in_array($k, $indexTH)){
										continue;
									} else {
										$indexTH[] = $k;
									}
								}
							} else {
								$indexTH[] = $index_magento[$index_header[$value]];	
							}
						}
						
						// array_unshift($indexTH,'sku');
						// $str = rtrim($str,',');
						fputcsv($file,$indexTH);
						fclose($file);

						$file = fopen($dirprepare."Import_Produce_EN.csv","w+");
						$str = "";
						foreach ($import_EN as $value) {
							if($index_magento[$index_header[$value]]=='multiselect'){
								foreach ($map_attribute[$value]['multiselect'] as $k => $v) {
									if(in_array($k, $indexEN)){
										continue;
									} else {
										$indexEN[] = $k;
									}
								}
							} else {
								$indexEN[] = $index_magento[$index_header[$value]];	
							}
						}
						
						// $str = rtrim($str,',');
    					fputcsv($file,$indexEN);
						fclose($file);
						//echo '<pre>';
						// print_r($index_header);
						// echo '</pre>';
						
					} else {
						$cols = explode("|", $row);
						if($cols[0]==''){
							continue;
						}
						// echo '<pre>';
						// print_r($cols);
						// echo '</pre>';
						// $col[$cols[$index_header['Article_CODE']]] = $cols;
						$rowCsv_EN = array();
						$rowCsv_TH = array();
						foreach ($index_header as $key => $value) {
							//for by column check in the row
							// $dataRow = $col[];
							if(isset($map_attribute[$key]['multiselect'])){
								switch($index_magento[$value]){
									case 'multiselect':
										foreach ($map_attribute[$key]['multiselect'] as $km => $vm) {
											if($km == 'selling_point'){
												if(trim($cols[$value])=='1'){
													$rowCsv_EN[array_search('selling_point', $indexEN)] .= ','.$multiselect[$key];	
												}
												$rowCsv_EN[array_search('selling_point', $indexEN)] = ltrim($rowCsv_EN[array_search('selling_point', $indexEN)],',');
												$rowCsv_EN[array_search('selling_point', $indexEN)] = rtrim($rowCsv_EN[array_search('selling_point', $indexEN)],',');
											} else {
												$rowCsv_EN[array_search('categories', $indexEN)] .= ','.$catcodes[trim($cols[$value])];
												$rowCsv_EN[array_search('categories', $indexEN)] = ltrim($rowCsv_EN[array_search('categories', $indexEN)],',');
												$rowCsv_EN[array_search('categories', $indexEN)] = rtrim($rowCsv_EN[array_search('categories', $indexEN)],',');
											}
											//// price label

										}
										break;
								}
							} else {
								if(preg_match( "/_TH$/i", $key)){
									if(preg_match( "/COLOR_TH$/i", $key)){
										$rowCsv_TH[]=$cols[$index_header['COLOR_EN']];

									} else {
										$rowCsv_TH[]=$cols[$value];
									}
								} else {
									$rowCsv_EN[array_search($index_magento[$value], $indexEN)] = $cols[$value];
									if($key == "Article_CODE"){
										$rowCsv_TH[array_search($index_magento[$value], $indexTH)] = $cols[$value];
									}
								}
							}

						}
						echo '<pre>';
						print_r($rowCsv_EN);
						echo '</pre>';
						$file = fopen($dirprepare."Import_Produce_EN.csv","a+");
						fputcsv($file,$rowCsv_EN);
						fclose($file);

						$file = fopen($dirprepare."Import_Produce_TH.csv","a+");
						fputcsv($file,$rowCsv_TH);
						fclose($file);
					}
					$rowNum++;
					
				}

				Mage::log('close file : '.$dir.$filenamecsv, null, $filelogName);

				// moved file to completed path
				$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'product_master'.DS.'save'.DS;

				// Create folder
				if (!file_exists($newdir)) {
					$file = new Varien_Io_File();
					$file->mkdir($newdir);
				}

				// Mage::log($newdir);
				unlink($dir.$filename);
				rename($dir.$filenamecsv, $newdir.$filenamecsv);

				// Tiw
				// check to remove file
				if (!file_exists($dir.$filename)) {
					Mage::log('removed : '.$dir.$filename, null, $filelogName);
				}else{
					Mage::log('can not removed : '.$dir.$filename, null, $filelogName);
				}

				// check to move file
				if (!file_exists($dir.$filenamecsv)) {
					Mage::log('moved to completed : '.$newdir.$filenamecsv, null, $filelogName);
				}else{
					Mage::log('can not moved : '.$newdir.$filenamecsv, null, $filelogName);
				}
				

		    }
		}

   	}
}