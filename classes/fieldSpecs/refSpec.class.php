<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CRefSpec extends CMbFieldSpec {
  
  var $class   = null;
  var $cascade = null;
  var $unlink  = null;
  var $meta    = null;
  
  function getSpecType() {
    return("ref");
  }

  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
      
    if ($propValue === null || $object->$fieldName === ""){
      return "N'est pas une référence (format non numérique)";
    }
    
    if ($propValue == 0) {
      return "ne peut pas être une référence nulle";
    }
    
    if ($propValue < 0) {
      return "N'est pas une référence (entier négatif)";
    }
    
    if (!$this->class and !$this->meta) {
      return "Type d'objet cible on défini";
    }
    
    $class = $this->class;
    if ($meta = $this->meta) {
      $class = $object->$meta;
    }
    
    if (!is_subclass_of($class, "CMbObject")) {
      return "La classe '$class' n'est pas une classe d'objet métier";
    }
    
    $ref = new $class;
    if (!$this->unlink && !$ref->load($propValue)) {
      return "Objet référencé de type '$class' introuvable";      
    }

    return null;
  }
  
  function getDBSpec(){
    return "INT(11) UNSIGNED";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    $length = 8;
    CMbArray::defaultValue($params, "size", $length+2);
    CMbArray::defaultValue($params, "maxlength", $length);
    return $this->getFormElementText($object, $params, $value, $className);
  }
}

?>