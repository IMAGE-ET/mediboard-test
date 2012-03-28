<?php /* $Id: httpreq_vw_main_courante.php 14578 2012-02-07 16:08:46Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 14578 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->display("inc_uhcd.tpl");

?>