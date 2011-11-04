<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ressource_soin_id = CValue::get("ressource_soin_id");
CValue::setSession("ressource_soin_id", $ressource_soin_id);

$ressource_soin = new CRessourceSoin;
$ressource_soin->load($ressource_soin_id);

$smarty = new CSmartyDP;

$smarty->assign("ressource_soin", $ressource_soin);

$smarty->display("inc_form_ressource.tpl");

?>