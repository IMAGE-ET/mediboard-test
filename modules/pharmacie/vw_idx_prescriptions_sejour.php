<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can, $g;
$can->needsRead();

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

// Chargement de la liste des services
$service = new CService();
$services = $service->loadGroupList();

// Recuperation des valeurs
$praticien_id  = CValue::get("praticien_id");
$service_id    = CValue::get("service_id");
$valide_pharma = CValue::get("valide_pharma", 0);  // Par defaut, seulement les prescriptions contenant des lignes non validees

$date = mbDate();
$filter_sejour = new CSejour();
$filter_sejour->_date_entree = CValue::get('_date_entree', CValue::session('_date_min', $date));
$filter_sejour->_date_sortie = CValue::get('_date_sortie', CValue::session('_date_max', $date));

CValue::setSession('_date_min', $filter_sejour->_date_entree);
CValue::setSession('_date_max', $filter_sejour->_date_sortie);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("valide_pharma", $valide_pharma);
$smarty->assign("today", mbDate());
$smarty->assign("mode_pharma", "1");
$smarty->assign("prescription", new CPrescription());
$smarty->assign("filter_sejour", $filter_sejour);
$smarty->assign("filter_line_med", new CPrescriptionLineMedicament());
$smarty->assign("contexteType", "");
$smarty->assign("praticiens", $praticiens);
$smarty->assign("services", $services);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("service_id", $service_id);
$smarty->display('vw_idx_prescriptions_sejour.tpl');

?>