<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $g;

global $pathos;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date       = mbGetValueFromGetOrSession("date", mbDate()); 
$heureLimit = "16:00:00";
$mode       = mbGetValueFromGetOrSession("mode", 0);

/**
 * Retourne une référence sur un praticien donné, 
 * après mise en cache si nécessaire
 */
function &getCachedPraticien($praticien_id) {
  static $listPraticiens = array();
  
  if (!array_key_exists($praticien_id, $listPraticiens)) {
    $praticien = new CMediusers;
    $praticien->load($praticien_id);
    $praticien->_ref_function =& getCachedFunctions($praticien->function_id);
    $listPraticiens[$praticien_id] =& $praticien;
  }
  
  return $listPraticiens[$praticien_id];  
}

/**
 * Retourne une référence sur une fonction donnée, 
 * après mise en cache si nécessaire
 */
function &getCachedFunctions($function_id) {
  static $listFunctions = array();
  
  if (!array_key_exists($function_id, $listFunctions)) {
    $function = new CFunctions;
    $function->load($function_id);
    $listFunctions[$function_id] =& $function;
  }
  
  return $listFunctions[$function_id];  
}

/**
 * Retourne une référence sur un lit donné, 
 * après mise en cache si nécessaire
 */
function &getCachedLits($lit_id) {
  static $listLits = array();
  
  if (!array_key_exists($lit_id, $listLits)) {
    $lit = new CLit;
    $lit->load($lit_id);
    $lit->loadRefChambre();
    $listLits[$lit_id] =& $lit;
  }
    
  return $listLits[$lit_id];  
}


// Initialisation de la liste des chirs, patients et plagesop
global $listPats;
$listPats = array();

// Récupération du service à ajouter/éditer
$totalLits = 0;

// Récupération des chambres/services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$services = $services->loadList($where);

// Affichage ou non des services
$vwService = array();
$vwServiceCookie = mbGetValueFromCookie("fullService", null);
foreach ($services as $curr_service_id => $curr_service) {
  $vwService[$curr_service_id] = 1;
}
if($vwServiceCookie) {
  $vwServiceCookieArray = explode("@", $vwServiceCookie);
  mbRemoveValuesInArray("", $vwServiceCookieArray);
  foreach($vwServiceCookieArray as $element) {
    $matches = null;
    preg_match("/service(\d+)-trigger:trigger(Show|Hide)/i", $element, $matches);
    if($matches[2] == "Show") {
      $vwService[$matches[1]] = 0;
    }
  }
}

foreach ($services as $service_id => $service) {
  if($vwService[$service_id]) {
    $services[$service_id]->_vwService = 1;
    $services[$service_id]->loadRefsBack();
    $services[$service_id]->_nb_lits_dispo = 0;
    $chambres =& $services[$service_id]->_ref_chambres;
    foreach ($chambres as $chambre_id => $chambre) {
      $chambres[$chambre_id]->loadRefsBack();
      $lits =& $chambres[$chambre_id]->_ref_lits;
      foreach ($lits as $lit_id => $lit) {
        $lits[$lit_id]->loadAffectations($date);
        $affectations =& $lits[$lit_id]->_ref_affectations;
        foreach ($affectations as $affectation_id => $affectation) {
        	if(!$affectations[$affectation_id]->effectue || $mode) {
            $affectations[$affectation_id]->loadRefSejour();
            $affectations[$affectation_id]->loadRefsAffectations();
            $affectations[$affectation_id]->checkDaysRelative($date);
  
            $aff_prev =& $affectations[$affectation_id]->_ref_prev;
            if ($aff_prev->affectation_id) {
              $aff_prev->_ref_lit =& getCachedLits($aff_prev->lit_id);
            }
  
            $aff_next =& $affectations[$affectation_id]->_ref_next;
            if ($aff_next->affectation_id) {
              $aff_next->_ref_lit =& getCachedLits($aff_next->lit_id);
            }
  
            $sejour =& $affectations[$affectation_id]->_ref_sejour;
            $sejour->loadRefsOperations();
            $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);

            if(isset($listPats[$sejour->patient_id])) {
              $sejour->_ref_patient =& $listPats[$sejour->patient_id];
            }
            else {
              $sejour->loadRefPatient();
              $listPats[$sejour->patient_id] =& $sejour->_ref_patient;
            }
            foreach($sejour->_ref_operations as $operation_id => $curr_operation) {
              $sejour->_ref_operations[$operation_id]->loadRefCCAM();
            }
            $affectations[$affectation_id]->_ref_sejour->_ref_patient->verifCmuEtat($affectations[$affectation_id]->_ref_sejour->_date_entree_prevue);
          } else {
            unset($affectations[$affectation_id]);
          }
        }
      }
      $chambres[$chambre_id]->checkChambre();
      $services[$service_id]->_nb_lits_dispo += $chambres[$chambre_id]->_nb_lits_dispo;
      $totalLits += $chambres[$chambre_id]->_nb_lits_dispo;
    }
  } else {
    $services[$service_id]->_vwService = 0;
  }
}

// Récupération des admissions à affecter
function loadSejourNonAffectes($where) {
  global $listPats, $g;
  
  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
    "patients"        => "sejour.patient_id = patients.patient_id"
  );
  $where["sejour.group_id"] = "= '$g'";
  $where[] = "affectation.affectation_id IS NULL";
  $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  
  $sejourNonAffectes = new CSejour;
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, $order, null, null, $leftjoin);

  foreach ($sejourNonAffectes as $keySejour => $valSejour) {
    $sejour =& $sejourNonAffectes[$keySejour];

    $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
     
    // Chargement optimisé du patient
    if (array_key_exists($sejour->patient_id, $listPats)) {
      $sejour->_ref_patient =& $listPats[$sejour->patient_id];
    } else {
      $sejour->loadRefPatient();
      $listPats[$sejour->patient_id] =& $sejour->_ref_patient;
    }

    // Chargement des opérations
    $sejour->loadRefsOperations();
    foreach($sejour->_ref_operations as $keyOp => $valueOp) {
      $operation =& $sejour->_ref_operations[$keyOp];
      $operation->loadRefCCAM();
    }
  }
  
  return $sejourNonAffectes;
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDateTime("+7 days", $today);
$where = array(
  "entree_prevue" => "BETWEEN '$today' AND '$endWeek'",
  "type" => "!= 'exte'",
  "annule" => "= 0"
);
$where["sejour.group_id"] = "= '$g'";
$where[] = "affectation.affectation_id IS NULL";

$leftjoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$select = "count(sejour.sejour_id) AS total";  
$table = "sejour";

$sql = new CRequest();
$sql->addTable($table);
$sql->addSelect($select);
$sql->addWhere($where);
$sql->addLJoin($leftjoin);

$alerte = db_loadResult($sql->getRequest());

$groupSejourNonAffectes = array();

if($canEdit) {
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
  $where = array(
    "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
    "type" => "!= 'exte'",
    "annule" => "= 0"
  );

  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where);
  
  // Admissions du matin
  $where = array(
    "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
    "type" => "!= 'exte'",
    "annule" => "= 0"
  );
  
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where);
  
  // Admissions du soir
  $where = array(
    "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
    "type" => "!= 'exte'",
    "annule" => "= 0"
  );
  
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where);
  
  // Admissions antérieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where = array(
    "annule" => "= 0",
    "'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue"
  );
  
  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where);
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->debugging = false;
$smarty->assign("vwService"             , $vwService);
$smarty->assign("pathos"                , $pathos);
$smarty->assign("date"                  , $date );
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("services"              , $services);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->display("vw_affectations.tpl");
?>