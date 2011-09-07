<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeInteger extends CHL7v2DataType {
  function toMB($value, CHLv2Field $field){
    $result = parent::toMB($value, $field);
    return ($result === false ? null : (int)$result);
  }
  
  function toHL7($value, CHLv2Field $field) {
    $result = parent::toHL7($value, $field);
    return ($result === false ? null : (int)$result);
  }
}
