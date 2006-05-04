<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass('mbobject'));

require_once($AppUI->getModuleClass('dPgestionCab', 'gestionCab') );

/**
 * The CRubrique Class
 */
class CRubrique extends CMbObject {
  // DB Table key
  var $rubrique_id = null;

  // DB Fields
  var $function_id = null;
  var $nom = null;

  // Object References
  var $_ref_function = null;

  function CRubrique() {
    $this->CMbObject('rubrique_gestioncab', 'rubrique_id');
    
    $this->_props["function_id"] = "ref|notNull";
    $this->_props["nom"]         = "str|notNull";

    $this->buildEnums();
  }

  // Forward references
  function loadRefsFwd() {
    // fonction (cabinet)
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
}

?>