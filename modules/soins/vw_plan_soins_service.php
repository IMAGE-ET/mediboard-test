<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::getOrSession("service_id");
$date       = CValue::getOrSession("date", mbDate());
$nb_decalage   = CValue::get("nb_decalage", 2);
$date_max   = mbDate("+ 1 DAY", $date);
 
// Chargement du service
$service = new CService();
$service->load($service_id);

// Chargement des sejours pour le service selectionné
$affectation = new CAffectation();

$ljoin = array();
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
  
$where = array();

$where[] = "'$date' <= affectation.sortie && '$date_max' >= affectation.entree";
$where["service.service_id"] = " = '$service_id'";

$affectations = $affectation->loadList($where, null, null, null, $ljoin);

CMbObject::massLoadFwdRef($affectations, "sejour_id");

foreach($affectations as $_affectation){
  $_affectation->loadView();
  
  $sejour = $_affectation->loadRefSejour(1);
  $sejour->_ref_current_affectation = $_affectation;
}

$sorter = CMbArray::pluck($affectations, "_ref_lit", "_view");
array_multisort($sorter, SORT_ASC, $affectations);
$sejours = CMbArray::pluck($affectations, "_ref_sejour");

$sejours_id = CMbArray::pluck($sejours, "_id");

/*
 * Chargement des elements prescrit pour ces sejours
 */

// Chargement des elements de prescription
$element = new CElementPrescription();
$ljoin = array();
$ljoin["prescription_line_element"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id AND prescription.type = 'sejour'";
$ljoin["sejour"] = "sejour.sejour_id = prescription.object_id AND prescription.object_class = 'CSejour'";

$where = array();
$where["sejour.sejour_id"] = CSQLDataSource::prepareIn($sejours_id);

$elements = $element->loadList($where, null, null, null, $ljoin);

CMbObject::massLoadFwdRef($elements, "category_prescription_id");

// Chargement des catégories des elements
$categories = array();
foreach($elements as $_element){
	$_element->loadRefCategory();
	$_category = $_element->_ref_category_prescription;
	$categories[$_category->chapitre][$_category->_id][$_element->_id] = $_element;
}

// Chargement de la liste des services
$services = $service->loadListWithPerms();
 
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("service", $service);
$smarty->assign("categories", $categories);
$smarty->assign("date", $date);
$smarty->assign("nb_decalage", $nb_decalage);
$smarty->assign("date", $date);
$smarty->assign("services", $services);
$smarty->display('vw_plan_soins_service.tpl');
