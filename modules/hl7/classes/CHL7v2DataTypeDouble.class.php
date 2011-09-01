<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeDouble extends CHL7v2DataType {
  function toMB($value){
    $result = parent::toMB($value);
    return ($result === null ? null : (double)$result);
  }
  
  function toHL7($value) {
    $result = parent::toHL7($value);
    return ($result === null ? null : (double)$result);
  }
}
