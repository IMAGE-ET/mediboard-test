<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkEdit();

$endowment_id = CValue::get('endowment_id');

$endowment = new CProductEndowment();
$endowment->load($endowment_id);

$group = new CGroups();

/** @var CGroups[] $groups */
$groups = $group->loadListWithPerms();

foreach ($groups as $_group) {
  $_group->loadRefsServices();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('endowment', $endowment);
$smarty->assign('groups', $groups);

$smarty->display('inc_duplicate_endowment.tpl');
