<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage PlanningOp
 * @version $Revision$
 * @author OpenXtrem
 */

CCanDo::checkAdmin();

$mode_class = CValue::get("mode_class");
$mode_id    = CValue::get("mode_id");

if (!in_array($mode_class, array("CModeEntreeSejour", "CModeSortieSejour"))) {
  throw new CMbException("Invalid class: '$mode_class'");
}

$mode = new $mode_class;
$mode->load($mode_id);

$mode->loadRefsNotes();
if (!$mode->_id) {
  $mode->group_id = CGroups::loadCurrent()->_id;
}

$smarty = new CSmartyDP();
$smarty->assign("mode", $mode);
$smarty->display("inc_edit_mode_entree_sortie.tpl");
