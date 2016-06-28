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
                $this->writeRecord($fields["name"]);
            }
        }
        
        return parent::write();
    }
    public function writeRecord($record, $islast = 0)
    {
        $encl  = chr($this->_getFeed()->getCsvEnclosure());
        $delim = chr($this->_getFeed()->getCsvDelimiter());     
        
        //ipune: replace \r\n
        foreach($record as $inx => $val){
            //$record[$inx] = nl2br($val);
            $record[$inx] = str_replace(array("\t","\r\n", "\n"), array(" ", "\t", "\t"), $val);
        }

        //ipune: csv tab use for excel can read thai
        if($delim == "^"){
            $cols = array();
            foreach($record as $inx => $val){
                $val = ltrim($val,'\-');
                $val = ltrim($val,'\\\'');
                $val = ltrim($val,'-');
                $val = ltrim($val,'\''); 
                $cols[] = '"' . str_replace(array('"', '\\', "\t"), array('""', '\\\\', ''), $val) . '"';
            }
            $row = implode("\t", $cols)."\n";
            if($isfirst){
                $row = "\xFF\xFE".iconv("UTF-8","UCS-2LE", $row);
            }
            else {
                $row = iconv("UTF-8","UCS-2LE", $row);
            }
            fwrite($this->fp, $row);
        }
        
        else if ($encl == 'n') {
            foreach($record as $inx => $val){
                $record[$inx] = str_replace($delim, "", $val);
            }
            $row = implode($delim, $record);
            //if(!$islast){
                $row = $row ."\n";
            //}
            $row = iconv("UTF-8","TIS-620", $row);
            fwrite($this->fp, $row);
        } else {
            fputcsv($this->fp, $record, $delim, $encl);
        }
    }
}