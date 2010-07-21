<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date    = CValue::getOrSession("date", mbDate());
$kine_id = CValue::getOrSession("kine_id");

$mediuser = new CMediusers();
$mediuser->load($kine_id);

$planning = new CPlanningWeek($date);

// Sejour SSR
$sejour = new CSejour;

$ds = CSQLDataSource::get("std");

// Sejours pour lesquels le kine est référent
$join = array();
$join["bilan_ssr"]  = "bilan_ssr.sejour_id = sejour.sejour_id";
$join["technicien"] = "technicien.technicien_id = bilan_ssr.technicien_id";
$where = array();
$where["sejour.type"  ] = "= 'ssr'";
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["sejour.annule"] = "= '0'";
$where["technicien.kine_id"] = "= '$kine_id'";
$sejours["referenced"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lesquels le kine est remplaçant
$join = array();
$join["replacement"]  = "replacement.sejour_id = sejour.sejour_id";
$where = array();
$where["sejour.type"  ] = "= 'ssr'";
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["sejour.annule"] = "= '0'";
$where["replacement.replacement_id"] = "IS NOT NULL";
$where["replacement.replacer_id"] = " = '$kine_id'";
$sejours["replaced"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lesquels le rééducateur a des événements
$join["evenement_ssr"]  = "evenement_ssr.sejour_id = sejour.sejour_id";
$where = array();
$where["sejour.type"  ] = "= 'ssr'";
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["sejour.annule"] = "= '0'";
$where["evenement_ssr.therapeute_id"] = "= '$kine_id'";

$sejours["planned"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lesquels le rééducateur est exécutant pour des lignes prescrites mais n'a pas encore d'evenement planifiés
$sejours["plannable"] = array();

// Séjours élligibles
$where = array();
$where["sejour.type"  ] = "= 'ssr'";
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["sejour.annule"] = "= '0'";
$sejour_ids = $sejour->loadIds($where);

// Identifiants de catégorie de prescriptions disponibles
$mediuser->loadRefFunction();
$function =& $mediuser->_ref_function;
$executants = $function->loadBackRefs("executants_prescription");
$category_ids = CMbArray::pluck($executants, "category_prescription_id");

// Recherche des lignes de prescriptions executables
$line = new CPrescriptionLineElement;
$join = array();
$where = array();
$join["element_prescription"] = "element_prescription.element_prescription_id = prescription_line_element.element_prescription_id";
$where["element_prescription.category_prescription_id"] = $ds->prepareIn($category_ids);
$join["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id";
$where["prescription.type"] = "= 'sejour'";
$where["prescription.object_class"] = "= 'CSejour'";
$where["prescription.object_id"] = $ds->prepareIn($sejour_ids);
$line_ids = $line->loadIds($where, null, null, null, $join);

// Prescriptions exécutables
$query = new CRequest;
$query->addSelect("DISTINCT prescription_id");
$query->addTable("prescription_line_element");
$query->addWhereClause("prescription_line_element_id", $ds->prepareIn($line_ids));
$prescription_ids = $ds->loadColumn($query->getRequest());

// Séjours planifiables
$query = new CRequest;
$query->addSelect("DISTINCT object_id");
$query->addTable("prescription");
$query->addWhereClause("prescription_id", $ds->prepareIn($prescription_ids));
$sejour_ids = $ds->loadColumn($query->getRequest());

$where = array();
$where["sejour_id"] = $ds->prepareIn($sejour_ids);
$sejours["plannable"] = $sejour->loadList($where);

// Chargement des détails affichés de chaque séjour
foreach ($sejours as &$_sejours) {
  foreach ($_sejours as $_sejour) {
    $_sejour->loadRefPatient();
    $_sejour->countEvenementsSSRWeek($kine_id, $planning->date_min, $planning->date_max);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_board_sejours.tpl");

?>