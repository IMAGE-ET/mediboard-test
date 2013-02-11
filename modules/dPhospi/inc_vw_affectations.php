<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/


/**
 * Retourne une référence sur un praticien donné, 
 * après mise en cache si nécessaire
 */
function & getCachedPraticien($praticien_id) {
  static $listPraticiens = array();
  
  if (!array_key_exists($praticien_id, $listPraticiens)) {
    $praticien = new CMediusers;
    $praticien->load($praticien_id);
    $praticien->_ref_function =& getCachedFunction($praticien->function_id);
    $listPraticiens[$praticien_id] =& $praticien;
  }
  
  return $listPraticiens[$praticien_id];  
}

/**
 * Retourne une référence sur une fonction donnée, 
 * après mise en cache si nécessaire
 */
function & getCachedFunction($function_id) {
  static $listFunctions = array();
  
  if (!array_key_exists($function_id, $listFunctions)) {
    $function = new CFunctions;
    $function->load($function_id);
    $listFunctions[$function_id] =& $function;
  }
  
  return $listFunctions[$function_id];  
}

/**
 * Retourne une référence sur un patient donné, 
 * après mise en cache si nécessaire
 */
function & getCachedPatient($patient_id) {
  static $listPatients = array();
  
  if (!array_key_exists($patient_id, $listPatients)) {
    $patient = new CPatient;
    $patient->load($patient_id);
    $listPatients[$patient_id] =& $patient;
  } 
  
  return $listPatients[$patient_id];  
}

/**
 * Retourne une référence sur un lit donné, 
 * après mise en cache si nécessaire
 */
function &getCachedLit($lit_id) {
  static $listLits = array();
  
  if (!array_key_exists($lit_id, $listLits)) {
    $lit = new CLit;
    $lit->load($lit_id);
    $lit->loadRefChambre();
    $listLits[$lit_id] =& $lit;
  }

  return $listLits[$lit_id];  
}

/**
 * Charge complètement un service pour l'affichage des affectations
 */
function loadServiceComplet(&$service, $date, $mode, $praticien_id = "", $type = "") {
  $service->loadRefsBack();
  $service->_nb_lits_dispo = 0;
  $dossiers = array();

  foreach ($service->_ref_chambres as $chambre_id => &$chambre) {
    $chambre->loadRefsBack();

    foreach ($chambre->_ref_lits as $lit_id => &$lit) {
      $lit->loadAffectations($date);

      foreach ($lit->_ref_affectations as $affectation_id => &$affectation) {
        if (!$affectation->effectue || $mode) {
          $affectation->loadRefSejour();
          if ($praticien_id){
          	if($affectation->_ref_sejour->praticien_id != $praticien_id){
          		unset($lit->_ref_affectations[$affectation_id]);
          		continue;
          	}
          }
         if ($type){
            if($affectation->_ref_sejour->type != $type){
              unset($lit->_ref_affectations[$affectation_id]);
              continue;
            }
          }
          
          $affectation->loadRefsAffectations();
          $affectation->checkDaysRelative($date);

          $aff_prev =& $affectation->_ref_prev;
          if ($aff_prev->affectation_id) {
            $aff_prev->_ref_lit =& getCachedLit($aff_prev->lit_id);
          }

          $aff_next =& $affectation->_ref_next;
          if ($aff_next->affectation_id) {
            $aff_next->_ref_lit =& getCachedLit($aff_next->lit_id);
          }

          $sejour =& $affectation->_ref_sejour;
          $sejour->loadRefPrestation();
          $sejour->loadRefsOperations();
          $sejour->loadNDA();
          $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
          $sejour->_ref_patient =& getCachedPatient($sejour->patient_id);
          $sejour->_ref_patient->loadRefDossierMedical(false);
          
		      // Chargement des droits CMU
          $sejour->getDroitsCMU();

          foreach($sejour->_ref_operations as $operation_id => $curr_operation) {
            $sejour->_ref_operations[$operation_id]->loadExtCodesCCAM();
          }
          $chambre->_nb_affectations++;
          $dossiers[] = $sejour->_ref_patient->_ref_dossier_medical;
        } else {
          unset($lit->_ref_affectations[$affectation_id]);
        }
      }
    }
    CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");
    if(!$service->externe) {
      $chambre->checkChambre();
      $service->_nb_lits_dispo += ($chambre->annule == 0 ? $chambre->_nb_lits_dispo : 0);
    }
  }
}

/**
 *  Chargement des admissions à affecter
 */
function loadSejourNonAffectes($where, $order = null, $praticien_id = null) {
  $group_id = CGroups::loadCurrent()->_id;
  
  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
    "patients"        => "sejour.patient_id = patients.patient_id"
  );

  if ($praticien_id){
    $where["sejour.praticien_id"] = " = '$praticien_id'";
  }

  $where["sejour.group_id"] = "= '$group_id'";
  
  $where[] = "(sejour.type != 'seances' && affectation.affectation_id IS NULL) || sejour.type = 'seances'";
  
  if ($order == null){
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }

  $sejourNonAffectes = new CSejour;
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, $order, null, null, $leftjoin);

  foreach ($sejourNonAffectes as &$sejour) {
  	$sejour->loadRefPrestation();
  	$sejour->loadNDA();
  	$sejour->loadRefsPrescriptions();
    $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
    $sejour->_ref_patient   =& getCachedPatient($sejour->patient_id);
    $sejour->_ref_patient->loadRefDossierMedical(false);
    
    // Chargement des droits CMU
    $sejour->getDroitsCMU();
    
    // Chargement des opérations
    $sejour->loadRefsOperations();
    foreach($sejour->_ref_operations as &$operation) {
      $operation->loadExtCodesCCAM();
    }
  }
  $dossiers = CMbArray::pluck($sejourNonAffectes, "_ref_patient", "_ref_dossier_medical");
  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");
  return $sejourNonAffectes;
}

/**
 * Chargement des affectations dans les couloirs
 */
function loadAffectationsCouloirs($where, $order = null, $praticien_id = null) {
  $group_id = CGroups::loadCurrent()->_id;
  
  $ljoin = array(
    "sejour"          => "affectation.sejour_id = sejour.sejour_id",
    "patients"        => "sejour.patient_id = patients.patient_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  );
  
  if ($praticien_id) {
    $where["sejour.praticien_id"] = " = '$praticien_id'";
  }
  
  if ($order == null){
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }
  
  $where["affectation.lit_id"] = " IS NULL";
  
  $affectation = new CAffectation;
  $affectations = $affectation->loadList($where, $order, null, null, $ljoin);
  
  $tab_affectations = array();
  
  foreach ($affectations as $affectation) {
    $affectation->loadRefsAffectations();
    $affectation->loadRefSejour()->loadRefPatient();
    $aff_prev =& $affectation->_ref_prev;
    if ($aff_prev->affectation_id) {
      $aff_prev->_ref_lit =& getCachedLit($aff_prev->lit_id);
    }

    $aff_next =& $affectation->_ref_next;
    if ($aff_next->affectation_id) {
      $aff_next->_ref_lit =& getCachedLit($aff_next->lit_id);
    }

    $sejour =& $affectation->_ref_sejour;
    $sejour->loadRefPrestation();
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
    $sejour->_ref_patient =& getCachedPatient($sejour->patient_id);
    $sejour->_ref_patient->loadRefDossierMedical(false);
    
    // Chargement des droits CMU
    $sejour->getDroitsCMU();

    foreach($sejour->_ref_operations as $operation_id => $curr_operation) {
      $sejour->_ref_operations[$operation_id]->loadExtCodesCCAM();
    }
    $tab_affectations[$affectation->service_id][] = $affectation;
  }
  $dossiers = CMbArray::pluck($affectations, "_ref_sejour", "_ref_patient", "_ref_dossier_medical");
  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

  return $tab_affectations;
}
