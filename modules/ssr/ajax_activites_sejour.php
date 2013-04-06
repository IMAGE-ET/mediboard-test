<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

CPrescriptionLine::$contexte_recent_modif = 'ssr';

// Sejour SSR
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

$date = CValue::getOrSession("date", CMbDT::date());

$monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
$sunday = CMbDT::date("next sunday", $date);

for ($i = 0; $i < 7; $i++) {
  $week_days[$i] = CMbDT::transform("+$i day", $monday, "%a");
}

// Prescription
$prescription = $sejour->loadRefPrescriptionSejour();
$lines_by_cat = $prescription ? 
  $prescription->loadRefsLinesElementByCat("0", "1", "kine") :
  array();

// Prescription lines and CdARRs
$categories = array();
foreach ($lines_by_cat as $chapter => $_lines_by_chap) {
  foreach ($_lines_by_chap as $_lines_by_cat) {
    foreach ($_lines_by_cat['element'] as $_line) {
      $element = $_line->_ref_element_prescription;
      $category = $element->_ref_category_prescription;
      if (!array_key_exists($category->_id, $categories)){
        $categories[$category->_id] = $category;
      }
      
      $element->loadBackRefs("cdarrs");
      $element->_ref_cdarrs_by_type = array();
      
      $cdarrs_by_type =& $element->_ref_cdarrs_by_type;
      foreach ($element->_back["cdarrs"] as $_acte_cdarr){
        $_activite_cdarr = $_acte_cdarr->loadRefActiviteCdARR();
        $cdarrs_by_type[$_activite_cdarr->type][] = $_acte_cdarr;
      }
    }
  }
}

// Creation d'un nouveau tableau pour stocker les lignes par elements de prescription
$lines_by_element = array(); 
foreach ($lines_by_cat as $chap => $_lines_by_chap){
  foreach ($_lines_by_chap as $cat => $_lines_by_cat){
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
$technicien = new CTechnicien;
$plateau = new CPlateauTechnique;
if ($technicien->_id = $bilan->technicien_id) {
  $technicien->loadMatchingObject();
  $plateau = $technicien->loadFwdRef("plateau_id");
  $plateau->loadRefsEquipements();
  $plateau->loadRefsTechniciens();
}

// Chargement de tous les plateaux et des equipements et techniciens associés
$plateau_tech = new CPlateauTechnique();
$plateau_tech->group_id = CGroups::loadCurrent()->_id;
$plateaux = $plateau_tech->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $_plateau->loadRefsEquipements();
}

$executants = array();

// Chargement des executants en fonction des category de prescription
$executants = array();
$reeducateurs = array();
$selected_cat = "";
$user = CMediusers::get();
foreach ($categories as $_category) {
  // Chargement des associations pour chaque catégorie
  $associations[$_category->_id] = $_category->loadBackRefs("functions_category");
    
  // Parcours des associations trouvées et chargement des utilisateurs
  foreach ($associations[$_category->_id] as $_association) {
    $function = $_association->loadRefFunction();
    $function->loadRefsUsers();
    foreach($function->_ref_users as $_user){
      $_user->_ref_function = $function;
       if ($_user->_id == $user->_id && !$selected_cat){
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
$smarty->assign("evenement", $evenement);
$smarty->assign("week_days", $week_days);
$smarty->assign("sejour" , $sejour);
$smarty->assign("bilan"  , $bilan);
$smarty->assign("plateau", $plateau);
$smarty->assign("prescription", $prescription);
$smarty->assign("plateaux", $plateaux);
$smarty->assign("executants", $executants);
$smarty->assign("reeducateurs", $reeducateurs);
$smarty->assign("selected_cat", $selected_cat);
$smarty->assign("user", $user);
$smarty->assign("lines_by_element", $lines_by_element);
$smarty->display("inc_activites_sejour.tpl");

?>
