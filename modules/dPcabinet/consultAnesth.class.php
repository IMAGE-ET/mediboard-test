<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('mbobject' ) );

require_once($AppUI->getModuleClass('dPcim10', 'favoricim10'));
require_once($AppUI->getModuleClass('dPcim10', 'codecim10'));
require_once($AppUI->getModuleClass('dPpatients', 'patients'));
require_once($AppUI->getModuleClass('dPcabinet', 'consultation'));
require_once($AppUI->getModuleClass('dPcabinet', 'plageconsult'));
require_once($AppUI->getModuleClass('dPcabinet', 'files'));
require_once($AppUI->getModuleClass('dPcompteRendu', 'compteRendu'));

class CConsultAnesth extends CMbObject {
  // DB Table key
  var $consultation_anesth_id = null;

  // DB References
  var $consultation_id = null;
  var $operation_id = null;

  // DB fields
  var $poid = null;
  var $taille = null;
  var $groupe = null;
  var $rhesus = null;
  var $antecedents = null;
  var $traitements = null;
  var $tabac = null;
  var $oenolisme = null;
  var $transfusions = null;
  var $tasys = null;
  var $tadias = null;
  
  var $intubation = null;
  var $biologie = null;
  var $commande_sang = null;
  var $ASA = null;

  // Form fields
  var $_date_consult = null;
  var $_date_op = null;

  // Object References
  var $_ref_consult = null;
  var $_ref_last_consultanesth = null;
  var $_ref_operation = null;
  var $_ref_plageconsult = null;

  function CConsultAnesth() {
    $this->CMbObject( 'consultation_anesth', 'consultation_anesth_id' );

    $this->_props["consultation_id"] = "ref|notNull";
    $this->_props["operation_id"]    = "ref|notNull";
    // @todo : un type particulier pour le poid et la taille
    $this->_props["poid"]            = "currency";
    $this->_props["taille"]          = "currency";
    $this->_props["groupe"]          = "enum|?|O|A|B|AB";
    $this->_props["rhesus"]          = "enum|?|-|+";
    $this->_props["antecedants"]     = "str|confidential";
    $this->_props["traitements"]     = "str|confidential";
    $this->_props["tabac"]           = "enum|?|-|+|++";
    $this->_props["oenolisme"]       = "enum|?|-|+|++";
    $this->_props["transfusions"]    = "enum|?|-|+";
    $this->_props["tasys"]           = "num";
    $this->_props["tadias"]          = "num";
    $this->_props["intubation"]      = "enum|?|dents|bouche|cou";
    $this->_props["biologie"]        = "enum|?|NF|COAG|IONO";
    $this->_props["commande_sang"]   = "enum|?|clinique|CTS|autologue";
    $this->_props["ASA"]             = "enum|1|2|3|4|5";

    $this->buildEnums();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  }
   
  function updateDBFields() {
    parent::updateDBFields();
  }

  function check() {
    // Data checking
    $msg = null;
    return $msg . parent::check();
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_consultation = new CConsultation;
    $this->_ref_consultation->load($this->consultation_id);
    $this->_ref_consultation->loadRefsFwd();
    $this->_ref_plageconsult =& $this->_ref_consultation->_ref_plageconsult;
    $this->_ref_operation = new COperation;
    $this->_ref_operation->load($this->operation_id);
    $this->_ref_operation->loadRefsFwd();
    $this->_date_consult =& $this->_ref_consultation->_date;
    $this->_date_op =& $this->_ref_operation->_ref_plageop->date;
  }
  
  function loadRefsBack() {
    // Backward references
  }
  
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_consultation->fillTemplate($template);
    $this->_ref_operation->fillTemplate($template);
  }
}

?>