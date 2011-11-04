<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ressource_soin_id = CValue::getOrSession("ressource_soin_id");

$smarty = new CSmartyDP;
$smarty->assign("ressource_soin_id", $ressource_soin_id);
$smarty->display("vw_edit_ressources.tpl");

?>