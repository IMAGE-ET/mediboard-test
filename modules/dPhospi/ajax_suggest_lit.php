<?php /* $Id: ajax_suggest_lit.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id       = CValue::get("affectation_id");
$_link_affectation    = CValue::get("_link_affectation", 0);
$services_ids_suggest = CValue::get("services_ids_suggest");
$datetime             = CValue::get("datetime");

if (!$datetime) {
  $datetime = CMbDT::dateTime();
}

$entree = $datetime;
$sortie = null;
$lit_id = null;

$affectation = new CAffectation();
$affectation->load($affectation_id)->loadRefLit()->loadRefChambre();

if (!$services_ids_suggest) {
  $services_ids_suggest = array($affectation->_ref_lit->_ref_chambre->service_id);
}
else {
  $services_ids_suggest = explode(",", $services_ids_suggest);
}

$sortie = $affectation->sortie;

$where = array();
$where["chambre.service_id"] = CSQLDataSource::prepareIn($services_ids_suggest);

$ljoin = array();
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";

$lit = new CLit();
$lits = $lit->loadList($where, null, null, null, $ljoin);

//unset($lits[$affectation->lit_id]);

$max_entree = 0;
$max_sortie = 0;

$ds = $lit->getDS();

foreach ($lits as $key => $_lit) {
  
  $_lit->_ref_affectations = array();
  $_lit->loadCompleteView();
  
  if ($_lit->_id == $affectation->lit_id) {
    
    $_lit->_ref_last_dispo = new CAffectation();
    $_lit->_ref_last_dispo->sortie = $entree;
    $_lit->_dispo_depuis = 0;
  }
  else {
    $where = array();
    $where["lit_id"] = "= '$_lit->_id'";
    $where["entree"] = "<= '$sortie'";
    $where["sortie"] = ">= '$entree'";
    
    $affectation_collide = new CAffectation();
    $affectation_collide->loadObject($where);
    
    if ($affectation_collide->_id) {
      unset($lits[$key]);
      continue;
    }
    $where = array(
      "lit_id" => "= '$_lit->_id'",
      "sortie" => "<= '$entree'");
    $_lit->_ref_last_dispo = new CAffectation();
    $_lit->_ref_last_dispo->loadObject($where, "sortie DESC");
    
    $_lit->_dispo_depuis = strtotime($entree) - strtotime($_lit->_ref_last_dispo->sortie);
    if ($_lit->_dispo_depuis < 0) {
      unset($lits[$key]);
      continue;
    }
    
    if ($_lit->_ref_last_dispo->_id && $_lit->_dispo_depuis > $max_entree) {
      $max_entree = $_lit->_dispo_depuis;
    }
  }

  // Sexe de l'autre patient présent dans la chambre
  $sql = "SELECT sexe
          FROM affectation
          LEFT JOIN lit ON lit.lit_id = affectation.lit_id
          LEFT JOIN sejour ON sejour.sejour_id = affectation.sejour_id
          LEFT JOIN patients ON patients.patient_id = sejour.patient_id
          WHERE lit.chambre_id = '$_lit->chambre_id'
          AND lit.lit_id != '$_lit->_id'
          AND '$datetime' BETWEEN affectation.entree AND affectation.sortie";
  $_lit->_sexe_other_patient = $ds->loadResult($sql);

  $where = array(
      "lit_id" => "= '$_lit->_id'",
      "entree" => " >= '$sortie'");
  $_lit->_ref_next_dispo = new CAffectation();
  $_lit->_ref_next_dispo->loadObject($where, "entree ASC");
  
  $_lit->_dispo_depuis_friendly = CMbDate::relative($_lit->_ref_last_dispo->sortie, $entree);
  
  if ($_lit->_ref_next_dispo->entree) {
    $_lit->_occupe_dans = strtotime($_lit->_ref_next_dispo->entree) - strtotime($sortie);
    
    if ($_lit->_occupe_dans < 0) { 
      unset($lits[$key]);
      continue;
    }
    
    if ($max_sortie < $_lit->_occupe_dans) {
      $max_sortie = $_lit->_occupe_dans;
    }
    
    $_lit->_occupe_dans_friendly = CMbDate::relative($sortie, $_lit->_ref_next_dispo->entree); 
  }
  else {
    $_lit->_occupe_dans = "libre";
  }
}

$sorter = CMbArray::pluck($lits, "_dispo_depuis");
array_multisort($sorter, SORT_ASC, $lits);

$smarty = new CSmartyDP();

$smarty->assign("lits", $lits);
$smarty->assign("affectation_id", $affectation_id);
$smarty->assign("max_entree", $max_entree);
$smarty->assign("max_sortie", $max_sortie);
$smarty->assign("_link_affectation", $_link_affectation);
$smarty->assign("services_ids_suggest", $services_ids_suggest);

$smarty->display("inc_suggest_lit.tpl");
