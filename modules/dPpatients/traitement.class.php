<?php /* $Id: traitement.class.php,v 1.2 2006/02/01 10:53:25 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.2 $
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('mbobject' ) );

require_once($AppUI->getModuleClass('dPpatients', 'patients'));

class CTraitement extends CMbObject {
  // DB Table key
  var $traitement_id = null;

  // DB References
  var $patient_id = null;

  // DB fields
  var $debut = null;
  var $fin = null;
  var $traitement = null;
  
  // Object References
  var $_ref_patient = null;

  function CTraitement() {
    $this->CMbObject( 'traitement', 'traitement_id' );

    $this->_props["patient_id"] = "ref|notNull";
    $this->_props["debut"]      = "date|notNull";
    $this->_props["fin"]        = "date";
    $this->_props["traitement"] = "text";
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
}

?>