<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */
class Amasty_Birth_Block_Log extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $tableName = $resource->getTableName('ambirth/log');

        $query = "SELECT * FROM " . $tableName . "  order by log_id desc limit 5";

        $data = $readConnection->fetchAll($query);
        
        $html = '<table class="form-list">
                    <tr>
                        <td class="label"><a href="#" onclick="showBirthLog()">' . $this->__('Ambirth Log Table') . '</a></td>
                        <td class="value">';
        if (count($data) === 0){
            $html .=  $this->__('Log is empty');
        }
        else {
            $html .= '<table style="display: none;" id="ambirth_log_table">';
            foreach ($data as $row){
               $html .= '<tr>
                    <td style="padding: 0px 5px;">' . $row['log_id']      . '</td>
                    <td style="padding: 0px 5px;">' . $row['customer_id'] . '</td>
                    <td style="padding: 0px 5px;">' . $row['type']        . '</td>
                    <td style="padding: 0px 5px;">' . $row['sent_date']   . '</td>
                </tr>';
            }
            $html .= '        </table>
                          </td>
                        </tr>
                    </table>
                    <script type="text/javascript">
                        function showBirthLog(){
                        $("ambirth_log_table").show();
                        }
                        decorateTable("ambirth_log_table");
                    </script>
            ';
        }
        
        return $html;
    }


}