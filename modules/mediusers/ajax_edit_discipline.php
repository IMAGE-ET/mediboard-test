<?php
/**
 * Edit discipline
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$discipline_id = CValue::getOrSession("discipline_id");

// Récupération des groups
$groups = CGroups::loadGroups(PERM_EDIT);

// Récupération de la fonction selectionnée
$discipline = new CDiscipline();
$discipline->load($discipline_id);
$discipline->loadGroupRefsBack();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("discipline", $discipline);
$smarty->assign("groups"  , $groups);

$smarty->display("inc_edit_discipline.tpl");