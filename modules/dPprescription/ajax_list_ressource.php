<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ressource_soin_id = CValue::getOrSession("ressource_soin_id");
$ressource_soin = new CRessourceSoin();
$ressources_soins = $ressource_soin->loadList();

$smarty = new CSmartyDP;
$smarty->assign("ressources_soins", $ressources_soins);
$smarty->assign("ressource_soin_id", $ressource_soin_id);
$smarty->display("inc_list_ressources.tpl");

?>