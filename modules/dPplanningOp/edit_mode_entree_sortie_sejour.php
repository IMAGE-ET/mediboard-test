<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$mode_class = CValue::get("mode_class");
$mode_id    = CValue::get("mode_id");

if (!in_array($mode_class, array("CModeEntreeSejour", "CModeSortieSejour"))) {
  throw new CMbException("Invalid class: '$mode_class'");
}

/** @var CModeEntreeSejour|CModeSortieSejour $mode */
$mode = new $mode_class;
$mode->load($mode_id);

$mode->loadRefsNotes();
if (!$mode->_id) {
  $mode->group_id = CGroups::loadCurrent()->_id;
}

$smarty = new CSmartyDP();
$smarty->assign("mode", $mode);
$smarty->display("inc_edit_mode_entree_sortie.tpl");
