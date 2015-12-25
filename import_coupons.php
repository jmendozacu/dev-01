<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

ini_set('display_errors', E_ALL);
ini_set('auto_detect_line_endings', 1);  
set_time_limit(0);

$message = "";

if (isset($_POST['rule_id'])){
    $rule_id = intVal($_POST['rule_id']);
    
    if (!$rule_id){
        $message .= "<br/>Please provide an existing shopping cart rule ID";
    }
    
    if (empty($_FILES['file']['tmp_name'])){
        $message .= "<br/>Please upload a file.";
    }
    elseif (empty($_FILES['file']['size']) || !stripos($_FILES['file']['name'],'csv')){
        $message .= "<br/>Please upload a CSV file with coupons, one coupom per line.";
    }
    
    if (!$message){
        $message = generate($rule_id, $_FILES['file']['tmp_name']);    
    }
}


function generate($rule_id, $fileName)
{
    $fields = array(
        'name', 
        'description', 
        'from_date', 
        'to_date', 
        'coupon_code', 
        'uses_per_coupon', 
        'uses_per_customer', 
        'customer_group_ids', 
        'is_active', 
        'conditions_serialized', 
        'actions_serialized', 
        'stop_rules_processing', 
        'is_advanced', 
        'product_ids', 
        'sort_order', 
        'simple_action', 
        'discount_amount', 
        'discount_qty', 
        'discount_step', 
        'simple_free_shipping', 
        //'apply_to_shipping', 
        'times_used', 
        'is_rss', 
        'website_ids', 
    );
    $message = db_connect();
    if ($message){
        return $message;
    }

    $lines = file($fileName);
    $names = join(',', $fields);
    
    
    $tableName = 'salesrule';
    $path = dirname(__FILE__) . '/app/etc/local.xml';
    if (file_exists($path)){
         $xml = file_get_contents($path);
         $m  = array();
         if (preg_match('#<table_prefix><!\[CDATA\[(.*?)\]\]></table_prefix>#', $xml, $m)){
            $tableName = $m[1] . $tableName;
         }
    }    
    
    $n = 0;
    foreach ($lines as $code){
        $code = trim($code, " \t\r\n\",'");
        if (!$code)
            continue;
        $fields[4] = '"' . addcslashes($code, '"') . '"';
        $into = join(',', $fields);
        $sql = 'INSERT INTO `'.$tableName.'` ('.$names.') SELECT '.$into.' FROM `'.$tableName.'` WHERE rule_id = ' . $rule_id;
        if (!mysql_query($sql)){
            $message .= "<br/>Can not add coupon code `$code`, skipped: " . mysql_error();    
        }
        else {
            ++$n;
        }
        
        
    }
    $message .= "<br/><br/> $n coupon code(s) has been added.";
    return $message;
}
	
function db_connect(){
    
    $DB = array('chset'=>"utf8");
    
    $path = dirname(__FILE__) . '/app/etc/local.xml';
    if (file_exists($path)){
     $xml = file_get_contents($path);
     $m  = array();
     if (preg_match('#<host><!\[CDATA\[(.*?)\]\]></host>#', $xml, $m))
        $DB['host'] = $m[1];
    
     if (preg_match('#<username><!\[CDATA\[(.*?)\]\]></username>#', $xml, $m))
        $DB['user'] = $m[1];
        
     if (preg_match('#<password><!\[CDATA\[(.*?)\]\]></password>#', $xml, $m))
        $DB['pwd'] = $m[1];
        
     if (preg_match('#<dbname><!\[CDATA\[(.*?)\]\]></dbname>#', $xml, $m))
        $DB['db'] = $m[1];
    }
    else {
        return "<br>Please upload the file to the Magento root folder.";
    }

    $dbh = @mysql_connect($DB['host'].($DB['port']?":$DB[port]":''),$DB['user'],$DB['pwd']);
    if (!$dbh) {
        return 'Cannot connect to the MySQL host "'.$DB['host'].'", error is: ' . mysql_error();
    }
    $res = mysql_select_db($DB['db'], $dbh);
    
    if (!$res) {
        return 'Cannot select database "'. $DB['db'] .', error is: ' . mysql_error();
    }
        
    mysql_query("SET NAMES utf8");
    
    return '';
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta content="Amasty Ltd." http-equiv="autor">
    <title>Import coupons for Magento CE 1.4.0 and CE 1.3</title>
</head>
<body>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']?>" enctype="multipart/form-data">
<p><span style="color:red;font-weight:bold"><?php echo $message?></span></p>
<table border="1" align="center" width="50%">
<tr>
    <td width="30%">Rule ID:</td>
    <td><input type="text" size="3" value="" name="rule_id"/></td>
</tr>
<tr>
    <td>CSV file with coupons:</td>
    <td><input type="file" name="file" /></td>
</tr>
<tr>
    <td colspan="2" align="left"><input type="submit" value="Submit"/></td>
</tr>
</table>
</form>
</body>
</html>