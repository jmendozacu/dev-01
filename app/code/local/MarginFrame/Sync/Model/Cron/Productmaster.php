<?php
class MarginFrame_Sync_Model_Cron_Productmaster extends Mage_Core_Model_Abstract
{
	public function Run() {
		$message = array();
		$check = false ;
		$filenamecsv = '';
		try {
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
				'MC-SAP1' 				=> array('multiselect'=> array('category_ids'=>'lv1')),
				'MC-SAP2' 				=> array('multiselect'=> array('category_ids'=>'lv2')),
				'MC-SAP3' 				=> array('multiselect'=> array('category_ids'=>'lv3')),
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
				'JoyPrice' 				=> array('multiselect'=> array('price_tag'=>'joyprice')),//joyprice
				'DontMiss' 				=> array('multiselect'=> array('price_tag'=>'dontmiss')),//dontmiss
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

			$price_tag = array(
				'JoyPrice' => 'joyprice',
				'DontMiss' => 'dontmiss',
				'HotPrice' => 'hotprice'
			);
		
			$product = Mage::getModel('catalog/product');
			$collections = Mage::getModel('mgfsync/catcode')->getCollection();
			$catcodes = array();
			foreach ($collections->getData() as $catcode) {
				$catcodes[$catcode['catalog_code']][] = $catcode['entity_id'];
			}
			//var_dump($catcodes);
			while (false !== ($filename = readdir($dh))) {
			    $files[] = $filename;

			    // find .trg file
			    if(strrpos($filename, '.trg')) {

			    	// rename file .trg to .csv
			    	$filenamecsv = str_replace('.trg', '.txt', $filename);
			    	Mage::log('open : '.$dir.$filenamecsv, null, $filelogName,true);
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
					//echo $filenamecsv;
					
					$iiii = 0;

					$res_TH = array();
					$res_EN = array();

					$tmpval = "";
					foreach ($data as $row) {		
						
						//check col
						//$tmpcol = explode("|", $row);
						//var_dump(count($tmpcol).':'.$iiii);
						//$iiii++;

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
							
							//$file = fopen($dirprepare."Import_Produce_TH.csv","w+");
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
							$res_TH[] = '"' . implode('","', $indexTH) . '"';
							
							// array_unshift($indexTH,'sku');
							// $str = rtrim($str,',');
							//fputcsv($file, $indexTH, ',', '"');
							//fclose($file);

							$import_EN[] ='visibility';
							//$file = fopen($dirprepare."Import_Produce_EN.csv","w+");
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
									if(isset($index_magento[$index_header[$value]])){
										$indexEN[] = $index_magento[$index_header[$value]];	
									} else {
										$indexEN[] = $value;
									}
								}
							}
							$res_EN[] = '"' . implode('","', $indexEN) . '"';
							
							// $str = rtrim($str,',');
	    					//fputcsv($file, $indexEN, ',', '"');
							//fclose($file);
							
							
						} else {
							$cols = explode("|", $row);
							if($cols[0]==''){
								continue;
							}
							// echo '<pre>';
							// print_r($cols);
							// echo '</pre>';
							// $col[$cols[$index_header['Article_CODE']]] = $cols;
							$rowCsv_EN = array_fill(0, count($indexEN) - 1, "");
							$rowCsv_TH = array();

							$i_selling_point = array_search('selling_point', $indexEN);
							$i_price_tag = array_search('price_tag', $indexEN);
							$i_category_ids = array_search('category_ids', $indexEN);
							//var_dump($indexEN); die;
							$rowCsv_EN[$i_category_ids] = "1,2,3";
							foreach ($index_header as $key => $value) {
								//for by column check in the row
								// $dataRow = $col[];
								if(isset($map_attribute[$key]['multiselect'])){
									switch($index_magento[$value]){
										case 'multiselect':
											foreach ($map_attribute[$key]['multiselect'] as $km => $vm) {
												if($km == 'selling_point'){
													if(trim($cols[$value])=='1'){
														$rowCsv_EN[$i_selling_point] .= ','.$multiselect[$key];	
													}
													$rowCsv_EN[$i_selling_point] = ltrim($rowCsv_EN[$i_selling_point],',');
													$rowCsv_EN[$i_selling_point] = rtrim($rowCsv_EN[$i_selling_point],',');
												} elseif($km == 'price_tag'){
													if(trim($cols[$value])=='1'){
														$rowCsv_EN[$i_price_tag] .= ','.$price_tag[$key];	
													}
													$rowCsv_EN[$i_price_tag] = ltrim($rowCsv_EN[$i_price_tag],',');
													$rowCsv_EN[$i_price_tag] = rtrim($rowCsv_EN[$i_price_tag],',');
												} elseif($km == 'category_ids'){
													if(isset($catcodes[trim($cols[$value])])){
														$rowCsv_EN[$i_category_ids] .= ','. implode(',', $catcodes[trim($cols[$value])]);
														$rowCsv_EN[$i_category_ids] = ltrim($rowCsv_EN[$i_category_ids],',');
														$rowCsv_EN[$i_category_ids] = rtrim($rowCsv_EN[$i_category_ids],',');
													}
													else {
														$rowCsv_EN[$i_category_ids] .= "";
													}
												}
											}
											break;
									}
								} else {
									if(preg_match( "/_TH$/i", $key)){
										if(preg_match( "/COLOR_TH$/i", $key)){
											$rowCsv_TH[]=$cols[$index_header['COLOR_EN']];
										} else {
											$tmpval = preg_replace("/[\\\\]{2,}/", '\\', $cols[$value]);
											$tmpval = preg_replace("/[']{2,}/", '"', $cols[$value]);
											$tmpval = str_replace(array('\\"', '"', '\\'), array('""' ,'""', '\\\\'), $tmpval);
											$rowCsv_TH[] = $tmpval;
										}
									} elseif(preg_match( "/PICTURE$/i", $key)){
										if($cols[$value]==''){
											$rowCsv_EN[array_search($index_magento[$value], $indexEN)] = $cols[$index_header['Article_CODE']].'.jpg';
										} else {
											$rowCsv_EN[array_search($index_magento[$value], $indexEN)] = $cols[$value];
										}
									} else {
										if($key == "Article_CODE"){
											$rowCsv_TH[array_search($index_magento[$value], $indexTH)] = $cols[$value];
										}
										else {
											$tmpval = preg_replace("/[\\\\]{2,}/", '\\', $cols[$value]);
											$tmpval = preg_replace("/[']{2,}/", '"', $cols[$value]);
											$tmpval = str_replace(array('\\"', '"', '\\'), array('""' ,'""', '\\\\'), $tmpval);
											$rowCsv_EN[array_search($index_magento[$value], $indexEN)] = $tmpval;
										}
									}
								}

							}
							$rowCsv_EN[] = '2'; //visibility
							
			
							$res_EN[] = '"' . implode('","', $rowCsv_EN) . '"';
							$res_TH[] = '"' . implode('","', $rowCsv_TH) . '"';

							//$file = fopen($dirprepare."Import_Produce_EN.csv","a+");
							//fputcsv($file, $rowCsv_EN, ',', '"');
							//fclose($file);

							//$file = fopen($dirprepare."Import_Produce_TH.csv","a+");
							//fputcsv($file, $rowCsv_TH, ',', '"');
							//fclose($file);
						}
						$rowNum++;
						
					}

					file_put_contents($dirprepare."Import_Produce_EN.csv", implode("\n", $res_EN));
					file_put_contents($dirprepare."Import_Produce_TH.csv", implode("\n", $res_TH));

					Mage::log('close file : '.$dir.$filenamecsv, null, $filelogName,true);

					// moved file to completed path
					$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'product_master'.DS.'save'.DS;

					// Create folder
					if (!file_exists($newdir)) {
						$file = new Varien_Io_File();
						$file->mkdir($newdir);
					}

					// Mage::log($newdir);
					/*
					unlink($dir.$filename);
					rename($dir.$filenamecsv, $newdir.$filenamecsv);

					// Tiw
					// check to remove file
					if (!file_exists($dir.$filename)) {
						Mage::log('removed : '.$dir.$filename, null, $filelogName,true);
					}else{
						Mage::log('can not removed : '.$dir.$filename, null, $filelogName,true);
					}

					// check to move file
					if (!file_exists($dir.$filenamecsv)) {
						Mage::log('moved to completed : '.$newdir.$filenamecsv, null, $filelogName,true);
					}else{
						Mage::log('can not moved : '.$newdir.$filenamecsv, null, $filelogName,true);
					}
					*/

			    }
			}

			// $log = 'Success';

		} catch(Exception $ex) {
			$check = true;
			$message[] = 'Error : '.$ex->getMessage();
		}

		$sync_type = 'Product Master';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}