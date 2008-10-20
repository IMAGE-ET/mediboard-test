<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsAdmin();

$service_id = mbGetValueFromGetOrSession('service_id');

// Services list
$service = new CService();
$service->group_id = $g;
$list_services = $service->loadMatchingList('nom');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('service_id', $service_id);
$smarty->assign('list_services', $list_services);

$smarty->display('vw_idx_discrepancy.tpl');

?>