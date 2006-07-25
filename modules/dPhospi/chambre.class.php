<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("dPhospi"     , "lit"));
require_once($AppUI->getModuleClass("dPhospi"     , "service"));
require_once($AppUI->getModuleClass("dPplanningOp", "pathologie"));

/**
 * Classe CChambre. 
 * @abstract Gère les chambre d'hospitalisation
 * - contient des lits
 */
class CChambre extends CMbObject {
  // DB Table key
	var $chambre_id = null;	
  
  // DB References
  var $service_id = null;

  // DB Fields
  var $nom = null;
  var $caracteristiques = null; // côté rue, fenêtre, lit accompagnant, ...

  // Form Fields
  var $_nb_lits_dispo        = null;
  var $_overbooking          = null;
  var $_ecart_age            = null;
  var $_genres_melanges      = null;
  var $_chambre_seule        = null;
  var $_chambre_double       = null;
  var $_conflits_chirurgiens = null;
  var $_conflits_pathologies = null;

  // Object references
  var $_ref_service = null;
  var $_ref_lits = null;

	function CChambre() {
		$this->CMbObject("chambre", "chambre_id");
    
    $this->_props["service_id"]       = "ref|notNull";
    $this->_props["nom"]              = "str|notNull|confidential";
    $this->_props["caracteristiques"] = "str|confidential";
    
    $this->_seek["nom"]        = "like";
	}

  function loadRefsFwd() {
    $this->_ref_service = new CService;
    $this->_ref_service->load($this->service_id);
  }

  function loadRefsBack() {
    $where = array (
      "chambre_id" => "= '$this->chambre_id'"
    );
    // A cause de l'inversion porte - fenêtre
    $order = "lit.nom DESC";
    
    $this->_ref_lits = new CLit;
    $this->_ref_lits = $this->_ref_lits->loadList($where, $order);
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "Lits", 
      "name"      => "lit", 
      "idfield"   => "lit_id", 
      "joinfield" => "chambre_id"
    );
        
    return CDpObject::canDelete($msg, $oid, $tables);
  }
  
  function checkChambre() {
    global $pathos;
    
    assert($this->_ref_lits !== null);
    $this->_nb_lits_dispo = count($this->_ref_lits);
    
    $listAff = array();
    
    $this->_chambre_seule = 0;
    $this->_chambre_double = 0;
    $this->_conflits_pathologies = 0;
    $this->_ecart_age = 0;
    $this->_genres_melanges = false;
    $this->_conflits_chirurgiens = 0;

    foreach ($this->_ref_lits as $key => $lit) {
      assert($lit->_ref_affectations !== null);

      // overbooking
      $lit->checkOverBooking();
      $this->_overbooking += $lit->_overbooking;

      // Lits dispo
      if (count($lit->_ref_affectations)) {
        $this->_nb_lits_dispo--;
      }
      
      // Liste des affectations
      foreach($lit->_ref_affectations as $key2 => $aff)
        $listAff[] =& $this->_ref_lits[$key]->_ref_affectations[$key2];
    }

    foreach ($listAff as $affectation1) {
      $sejour1 =& $affectation1->_ref_sejour;
      assert($sejour1);
      $patient1 =& $sejour1->_ref_patient;
      assert($patient1);
      $chirurgien1 =& $sejour1->_ref_praticien;
      assert($chirurgien1);
      if((count($this->_ref_lits) == 1) && $sejour1->chambre_seule == "n")
        $this->_chambre_double++;
      if((count($this->_ref_lits) > 1) && $sejour1->chambre_seule == "o")
        $this->_chambre_seule++;
      
      foreach($listAff as $affectation2) {
      	$flag = $affectation1->affectation_id != $affectation2->affectation_id;
      	$flag = $flag && $affectation1->colide($affectation2);
      	$flag = $flag && ($affectation1->lit_id != $affectation2->lit_id);
   	    if($flag) {
          $sejour2 =& $affectation2->_ref_sejour;
          assert($sejour2);
          $patient2 =& $sejour2->_ref_patient;
          assert($patient2);
          $chirurgien2 =& $sejour2->_ref_praticien;
          assert($chirurgien2);

          // Conflits de pathologies
          $pathologie1 = array(
            "pathologie" => $sejour1->pathologie,
            "septique" => $sejour1->septique);
          $pathologie2 = array(
            "pathologie" => $sejour2->pathologie,
            "septique" => $sejour2->septique);
          if (!$pathos->isCompat($pathologie1["pathologie"], $pathologie2["pathologie"], $pathologie1["septique"], $pathologie2["septique"]))
            $this->_conflits_pathologies++;

          // Ecart d'âge
          $ecart = max($patient1->_age, $patient2->_age)-min($patient1->_age, $patient2->_age);
          $this->_ecart_age = max($ecart, $this->_ecart_age);

          // Genres mélangés
          if(($patient1->sexe != $patient2->sexe) && (($patient1->sexe == "m") || ($patient2->sexe == "m")))
            $this->_genres_melanges = true;
        
          // Conflit de chirurgiens
          if(($chirurgien1->user_id != $chirurgien2->user_id) && ($chirurgien1->function_id == $chirurgien2->function_id))
            $this->_conflits_chirurgiens++;
        }
      }
    }
    $this->_conflits_pathologies /= 2;
    $this->_conflits_chirurgiens /= 2;
  }
}
  
?>