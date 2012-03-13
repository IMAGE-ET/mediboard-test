<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Séjour concernés
$rhs = new CRHS();
$rhs->load(CValue::get("rhs_id"));
if (!$rhs->_id) {
  CAppUI::stepAjax("RHS inexistant", UI_MSG_ERROR);
}
$rhs->loadRefsNotes();
$rhs->buildTotaux();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rhs", $rhs);

$smarty->display("inc_totaux_rhs.tpl");

?>