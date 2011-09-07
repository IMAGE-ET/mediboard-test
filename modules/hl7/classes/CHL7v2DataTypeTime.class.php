<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeTime extends CHL7v2DataType {
  function toMB($value, CHLv2Field $field){
    $hl7 = $this->parseHL7($value, $field);
    
    if ($hl7 === false) {
      return;
    }
    
    return      CValue::read($hl7, "hour",   "00").
            ":".CValue::read($hl7, "minute", "00").
            ":".CValue::read($hl7, "second", "00");
  }
  
  function toHL7($value, CHLv2Field $field) {
    $mb = $this->parseMB($value, $field);
    
    if ($mb === false) {
      return;
    }
    
    
    return $mb["hour"].
           $mb["minute"].
           $mb["second"];
  }
}