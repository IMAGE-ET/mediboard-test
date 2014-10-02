<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$categories_id = CValue::getOrSession("categories_id");
$service_id    = CValue::getOrSession("service_id");
$date          = CValue::getOrSession("date");
$real_time     = CValue::getOrSession("real_time", 0);
$nb_decalage   = CValue::get("nb_decalage");
$date_max      = CMbDT::date("+ 1 DAY", $date);

// Chargement du service
$service = new CService();
$service->load($service_id);

// Si le service en session n'est pas dans l'etablissement courant
if (CGroups::loadCurrent()->_id != $service->group_id) {
  $service_id = "";
  $service = new CService();
}

// Chargement des configs de services
if (!$service_id) {
  $service_id = "none";
}
$configs = CConfigService::getAllFor($service_id);

// Si la date actuelle est inf�rieure a l'heure affich�e sur le plan de soins, on affiche le plan de soins de la veille
$datetime_limit = CMbDT::dateTime($configs["Poste 1"].":00:00");

if (!$date) {
  if (CMbDT::dateTime() < $datetime_limit) {
    $date = CMbDT::date("- 1 DAY", $date);
  }
  else {
    $date = CMbDT::date();
  }
}

if (!$nb_decalage) {
  $nb_decalage = $configs["Nombre postes avant"];
}

// Chargement des sejours pour le service selectionn�
$affectation = new CAffectation();

$where = array();
if ($real_time) {
  $time = CMbDT::time();
  $where[] = "'$date $time' <= affectation.sortie && '$date $time' >= affectation.entree";
}
else {
  $where[] = "'$date' <= affectation.sortie && '$date_max' >= affectation.entree";
}
$where["affectation.service_id"] = " = '$service_id'";

$affectations = $affectation->loadList($where);

CMbObject::massLoadFwdRef($affectations, "sejour_id");

foreach ($affectations as $_affectation) {
  $_affectation->loadRefLit()->loadCompleteView();
  $_affectation->_view = $_affectation->_ref_lit->_view;
  
  $sejour = $_affectation->loadRefSejour(1);
  $sejour->_ref_current_affectation = $_affectation;
}

$sorter = CMbArray::pluck($affectations, "_ref_lit", "_view");
array_multisort($sorter, SORT_ASC, $affectations);
$sejours = CMbArray::pluck($affectations, "_ref_sejour");

$sejours_id = CMbArray::pluck($sejours, "_id");

/*
 * Chargement des elements prescrits pour ces sejours
 */

// Chargement des elements de prescription
$element = new CElementPrescription();
$ljoin = array();
$ljoin["prescription_line_element"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id AND prescription.type = 'sejour'";
$ljoin["sejour"] = "sejour.sejour_id = prescription.object_id AND prescription.object_class = 'CSejour'";

$where = array();
$where["sejour.sejour_id"] = CSQLDataSource::prepareIn($sejours_id);
$where["prescription_line_element.active"] = " = '1'";

$elements = $element->loadList($where, null, null, "element_prescription_id", $ljoin);

CMbObject::massLoadFwdRef($elements, "category_prescription_id");

// Chargement des cat�gories des elements
$categories = array();
$categories_by_names = array();
foreach ($elements as $_element) {
  $_element->loadRefCategory();
  $_category = $_element->_ref_category_prescription;
  $categories[$_category->chapitre][$_category->_id][$_element->_id] = $_element;
  $categories_by_names[$_category->chapitre][$_category->nom] = $_category->_id;
}

// Tri par chapitre
$sorted_category = array('med', 'med_elt', 'anapath', 'biologie', 'imagerie', 'consult', 'kine', 'soin', 'dm', 'dmi', 'ds');
$categories = CMbArray::ksortByArray($categories, $sorted_category);

// Tri par cat�gorie
foreach ($categories_by_names as $key => $category) {
  ksort($category);
  $categories[$key] = CMbArray::ksortByArray($categories[$key], $category);
}

// R�cup�ration de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$_service = new CService();
$services = $_service->loadGroupList($where);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("service"      , $service);
$smarty->assign("categories"   , $categories);
$smarty->assign("date"         , $date);
$smarty->assign("nb_decalage"  , $nb_decalage);
$smarty->assign("services"     , $services);
$smarty->assign("categories_id", $categories_id);
$smarty->assign('real_time'    , $real_time);
$smarty->assign('day'          , CMbDT::date());
$smarty->display('vw_plan_soins_service.tpl');