<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CAdministration extends CMbMetaObject {
  // DB Field
  var $administration_id = null;
  var $administrateur_id = null;  // Utilisateur effectuant l'administration
  var $dateTime          = null;  // Heure de l'administration
  var $quantite          = null;  // Info sur la prise
  var $unite_prise       = null;  // Info sur la prise
  var $commentaire       = null;  // Commentaire sur l'administration
  var $prise_id          = null;
  
  // Gestion des replanifications
  var $planification     = null;  // Flag permettant de gerer les plannifications
  var $original_dateTime = null;  // Champ permettant de stocker la date d'origine de la prise prevue replanifiée
  
  // Form field
  var $_heure = null;
  
  // Object references
  var $_ref_administrateur = null;
  var $_ref_transmissions  = null;
  var $_ref_log   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'administration';
    $spec->key   = 'administration_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["transmissions"] = "CTransmissionMedicale object_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["object_id"]         = "ref notNull class|CMbObject meta|object_class";
    $specs["object_class"]      = "enum notNull list|CPrescriptionLineMedicament|CPrescriptionLineElement|CPrescriptionLineMixItem";
    $specs["administrateur_id"] = "ref notNull class|CMediusers";
    $specs["prise_id"]          = "ref class|CPrisePosologie";
    $specs["quantite"]          = "float";
    $specs["unite_prise"]       = "text";
    $specs["dateTime"]          = "dateTime";
    $specs["commentaire"]       = "text";
    $specs["planification"]     = "bool default|0";
    $specs["original_dateTime"] = "dateTime";
    return $specs;
  }

  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_heure = substr(mbTime($this->dateTime), 0, 2);
  	$this->_unite_prise = ($this->unite_prise !== "aucune_prise" ? $this->unite_prise : ""); // Parfois modifié par loadRefPrise
  }
  
  function loadRefsFwd(){
  	parent::loadRefsFwd();
  	$this->loadRefAdministrateur();
  	if($this->_ref_object){
      $this->_ref_object->loadRefsFwd();
  	}
    $dateFormat = "%d/%m/%Y à %Hh%M";
  	$this->_view = "Administration du ".mbTransformTime(null, $this->dateTime, $dateFormat)." par {$this->_ref_administrateur->_view}";
  	if($this->object_class === "CPrescriptionLineMedicament") {
  		$this->_view .= " ({$this->_ref_object->_ucd_view})";
  	}
  }
  
  function loadRefsTransmissions(){
  	$this->_ref_transmissions = $this->loadBackRefs("transmissions");
		foreach($this->_ref_transmissions as &$_trans){
  	  $_trans->loadRefsFwd();
    }
  }
  
  function loadRefPrise(){
  	$this->_ref_prise = new CPrisePosologie();
  	$this->_ref_prise = $this->_ref_prise->getCached($this->prise_id);
  	$this->_unite_prise = $this->_ref_prise->_unite;
  }
  
  function loadRefAdministrateur(){
  	$this->_ref_administrateur = new CMediusers();
  	$this->_ref_administrateur = $this->_ref_administrateur->getCached($this->administrateur_id);
  }
  
  function loadRefLog(){
    $this->_ref_log = new CUserLog();
    $this->_ref_log->object_id = $this->_id;
    $this->_ref_log->object_class = $this->_class_name;
    $this->_ref_log->loadMatchingObject();
    $this->_ref_log->loadRefsFwd();
  }
}

?>