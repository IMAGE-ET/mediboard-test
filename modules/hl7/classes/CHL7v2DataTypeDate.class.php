<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CHL7v2DataTypeDate extends CHL7v2DataType {
  function toMB($value, CHL7v2Field $field){
    $parsed = $this->parseHL7($value, $field);
    
    // empty value
    if ($parsed === "") {
      return "";
    }
    
    // invalid value
    if ($parsed === false) {
      return;
    }
    
    return CValue::read($parsed, "year")."-".
           CValue::read($parsed, "month", "00")."-".
           CValue::read($parsed, "day", "00");
  }
  
  function toHL7($value, CHL7v2Field $field) {
    $parsed = $this->parseMB($value, $field);
    
    // empty value
    if ($parsed === "") {
      return "";
    }
    
    // invalid value
    if ($parsed === false) {
      return;
    }
    
    return $parsed["year"].($parsed["month"] === "00" ? "" : $parsed["month"]).($parsed["day"] === "00" ? "" : $parsed["day"]);
  }
}
