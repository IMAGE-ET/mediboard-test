<?php /* $Id: vw_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prestation_id = CValue::getOrSession("prestation_id");
$object_class  = CValue::getOrSession("object_class", "CPrestationPonctuelle");

$smarty = new CSmartyDP;

$smarty->assign("prestation_id", $prestation_id);
$smarty->assign("object_class", $object_class);

$smarty->display("vw_prestations.tpl");

?>