<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$date = CValue::getOrSession("date", CMbDT::date());

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->display("vw_idx_identito_vigilance.tpl");

?>