<?php
class Magebuzz_Importcustomer_Helper_Data extends Mage_Core_Helper_Abstract{
  //note: if want show city address in admin tab address need add attribute city(26) to customer_address_entity_varchar 
  //      show column State/Province in customer grid nee add attribute region(28) to customer_address_entity_varchar
  
  public function insertCustomer(){
    $this->clearCustomerTempData();
    $this->readDataFromCSV();
    $this->addCustomer();
    $this->addCustomerAttribute();
    $this->addCustomerAddress();
    try{
      $this->setDefaultAddress();
    }
    catch(Exception $e){
      
    }
  }
  
  public function clearCustomerTempData() {
		$import_customer_temp = $this->_getTableName('import_customer_temp');
		$query = "TRUNCATE TABLE `" . $import_customer_temp . "`";
		$this->_getWriteConnection()->query($query);
	}
  
  public function readDataFromCSV(){
    $filepath = Mage::getBaseDir('media') . DS . 'importcustomer' .DS. 'customer.csv';
    $handler = new Varien_File_Csv();
    $import_data = $handler->getData($filepath);
    
    $customer_array = array();
    $key = $import_data[0];
    
    foreach($import_data as $row => $dataRow) {
      if($row == 0){
        continue;
      }
      $customer_array[] = array_combine($key, $dataRow);
    }
    
    //save data in table import_customer_temp
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    if (count($customer_array)) {
      foreach ($customer_array as  $item){
        $query = "
          INSERT IGNORE INTO ".$import_customer_temp." 
          (
            `website_id`,
            `group_id`, 
            `firstname`,
            `lastname`,
            `email`,
            `password`,
            `country_id`,
            `region_id`,
            `city`,
            `subdistrict_id`,
            `postcode`,
            `street`,
            `telephone`,
            `fax`,
            `joycard`,
            `mobile`
          )
          VALUES (
            '".$item['website_id']."',
            '".$item['group_id']."',
            '".$item['firstname']."',
            '".$item['lastname']."',
            '".$item['email']."',
            '".$item['password']."',
            '".$item['country_id']."',
            '".$item['region_id']."',
            '".$item['city']."',
            '".$item['subdistrict_id']."',
            '".$item['postcode']."',
            '".$item['street']."',
            '".$item['telephone']."',
			'".$item['fax']."',
			'".$item['joycard']."',
			'".$item['mobile']."'
          )
        ";
        //'".$this->getHashPass($item['password'])."',

        $this->_getWriteConnection()->query($query);
      }
    }
  }
  
  public function addCustomer(){
    $customer_entity_tab = $this->_getTableName('customer_entity');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    //update customer_id for table import_customer_temp
    $query = "
			UPDATE ".$import_customer_temp." ict JOIN ".$customer_entity_tab." cet
			ON ict.email = cet.email
			SET ict.customer_id = cet.entity_id
		";
		$this->_getWriteConnection()->query($query);
    
    //inset customer to customer_entity
    $query = "
			INSERT INTO ".$customer_entity_tab."
			(
				`entity_id`,
				`entity_type_id`,
				`attribute_set_id`,
				`website_id`,
				`email`,
				`group_id`,
				`increment_id`,
				`store_id`,
				`created_at`,
				`updated_at`,
				`is_active`,
				`disable_auto_group_change`
			)
			(
				SELECT
				customer_id,
				'".$this->_getCustomerEntityTypeId()."',
				'0',
				website_id,
				email,
				group_id,
				'',
				'1',
				now(),
				now(),
        '1',
        '0'
				FROM ".$import_customer_temp."
			)
			ON DUPLICATE KEY UPDATE
			updated_at=now()
		";
		$this->_getWriteConnection()->query($query);
    
    //update customer_id for table import_customer_temp
    $query = "
			UPDATE ".$import_customer_temp." ict JOIN ".$customer_entity_tab." cet
			ON ict.email = cet.email
			SET ict.customer_id = cet.entity_id
		";
		$this->_getWriteConnection()->query($query);
  }
  
  public function addCustomerAttribute(){
    $this->addFirstName();
    $this->addLastName();
    $this->addPassword();
    $this->addCustomAttribute('customer_entity_varchar', 'joycard');
  }
  

  public function addCustomAttribute($attr_table, $attr_name){
    $customer_entity_varchar = $this->_getTableName($attr_table);
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerEntityTypeId()."',
				'".$this->_getCustomerAttributeId($attr_name)."',
				customer_id,{$attr_name}
				FROM ".$import_customer_temp." ict
			)
			ON DUPLICATE KEY UPDATE
			value=ict.{$attr_name}
		";
		$this->_getWriteConnection()->query($query);
  }

  public function addFirstName(){
    $customer_entity_varchar = $this->_getTableName('customer_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerEntityTypeId()."',
				'".$this->_getCustomerAttributeId('firstname')."',
				customer_id,				
        firstname
				FROM ".$import_customer_temp." ict
			)
			ON DUPLICATE KEY UPDATE
			value=ict.firstname
		";
		$this->_getWriteConnection()->query($query);
  }
  
  public function addLastName(){
    $customer_entity_varchar = $this->_getTableName('customer_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerEntityTypeId()."',
				'".$this->_getCustomerAttributeId('lastname')."',
				customer_id,				
        lastname
				FROM ".$import_customer_temp." ict
			)
			ON DUPLICATE KEY UPDATE
			value=ict.lastname
		";
		$this->_getWriteConnection()->query($query);
  }
  
  public function addPassword(){
    $customer_entity_varchar = $this->_getTableName('customer_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerEntityTypeId()."',
				'".$this->_getCustomerAttributeId('password_hash')."',
				customer_id,				
        password
				FROM ".$import_customer_temp." ict
			)
			ON DUPLICATE KEY UPDATE
			value=ict.password
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //address for customer
  public function addCustomerAddress(){
    $this->addAddressEntity();
    $this->addFirstNameAddress();
    $this->addLastNameAddress();
    $this->addCountryAddress();
    //$this->addRegionAddress();
    $this->addRegionIdAddress();
    //$this->addCityAddress();
    $this->addCityIdAddress();
    $this->addSubdistrictIdAddress();
    //$this->addSubdistrictAddress();
    $this->addPostcodeAddress();
    $this->addStreetAddress();
    $this->addTelephoneAddress();
    $this->addFaxAddress();
    $this->addMobileAddress();
  }
  
  public function addAddressEntity(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity."
			(
				`entity_type_id`,
				`attribute_set_id`,
				`increment_id`,
				`parent_id`,
				`created_at`,
				`updated_at`,
				`is_active`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
				'0',
        '',
				customer_id,				
        now(),
        now(),
        '1'
				FROM ".$import_customer_temp."
			)
			ON DUPLICATE KEY UPDATE
			updated_at=now()
		";
		$this->_getWriteConnection()->query($query);
  }
  
  public function addFirstNameAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('firstname')."',
				MAX(cae.entity_id),				
        firstname
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
			
		";
		$this->_getWriteConnection()->query($query);
  }
  
  public function addLastNameAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('lastname')."',
				MAX(cae.entity_id),				
        lastname
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add country
  public function addCountryAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('country_id')."',
				MAX(cae.entity_id),			
        country_id
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add region
 /*  public function addRegionAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('region')."',
				MAX(cae.entity_id),				
        region_id
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  } */
  
  /* add region id */
  public function addRegionIdAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    //$customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $customer_address_entity_int = $this->_getTableName('customer_address_entity_int');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_int."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('region_id')."',
				MAX(cae.entity_id),				
        region_id
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add city
  /* public function addCityAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('city')."',
				MAX(cae.entity_id),				
        city
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  } */
  
  public function addCityIdAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_int = $this->_getTableName('customer_address_entity_int');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_int."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('city_id')."',
				MAX(cae.entity_id),				
        city
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add subdistrict
  public function addSubdistrictIdAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_int = $this->_getTableName('customer_address_entity_int');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_int."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('subdistrict_id')."',
				MAX(cae.entity_id),				
        subdistrict_id
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add postcode
  public function addPostcodeAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('postcode')."',
				MAX(cae.entity_id),				
        postcode
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add Street
  public function addStreetAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_text = $this->_getTableName('customer_address_entity_text');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_text."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('street')."',
				MAX(cae.entity_id),				
        street
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //add telephone
  public function addTelephoneAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('telephone')."',
				MAX(cae.entity_id),				
        telephone
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }

  //add fax
  public function addFaxAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('fax')."',
				MAX(cae.entity_id),				
        telephone
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }

    //add mobile
  public function addMobileAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_address_entity_varchar = $this->_getTableName('customer_address_entity_varchar');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_address_entity_varchar."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerAddressEntityTypeId()."',
        '".$this->_getCustomerAddressAttributeId('mobile_customer')."',
				MAX(cae.entity_id),				
        telephone
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //set default billing and shipping address
  public function setDefaultAddress(){
    $this->setDefaultBillingAddress();
    $this->setDefaultShippingAddress();
  }
  
  //set default billing address
  public function setDefaultBillingAddress(){
    //table = customer_entity_int;
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_entity_int = $this->_getTableName('customer_entity_int');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_entity_int."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerEntityTypeId()."',
        '".$this->_getCustomerAttributeId('default_billing')."',
				customer_id,				
        MAX(cae.entity_id)
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  //set default shipping address
  public function setDefaultShippingAddress(){
    $customer_address_entity = $this->_getTableName('customer_address_entity');
    $customer_entity_int = $this->_getTableName('customer_entity_int');
    $import_customer_temp = $this->_getTableName('import_customer_temp');
    
    $query = "
			INSERT INTO ".$customer_entity_int."
			(
				`entity_type_id`,
				`attribute_id`,
				`entity_id`,
				`value`
			)
			(
				SELECT
				'".$this->_getCustomerEntityTypeId()."',
        '".$this->_getCustomerAttributeId('default_shipping')."',
				customer_id,				
        MAX(cae.entity_id)
				FROM ".$import_customer_temp." ict JOIN ".$customer_address_entity." cae
        ON cae.parent_id=ict.customer_id
        GROUP BY ict.customer_id
			)
		";
		$this->_getWriteConnection()->query($query);
  }
  
  #######################
  
  public function getHashPass($password){
    $salt = Mage_Admin_Model_User::HASH_SALT_LENGTH;
    $salt = Mage::helper('core')->getRandomString($salt);
    $hastPass = md5($salt . $password) . ':' . $salt;
    return $hastPass;
  }
  
  //for customer
  protected function _getCustomerAttributeId($code) {
		$attributeId = Mage::getResourceModel('eav/entity_attribute')
			->getIdByCode('customer', $code);
		return $attributeId;
	}
  
  protected function _getCustomerEntityTypeId() {
		$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("customer");
		return $entity_type->getId();
	}
  
  //for customer address
  protected function _getCustomerAddressAttributeId($code) {
		$attributeId = Mage::getResourceModel('eav/entity_attribute')
			->getIdByCode('customer_address', $code);
		return $attributeId;
	}
  
  protected function _getCustomerAddressEntityTypeId(){
    $entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("customer_address");
		return $entity_type->getId();
  }
  
  protected function _getTableName($name) {
		return Mage::getSingleton('core/resource')->getTableName($name);
	}
  
  protected function _getWriteConnection() {
		return Mage::getSingleton('core/resource')->getConnection('core_write');
	}
  
  protected function _getReadConnection() {
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
  
  /* public function getHash($password, $salt = false){
    if (is_integer($salt)) {
      $salt = $this->_helper->getRandomString($salt);
    }
    return $salt === false ? $this->hash($password) : $this->hash($salt . $password) . ':' . $salt;
  }
  
  public function hash($data){
    return md5($data);
  } */
}