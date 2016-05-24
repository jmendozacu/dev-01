<?php

class MarginFrame_Sync_Helper_Ftp extends Mage_Core_Helper_Abstract
{
	
	public function sftpconnect($ftp_server, $ftp_port, $ftp_user_name, $ftp_user_pass){
		try {
			//FTP
			if((string)$ftp_port == '21') {
				$ftp = new Varien_Io_ftp();
				$option = array(
						'host' => $ftp_server,
						'port' => (int)$ftp_port,
						'user'=> $ftp_user_name,
						'password'=> $ftp_user_pass,
						'timeout'=> 20);
				$ftp->open($option);
				return $ftp;
			}
			//SFTP
			else {
				$sftp = new Varien_Io_Sftp();
				$option = array('host' => $ftp_server.':'.$ftp_port,
						'username'=> $ftp_user_name,
						'password'=> $ftp_user_pass,
						'timeout'=> 20);
				$sftp->open($option);
				return $sftp;
			}
		} catch (Exception $e) { 
			Mage::log((string)$e, null, 'mgf_ftp.log');
			return false; 
		}
	}
	
	//Varien_Io_Sftp or Varien_Io_ftp
	public function sftpwrite($sftp, $file_name, $contant, $createtrg = true) {
		try
		{
			//$file = fopen('php://temp', 'r+');
			if (strpos($file_name,'.xml')) {
				//fwrite($file, "\xEF\xBB\xBF".$contant);
				$contant = "\xEF\xBB\xBF".$contant;
			} else {
				//fwrite($file, $contant);
			}
			//rewind($file);
			$sftp->write($file_name, $contant);
			if($createtrg) {
				$trg_file_name = str_replace(array('.xml', '.txt', '.pdf'), '.trg', $file_name);
				//$trgfile = fopen('php://temp', 'r+');
				$sftp->write($trg_file_name, '');
			}		
		} catch (Exception $e) {
			Mage::log((string)$e, null, 'mgf_ftp.log');
		}
	}
	
	public function removeBOM($str=""){
		//if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
		//	$str=substr($str, 3);
		//}
		$bom = pack("CCC", 0xef, 0xbb, 0xbf);
		if (0 === strncmp($str, $bom, 3)) {
			//echo "BOM detected - file is UTF-8\n";
			$str = substr($str, 3);
		}
		return $str;
	}
	
	
	
	public function ftplist($ftp_server, $ftp_port = 21, $ftp_user_name, $ftp_user_pass, $ftp_dir) {
		$filelist = null;
		$conn_id = ftp_connect($ftp_server, $ftp_port) or die("Couldn't connect to $ftp_server");
		if(ftp_login($conn_id, $ftp_user_name, $ftp_user_pass))
		{
			$pasv = ftp_pasv($conn_id, TRUE);
			$changedir = ftp_chdir($conn_id, $ftp_dir);
		
			$filelist = ftp_nlist($conn_id, ".");
		
			// output $contents
			//var_dump ($contents);
		}
		ftp_close($conn_id);
		
		return $filelist;
	}

	public function ftpread($ftp_server, $ftp_port = 21, $ftp_user_name, $ftp_user_pass, $ftp_dir, $file_name) {
		$contents = null;
		$conn_id = ftp_connect($ftp_server, $ftp_port) or die("Couldn't connect to $ftp_server");
		if(ftp_login($conn_id, $ftp_user_name, $ftp_user_pass))
		{
			$pasv = ftp_pasv($conn_id, TRUE);
			$changedir = ftp_chdir($conn_id, $ftp_dir);
	
			//Create temp handler:
			$tempHandle = fopen('php://temp', 'r+');			
			//Get file from FTP:
			if (ftp_fget($conn_id, $tempHandle, $file_name, FTP_BINARY, 0)) {
				rewind($tempHandle);
				$contents = stream_get_contents($tempHandle);
			} else {
				$contents = null;
			}
						
			/*
			ob_start();
			$result = ftp_get($conn_id, "php://output", $file_name, FTP_BINARY);
			$contents = ob_get_contents();
			ob_end_clean();	
			*/
		}
		ftp_close($conn_id);
	
		return $contents;
	}	
	public function ftpconnread($ftp_connect, $file_name) {
		$contents = null;
		//Create temp handler:
		$tempHandle = fopen('php://temp', 'r+');			
		//Get file from FTP:
		if (ftp_fget($ftp_connect, $tempHandle, $file_name, FTP_BINARY, 0)) {
			rewind($tempHandle);
			$contents = stream_get_contents($tempHandle);
		}
		return $contents;
	}
	
	public function ftpwrite($ftp_server, $ftp_port = 21, $ftp_user_name, $ftp_user_pass, $ftp_dir, $file_name, $contant, $createtrg = true) {
		try
		{
			//Mage::log('ftpwrite'.$ftp_server.$ftp_port.$ftp_user_name.$ftp_user_pass.$ftp_dir.$file_name.$contant, null, 'mgf.log');
					
			$conn_id = ftp_connect($ftp_server, $ftp_port) or die("Couldn't connect to $ftp_server");
			if(ftp_login($conn_id, $ftp_user_name, $ftp_user_pass))
			{
				
				$pasv = ftp_pasv($conn_id, TRUE);
				$changedir = ftp_chdir($conn_id, $ftp_dir);
			
				$fp = fopen('php://temp', 'r+');
				if (strpos($file_name,'.pdf') == false) {
					fwrite($fp, "\xEF\xBB\xBF".$contant);
				} else {
					fwrite($fp, $contant);
				}
				rewind($fp);
				$upload = ftp_fput($conn_id, $file_name, $fp, FTP_BINARY);
				fclose($fp);
				
				if($createtrg) {					
					$trg_file_name = str_replace('.xml', '.trg',$file_name);
					$trg_file_name = str_replace('.txt', '.trg',$trg_file_name);
					$trg_file_name = str_replace('.pdf', '.trg',$trg_file_name);
					$fp = fopen('php://temp', 'r+');
					$upload = ftp_fput($conn_id, $trg_file_name, $fp, FTP_BINARY);
					fclose($fp);
				}
			}
			ftp_close($conn_id);
			
		} catch (Exception $e) {
			Mage::log((string)$e, null, 'mgf_ftp.log');
		}
	}
	
	public function ftpmove($ftp_connect, $dir, $filename, $newdir) {	
		if(ftp_rename($ftp_connect, $dir.$filename, $newdir.$filename)) {
       		//echo "File $dir.$filename moved to ".$newdir.$filename;
		} else {
		    //echo "ERROR!!!. The file could not be moved";
		}
		return $this;
	}
	
}

?>