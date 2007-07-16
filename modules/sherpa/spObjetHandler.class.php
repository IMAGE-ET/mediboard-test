<?php /* $Id: mbobject.class.php 2252 2007-07-12 10:00:15Z rhum1 $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: 1793 $
 *  @author Thomas Despoix
*/

/**
 * Class CMbObjectHandler 
 * @abstract Event handler class for CMbObject
 */

class CSpObjectHandler extends CMbObjectHandler {
  
  function getSpInstance(CMbObject &$mbObject) {
    switch ($mbObject->_class_name) {
      case "CPatient": return new CSpMalade;
      default : return null;
    }
  }
  
  function getIdsFor(CMbObject &$mbObject) {
    global $g, $m;
    
    // Etablissement courant
    $id400 = new CIdSante400;
    $tags = array (
      "sherpa",
      "etab:$g",
    );
    $id400->loadLatestFor($mbObject, join($tags, " "));
    mbTrace($id400->getProps());
  }
  
  function onStore(CMbObject &$mbObject) {
    if (null == $spInstance = $this->getSpInstance($mbObject)) {
      return;
    }
    
    mbTrace($mbObject->getProps(), "Storing object");
    mbTrace($this->getIdsFor($mbObject), "Found ids");
    die;
    
  }
  
  function onDelete(CMbObject &$mbObject) {}
}

?>