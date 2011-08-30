<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeDate extends CHL7v2DataType {
	protected $type = "Date";
	
  function toMB($value){
    $hl7 = $this->parseHL7($value);
		return $hl7["year"]."-".CValue::read($hl7, "month", "00")."-".CValue::read($hl7, "day", "00");
  }
  
  function toHL7($value) {
    $mb = $this->parseMB($value);
    return $mb["year"].($mb["month"] === "00" ? "" : $mb["month"]).($mb["day"] === "00" ? "" : $mb["day"]);
  }
}
