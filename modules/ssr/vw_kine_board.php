<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkEdit();

$date    = CValue::get("date", CMbDT::date());
$kine_id = CValue::getOrSession("kine_id", CAppUI::$instance->user_id);

// Chargement de la liste des utilisateurs
$mediuser = new CMediusers();
$kines = CFunctionCategoryPrescription::getAllExecutants();

$kine = new CMediusers();
$kine->load($kine_id);
$kine->loadRefIntervenantCdARR();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("kine", $kine);
$smarty->assign("kines", $kines);
$smarty->assign("kine_id", $kine_id);
$smarty->display("vw_kine_board.tpl");

?>