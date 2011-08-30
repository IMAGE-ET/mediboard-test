<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeTime extends CHL7v2DataType {
  protected $type = "Time";
	
  function toMB($value){
    $hl7 = $this->parseHL7($value);
    return      CValue::read($hl7, "hour",   "00").
            ":".CValue::read($hl7, "minute", "00").
            ":".CValue::read($hl7, "second", "00");
  }
  
  function toHL7($value) {
    $mb = $this->parseMB($value);
    return $mb["hour"].
           $mb["minute"].
           $mb["second"];
  }
}