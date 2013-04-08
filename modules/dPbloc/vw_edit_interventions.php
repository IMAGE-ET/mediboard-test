<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

if(!($plageop_id = CValue::getOrSession("plageop_id"))) {
  CAppUI::setMsg("Vous devez choisir une plage opératoire", UI_MSG_WARNING);
  CAppUI::redirect("m=dPbloc&tab=vw_edit_planning");
}

// Infos sur la plage opératoire
$plage = new CPlageOp();
$plage->load($plageop_id);
if(!$plage->temps_inter_op) {
  $plage->temps_inter_op = "00:00:00";
}
$plage->loadRefsFwd();
$plage->loadRefChir()->loadRefFunction();
$plage->loadRefAnesth()->loadRefFunction();
$plage->loadRefsNotes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plage", $plage);
$smarty->display("vw_edit_interventions.tpl");

?>