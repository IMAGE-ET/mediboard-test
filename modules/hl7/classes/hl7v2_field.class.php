<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "hl7v2_segment");

class CHL7v2Field extends CHL7v2Segment {  
  static $typesBase = array(
    "Date",
    "DateTime",
    "Double", 
    "Integer",
    "String",
    "Time"
  );
  
  var $name        = null;
  var $description = null;
  var $datatype    = null;
  
}

?>