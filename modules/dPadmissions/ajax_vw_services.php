<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;
$can->needsRead();

// Chargement su séjour s'il y en a un
$sejour = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$service = $sejour->service_mutation_id;

// Chargement des services
$order = "nom";
$services = $service->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("service", $service);
$smarty->assign("services", $services);

$smarty->display("inc_vw_services.tpl");

?>