<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkRead();
$kine_id = CValue::get("kine_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("kine_id", $kine_id);
$smarty->display("print_planning_technicien.tpl");

?>