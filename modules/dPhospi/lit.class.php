<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("dPhospi", "chambre"));
require_once($AppUI->getModuleClass("dPhospi", "affectation"));

/**
 * Classe CLit. 
 * @abstract Gère les lits d'hospitalisation
 */
class CLit extends CMbObject {
  // DB Table key
	var $lit_id = null;	
  
  // DB References
  var $chambre_id = null;

  // DB Fields
  var $nom = null;
  
  // Form Fields
  var $_overbooking = null;

  // Object references
  var $_ref_chambre      = null;
  var $_ref_affectations = null;
  var $_ref_last_dispo   = null;
  var $_ref_next_dispo   = null;

	function CLit() {
		$this->CMbObject("lit", "lit_id");
    
    $this->_props["chambre_id"] = "ref|notNull";
    $this->_props["nom"]        = "str|notNull|confidential";
    
    $this->_seek["nom"]        = "like";
    $this->_seek["chambre_id"] = "ref|CChambre";
	}

  function loadAffectations($date) {
    $where = array (
      "lit_id" => "= '$this->lit_id'",
      "entree" => "<= '$date 23:59:59'",
      "sortie" => ">= '$date 00:00:00'"
    );
    $order = "sortie DESC";
    
    $this->_ref_affectations = new CAffectation;
    $this->_ref_affectations = $this->_ref_affectations->loadList($where, $order);
    
    $this->checkDispo($date);
  }
  
  function updateFormFields() {
    $this->_view = $this->nom;
  }
  
  function loadCompleteView() {
    $this->loadRefsFwd();
    
    $chambre =& $this->_ref_chambre;
    $chambre->loadRefsFwd();
    
    $this->_view = "{$chambre->_ref_service->nom} - $chambre->nom - $this->nom";
  }

  function loadRefsFwd() {
    $this->_ref_chambre = new CChambre;
    $this->_ref_chambre->load($this->chambre_id);
    
    $this->_view = "{$this->_ref_chambre->nom} - $this->nom";
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "Affectations", 
      "name"      => "affectation", 
      "idfield"   => "affectation_id", 
      "joinfield" => "lit_id"
    );
        
    return CDpObject::canDelete($msg, $oid, $tables);
  }
  
  function checkOverBooking() {
    assert($this->_ref_affectations !== null);
    $this->_overbooking = 0;
    $listAff = $this->_ref_affectations;
    
    foreach ($this->_ref_affectations as $aff1) {
      foreach ($listAff as $aff2) {
        if ($aff1->affectation_id != $aff2->affectation_id) {
          if (($aff2->entree < $aff1->sortie and $aff2->sortie > $aff1->sortie)
            or ($aff2->entree < $aff1->entree and $aff2->sortie > $aff1->entree)
            or ($aff2->entree >= $aff1->entree and $aff2->sortie <= $aff1->sortie)) {
              $this->_overbooking++;
          }
        }
      }
    }
    $this->_overbooking = $this->_overbooking / 2;
  }
  
  function checkDispo($date) {
    assert($this->_ref_affectations !== null);

    // Last Dispo
    $where = array (
      "lit_id" => "= '$this->lit_id'",
      "sortie" => "<= '$date 23:59:59'",
    );
    $order = "sortie DESC";
    
    $this->_ref_last_dispo = new CAffectation;
    $this->_ref_last_dispo->loadObject($where, $order);
    $this->_ref_last_dispo->checkDaysRelative($date);
    
    // Next Dispo
    $where = array (
      "lit_id" => "= '$this->lit_id'",
      "entree" => ">= '$date 00:00:00'",
    );
    $order = "entree ASC";

    $this->_ref_next_dispo = new CAffectation;
    $this->_ref_next_dispo->loadObject($where, $order);
    $this->_ref_next_dispo->checkDaysRelative($date);
  }
  
}
?>