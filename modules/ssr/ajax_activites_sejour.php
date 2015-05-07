<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();

$sejour_id = CValue::get("sejour_id");
$date      = CValue::getOrSession("date", CMbDT::date());

// Initialisation de la variable permettant de ne pas passer par les alertes manuelles
if (CModule::getActive("dPprescription")) {
  CPrescriptionLine::$contexte_recent_modif = 'ssr';
}

// Sejour SSR
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();

$monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
$sunday = CMbDT::date("next sunday", $date);

$week_days = array();
for ($i = 0; $i < 7; $i++) {
  $week_days[$i] = CMbDT::transform("+$i day", $monday, "%a");
}

// Prescription
$prescription = $sejour->loadRefPrescriptionSejour();
$lines_by_cat = $prescription ? 
  $prescription->loadRefsLinesElementByCat("0", "1", "kine") :
  array();

// Prescription lines for SSR codes
/** @var CCategoryPrescription[] $categories */
$categories = array();
foreach ($lines_by_cat as $chapter => $_lines_by_chap) {
  foreach ($_lines_by_chap as $_lines_by_cat) {
    /** @var CPrescriptionLineElement $_line */
    foreach ($_lines_by_cat['element'] as $_line) {
      $element = $_line->_ref_element_prescription;
      $category = $element->_ref_category_prescription;
      
      // All categories
      if (!array_key_exists($category->_id, $categories)) {
        $categories[$category->_id] = $category;
      }
      
      // SSR Codes
      $element->loadRefsCsarrs();
    }
  }
}

// Creation d'un nouveau tableau pour stocker les lignes par elements de prescription
$lines_by_element = array(); 
foreach ($lines_by_cat as $chap => $_lines_by_chap) {
  foreach ($_lines_by_chap as $cat => $_lines_by_cat) {
    foreach ($_lines_by_cat['element'] as $line_id => $_line) {
      $lines_by_element[$chap][$cat][$_line->element_prescription_id][$_line->_id] = $_line;
    }
  }
}

// Bilan
$bilan = $sejour->loadRefBilanSSR();
$technicien = $bilan->loadRefTechnicien();
$technicien->loadRefKine();
$technicien->loadRefPlateau();

// Au cas où le bilan n'existe pas encore
$bilan->sejour_id = $sejour->_id;

// Technicien et plateau
$technicien = new CTechnicien();
$plateau = new CPlateauTechnique();
if ($technicien->_id = $bilan->technicien_id) {
  $technicien->loadMatchingObject();
  /** @var CPlateauTechnique $plateau */
  $plateau = $technicien->loadRefPlateau();
  $plateau->loadRefsEquipements();
  $plateau->loadRefsTechniciens();
}

// Chargement de tous les plateaux et des equipements et techniciens associés
$plateau_tech = new CPlateauTechnique();
$plateau_tech->group_id = CGroups::loadCurrent()->_id;
/** @var CPlateauTechnique[] $plateaux */
$plateaux = $plateau_tech->loadMatchingList();
CMbObject::massLoadBackRefs($plateaux, "equipements", "nom ASC");

foreach ($plateaux as $_plateau) {
  $_plateau->loadRefsEquipements();
}

// Chargement des executants en fonction des category de prescription
$executants = array();
$reeducateurs = array();
$selected_cat = "";
$user = CMediusers::get();
$func_cats = CStoredObject::massLoadBackRefs($categories, "functions_category");
CStoredObject::massLoadFwdRef($func_cats, "function_id");

foreach ($categories as $_category) {
  // Chargement des associations pour chaque catégorie
  $associations[$_category->_id] = $_category->loadBackRefs("functions_category");
    
  // Parcours des associations trouvées et chargement des utilisateurs
  /** @var CFunctionCategoryPrescription $_association */
  foreach ($associations[$_category->_id] as $_association) {
    $function = $_association->loadRefFunction();
    $function->loadRefsUsers();
    foreach ($function->_ref_users as $_user) {
      $_user->_ref_function = $function;
      if ($_user->_id == $user->_id && !$selected_cat) {
        $selected_cat = $_category;
      }
      $executants[$_category->_id][$_user->_id] = $_user;
      $reeducateurs[$_user->_id] = $_user;
    }
  }
}

// Executants hors exécutant de prescription
if (!$prescription) {
  $executants = $user->loadKines();
}

$evenement = new CEvenementSSR();
$evenement->duree = CAppUI::pref("ssr_planification_duree");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("evenement"       , $evenement);
$smarty->assign("week_days"       , $week_days);
$smarty->assign("sejour"          , $sejour);
$smarty->assign("bilan"           , $bilan);
$smarty->assign("plateau"         , $plateau);
$smarty->assign("prescription"    , $prescription);
$smarty->assign("plateaux"        , $plateaux);
$smarty->assign("executants"      , $executants);
$smarty->assign("reeducateurs"    , $reeducateurs);
$smarty->assign("selected_cat"    , $selected_cat);
$smarty->assign("user"            , $user);
$smarty->assign("lines_by_element", $lines_by_element);

$smarty->display("inc_activites_sejour.tpl");
