<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;

$can->needsRead();

// Récupération des dates
$date = CValue::getOrSession("date", mbDate());
$service_id = CValue::getOrSession("service_id");

// Initialisation
$sejour = new CSejour();
$sejours = array();

// Chargement des services
$service = new CService();
$services = $service->loadList(null, "nom");

// Récupération des sorties du jour
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

// ljoin pour filtrer par le service
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";

if($service_id){
  $where["service.service_id"] = " = '$service_id'";
}
$order = "service.nom, sejour.entree_reelle";
$where["sortie_prevue"] = "BETWEEN '$limit1' AND '$limit2'";
$where["type"] = " = 'ambu'";
$where["sejour.annule"] = " = '0'";
$where["sejour.group_id"] = " = '$g'";
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

foreach($sejours as $key => $_sejour){
  $_sejour->loadRefPatient();
  $_sejour->loadRefPraticien();
  $_sejour->loadRefsAffectations("sortie ASC");
  $_sejour->loadRefsOperations();
  $affectation =& $_sejour->_ref_last_affectation;
  if($affectation->affectation_id){
  	$affectation->loadReflit();
  	$affectation->_ref_lit->loadCompleteView();
  }
  foreach($_sejour->_ref_affectations as $key => $affect){
    $affect->loadRefLit();
    $affect->_ref_lit->loadCompleteView();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("service_id", $service_id);
$smarty->assign("sejours", $sejours);
$smarty->assign("services", $services);
$smarty->assign("date", $date);
$smarty->display("print_ambu.tpl");

?>