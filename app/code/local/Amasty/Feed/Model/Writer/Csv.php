<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */  
class Amasty_Feed_Model_Writer_Csv extends Amasty_Feed_Model_Writer_Abstract
{
    function write(){
        
        if ($this->isFirstStep()){
            
            $header = $this->_getFeed()->getCsvHeaderStatic();
            if (!empty($header)) {
                fwrite($this->fp, $header."\n");
            }
            
            if ($this->_getFeed()->getCsvHeader()) {
                $fields = $this->_getFeed()->getFields();
                $this->writeRecord($fields["name"], 1);
            }
        }
        
        return parent::write();
    }
    public function writeRecord($record, $isfirst = 0)
    {
        $encl  = chr($this->_getFeed()->getCsvEnclosure());
        $delim = chr($this->_getFeed()->getCsvDelimiter());     
        foreach($record as $inx => $val){
            $record[$inx] = nl2br($val);
            //$record[$inx] = str_replace(array("\r\n", "\n"), array(" ", " "), $val);
        }
        if($delim == "\t"){
            $cols = array();
            foreach($record as $inx => $val){
                $val = ltrim($val,'\-');
                $val = ltrim($val,'\\\'');
                $val = ltrim($val,'-');
                $val = ltrim($val,'\''); 
                $cols[] = '"' . str_replace(array('"', '\\', "\t"), array('""', '\\\\', ''), $val) . '"';
            }
            $row = implode($delim, $cols)."\n";

            if($isfirst){
                $row = "\xFF\xFE".iconv("UTF-8","UCS-2LE", $row);
            }
            else {
                $row = iconv("UTF-8","UCS-2LE", $row);
            }
            fwrite($this->fp, $row);
        }
        else if ( $encl == 'n') {
            foreach($record as $inx => $val){
                $record[$inx] = str_replace($delim, "", $val);
            }
            fwrite($this->fp, implode($delim, $record) . "\n");
        } else {
            fputcsv($this->fp, $record, $delim, $encl);
        }
    }
}