<?php

class MarginFrame_TTF_Model_Ftp extends Mage_Core_Model_Abstract
{
	
	protected $_connection;
	protected $_options;
	protected $_type;

	public function connect($ftp_server, $ftp_port = '21', $ftp_user_name = '', $ftp_user_pass = ''){
		
		if($ftp_server == 'database'){
			$ftp_server = '139.162.52.23'; //trim(Mage::getStoreConfig('mgfsyncapi/server/ip'));
            $ftp_port = '22'; //trim(Mage::getStoreConfig('mgfsyncapi/server/port'));
            $ftp_user_name = 'root'; //trim(Mage::getStoreConfig('mgfsyncapi/server/user'));
            $ftp_user_pass = '#sit20696'; //trim(Mage::getStoreConfig('mgfsyncapi/server/password'));
		}

		try {
			//FTP
			if($ftp_port == '21') {
				$this->_type = 'ftp';
				$this->_connection = new Varien_Io_ftp();
				$this->_options = array(
						'host' => $ftp_server,
						'port' => (int)$ftp_port,
						'user'=> $ftp_user_name,
						'password'=> $ftp_user_pass,
						'timeout'=> 20);
				$this->_connection->open($this->_options);
			}
			//SFTP
			else {
				$this->_type = 'sftp';
				$this->_connection = new Varien_Io_Sftp();
				$this->_options = array('host' => $ftp_server.':'.$ftp_port,
				//$this->_options = array('host' => $ftp_server,
				//		'port' => (int)$ftp_port,
						'username'=> $ftp_user_name,
						'password'=> $ftp_user_pass,
						'timeout'=> 60);
				$this->_connection->open($this->_options);
			}
			return $this->_connection;

		} catch (Exception $e) { 
			throw $e;
			//Mage::log((string)$e->getMessage(), null, 'mgf_ftp.log');
		}
		return $this;
	}

	//Varien_Io_Sftp or Varien_Io_ftp
	public function write($file_name, $contant, $createtrg = true) 
	{	
		try
		{

			$this->_connection->open($this->_options);
			if($this->_connection){
				
					//$file = fopen('php://temp', 'r+');
					if (strpos($file_name,'.xml')) {
						//fwrite($file, "\xEF\xBB\xBF".$contant);
						$contant = "\xEF\xBB\xBF".$contant;
					} else {
						//fwrite($file, $contant);
					}
					//rewind($file);
					$this->_connection->write($file_name, $contant);
					if($createtrg) {
						$trg_file_name = str_replace(array('.xml', '.txt', '.pdf'), '.trg', $file_name);
						//$trg_file_name = $file_name . '.trg';
						//$trgfile = fopen('php://temp', 'r+');
						$this->_connection->write($trg_file_name, '');
					}
					$this->_connection->close();
			}

		} catch (Exception $e) {
			throw $e;
			//Mage::log((string)$e->getMessage(), null, 'mgf_ftp.log');
		}

		return $this;
	}
	
}

?>