<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");
require_once("./classes/fieldSpecs/refSpec.class.php");

class CRefMandatorySpec extends CRefSpec {
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    if($propValue === null || $object->$fieldName == ""){
      return "N'est pas une référence (format non numérique)";
    }
    
    if ($propValue === 0) {
      return "ne peut pas être une référence nulle";
    }
    return parent::checkProperty(&$object);
  }
}

?>