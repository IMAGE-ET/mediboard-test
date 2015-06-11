<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$sejour_id     = CValue::get("sejour_id");
$context       = CValue::get("context", "all");
$relative_date = CValue::get("relative_date");

$sejour = new CSejour();
$sejour->load($sejour_id);

$prestations_j = CPrestationJournaliere::loadCurrentList($sejour->type);
$dates         = array();
$prestations_p = array();
$liaisons_j    = array();
$liaisons_p    = array();
$date_modif    = array();

CStoredObject::massLoadBackRefs($prestations_j, "items", "rank");
foreach ($prestations_j as $_prestation) {
  $items = $_prestation->loadRefsItems();
  CStoredObject::massLoadBackRefs($items, "sous_items", "nom");
  foreach ($items as $_item) {
    $_item->loadRefsSousItems();
  }
}

$sejour->loadRefPrescriptionSejour();
$sejour->loadRefCurrAffectation();
$sejour->loadRefsOperations();

$dossier_medical_sejour = $sejour->loadRefDossierMedical();
$dossier_medical_sejour->loadRefsAntecedents();

$patient = $sejour->loadRefPatient();
$patient->loadRefPhotoIdentite();
$patient->loadRefLatestConstantes();

$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsAllergies();
$dossier_medical->countAntecedents();
$dossier_medical->countAllergies();

//droits d'édition
$editRights = CModule::getCanDo("dPhospi")->edit;

$duree = strtotime($sejour->sortie) - strtotime($sejour->entree);
$affectations = $sejour->loadRefsAffectations();

$date_temp = CMbDT::date($sejour->entree);

while ($date_temp <= CMbDT::date($sejour->sortie)) {
  if (!isset($dates[$date_temp])) {
    $dates[$date_temp] = 0;
  }
  $date_temp = CMbDT::date("+1 day", $date_temp);
}

if (count($affectations)) {
  $lits = CMbObject::massLoadFwdRef($affectations, "lit_id");
  CMbObject::massLoadFwdRef($lits, "chambre_id");
  foreach ($affectations as $_affectation) {
    $_affectation->loadRefLit()->loadCompleteView();
    $_affectation->_rowspan = CMbDT::daysRelative($_affectation->entree, $_affectation->sortie)+1;
    $date_temp = CMbDT::date($_affectation->entree);
  
    while ($date_temp <= CMbDT::date($_affectation->sortie)) {
      $dates[$date_temp] = $_affectation->_id;
      $date_temp = CMbDT::date("+1 day", $date_temp);
    }
  }
}

// Gestion des liaisons hors séjour
$dates_after = array();

/** @var CItemLiaison[] $items_liaisons */
$items_liaisons = $sejour->loadBackRefs("items_liaisons");
CMbObject::massLoadFwdRef($items_liaisons, "item_souhait_id");
CMbObject::massLoadFwdRef($items_liaisons, "item_realise_id");
CMbObject::massLoadFwdRef($items_liaisons, "sous_item_id");

foreach ($items_liaisons as $_item_liaison) {
  if ($_item_liaison->date > CMbDT::date($sejour->sortie)) {
    $dates_after[] = CMbDT::date($_item_liaison->date);
  }

  $_item = $_item_liaison->loadRefItem();
  
  $_item_liaison->loadRefItemRealise();
  if (!$_item->_id) {
    $_item = $_item_liaison->_ref_item_realise;
  }

  $_item_liaison->loadRefSousItem();

  switch ($_item->object_class) {
    case "CPrestationJournaliere":
      $liaisons_j[$_item_liaison->date][$_item->object_id] = $_item_liaison;
      break;
    case "CPrestationPonctuelle":
      $liaisons_p[$_item_liaison->date][$_item->object_id][] = $_item_liaison;
      if (!isset($prestations_p[$_item->object_id])) {
        $prestation = new CPrestationPonctuelle();
        $prestation->load($_item->object_id);
        $prestation->_ref_items = $prestation->loadBackRefs("items");
        $prestations_p[$_item->object_id] = $prestation;
      }
  }
}

$date_temp = CMbDT::date($sejour->entree);

while (!isset($liaisons_j[$date_temp]) && $date_temp < CMbDT::date($sejour->sortie)) {
  $date_temp = CMbDT::date("+1 day", $date_temp);
}

$liaisons_j_date =& $liaisons_j[$date_temp];
$save_state = array();

foreach ($prestations_j as $_prestation_id => $_prestation) {
  $item_liaison = new CItemLiaison();
  $item_liaison->_id = "temp";
  $item_liaison->loadRefItem();
  $item_liaison->loadRefItemRealise();
  $item_liaison->loadRefSousItem();

  if (isset($liaisons_j_date[$_prestation_id])) {
    $date_modif[$date_temp] = 1;
    $save_liaison = $liaisons_j_date[$_prestation_id];
    
      $item_liaison->item_souhait_id          = $save_liaison->item_souhait_id;
      $item_liaison->item_realise_id          = $save_liaison->item_realise_id;
      $item_liaison->sous_item_id             = $save_liaison->sous_item_id;
      $item_liaison->_ref_item->_id           = $save_liaison->_ref_item->_id;
      $item_liaison->_ref_item->nom           = $save_liaison->_ref_item->nom;
      $item_liaison->_ref_item->rank          = $save_liaison->_ref_item->rank;
      $item_liaison->_ref_item->color         = $save_liaison->_ref_item->color;
      $item_liaison->_ref_item_realise->_id   = $save_liaison->_ref_item_realise->_id;
      $item_liaison->_ref_item_realise->nom   = $save_liaison->_ref_item_realise->nom;
      $item_liaison->_ref_item_realise->rank  = $save_liaison->_ref_item_realise->rank;
      $item_liaison->_ref_item_realise->color = $save_liaison->_ref_item_realise->color;
      $item_liaison->_ref_sous_item->_id      = $save_liaison->_ref_sous_item->_id;
      $item_liaison->_ref_sous_item->nom      = $save_liaison->_ref_sous_item->nom;
      $item_liaison->_ref_sous_item->item_prestation_id = $save_liaison->_ref_sous_item->item_prestation_id;
    $save_state[$_prestation_id] = $item_liaison;
  }
  else {
    $save_state[$_prestation_id] = $item_liaison;
    $liaisons_j_date[$_prestation_id] = $item_liaison;
  }
}

foreach ($dates as $_date => $_value) {
  if ($_date <= $date_temp) {
    continue;
  }
  if (!isset($liaisons_j[$_date])) {
    $liaisons_j[$_date] = array();
  }
  $liaisons_j_date =& $liaisons_j[$_date];
  
  foreach ($prestations_j as $_prestation_id => $_prestation) {
    $item_liaison = new CItemLiaison();
    $item_liaison->_id = "temp";
    $item_liaison->loadRefItem();
    $item_liaison->loadRefItemRealise();
    $item_liaison->loadRefSousItem();

    if (isset($liaisons_j_date[$_prestation_id])) {
      $date_modif[$_date] = 1;
      $save_liaison = $liaisons_j_date[$_prestation_id];
      
        $item_liaison->item_souhait_id          = $save_liaison->item_souhait_id;
        $item_liaison->item_realise_id          = $save_liaison->item_realise_id;
        $item_liaison->sous_item_id             = $save_liaison->sous_item_id;
        $item_liaison->_ref_item->_id           = $save_liaison->_ref_item->_id;
        $item_liaison->_ref_item->nom           = $save_liaison->_ref_item->nom;
        $item_liaison->_ref_item->rank          = $save_liaison->_ref_item->rank;
        $item_liaison->_ref_item->color         = $save_liaison->_ref_item->color;
        $item_liaison->_ref_item_realise->_id   = $save_liaison->_ref_item_realise->_id;
        $item_liaison->_ref_item_realise->nom   = $save_liaison->_ref_item_realise->nom;
        $item_liaison->_ref_item_realise->rank  = $save_liaison->_ref_item_realise->rank;
        $item_liaison->_ref_item_realise->color = $save_liaison->_ref_item_realise->color;
        $item_liaison->_ref_sous_item->_id      = $save_liaison->_ref_sous_item->_id;
        $item_liaison->_ref_sous_item->nom      = $save_liaison->_ref_sous_item->nom;
        $item_liaison->_ref_sous_item->item_prestation_id = $save_liaison->_ref_sous_item->item_prestation_id;
      $save_state[$_prestation_id] = $item_liaison;
    }
    else {
      $save_liaison = $save_state[$_prestation_id];
      
        $item_liaison->item_souhait_id          = $save_liaison->item_souhait_id;
        $item_liaison->item_realise_id          = $save_liaison->item_realise_id;
        $item_liaison->sous_item_id             = $save_liaison->sous_item_id;
        $item_liaison->_ref_item->_id           = $save_liaison->_ref_item->_id;
        $item_liaison->_ref_item->nom           = $save_liaison->_ref_item->nom;
        $item_liaison->_ref_item->rank          = $save_liaison->_ref_item->rank;
        $item_liaison->_ref_item->color         = $save_liaison->_ref_item->color;
        $item_liaison->_ref_item_realise->_id   = $save_liaison->_ref_item_realise->_id;
        $item_liaison->_ref_item_realise->nom   = $save_liaison->_ref_item_realise->nom;
        $item_liaison->_ref_item_realise->rank  = $save_liaison->_ref_item_realise->rank;
        $item_liaison->_ref_item_realise->color = $save_liaison->_ref_item_realise->color;
        $item_liaison->_ref_sous_item->_id      = $save_liaison->_ref_sous_item->_id;
        $item_liaison->_ref_sous_item->nom      = $save_liaison->_ref_sous_item->nom;
        $item_liaison->_ref_sous_item->item_prestation_id = $save_liaison->_ref_sous_item->item_prestation_id;
      $liaisons_j_date[$_prestation_id] = $item_liaison;
      
    }
  }
}

$empty_liaison = new CItemLiaison();
$empty_liaison->_id = "temp";
$empty_liaison->loadRefItem();
$empty_liaison->loadRefItemRealise();

// La date pour l'ajout d'une prestation ponctuelle doit être dans les dates du séjour
// Si la date actuelle est hors des bornes, alors réinitialisation à la date d'entrée du séjour
$today_ponctuelle = CMbDT::date();
if ($today_ponctuelle < CMbDT::date($sejour->entree) || $today_ponctuelle > CMbDT::date($sejour->sortie)) {
  $today_ponctuelle = CMbDT::date($sejour->entree);
}

$smarty = new CSmartyDP();

$smarty->assign("today"        , CMbDT::date());
$smarty->assign("today_ponctuelle", $today_ponctuelle);
$smarty->assign("dates"        , $dates);
$smarty->assign("dates_after"  , $dates_after);
$smarty->assign("relative_date", $relative_date);
$smarty->assign("sejour"       , $sejour);
$smarty->assign("affectations" , $affectations);
$smarty->assign("prestations_j", $prestations_j);
$smarty->assign("prestations_p", $prestations_p);
$smarty->assign("empty_liaison", $empty_liaison);
$smarty->assign("liaisons_p"   , $liaisons_p);
$smarty->assign("liaisons_j"   , $liaisons_j);
$smarty->assign("date_modified", $date_modif);
$smarty->assign("context"      , $context);
$smarty->assign("editRights"   , $editRights);
$smarty->assign("bank_holidays", CMbDate::getHolidays(CMbDT::date()));

$smarty->display("inc_vw_prestations.tpl");
