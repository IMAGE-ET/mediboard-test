<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeDate extends CHL7v2DataType {
  function toMB($value, CHLv2Field $field){
    $hl7 = $this->parseHL7($value, $field);
    
    if ($hl7 === false) {
      return;
    }
    
    return CValue::read($hl7, "year")."-".
           CValue::read($hl7, "month", "00")."-".
           CValue::read($hl7, "day", "00");
  }
  
  function toHL7($value, CHLv2Field $field) {
    $mb = $this->parseMB($value, $field);
    
    if ($mb === false) {
      return;
    }
    
    return $mb["year"].($mb["month"] === "00" ? "" : $mb["month"]).($mb["day"] === "00" ? "" : $mb["day"]);
  }
}
