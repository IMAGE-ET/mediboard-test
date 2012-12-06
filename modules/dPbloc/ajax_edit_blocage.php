<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$blocage_id = CValue::getOrSession("blocage_id");

$blocage = new CBlocage;
$blocage->load($blocage_id);

if (!$blocage->_id) {
  $blocage->deb = $blocage->fin = mbDate();
}

$bloc = new CBlocOperatoire();
$where = array("group_id" => "= '".CGroups::loadCurrent()->_id."'");
$blocs = $bloc->loadListWithPerms(PERM_READ, $where, "nom");

foreach ($blocs as $_bloc) {
  $_bloc->loadRefsSalles();
}

$smarty = new CSmartyDP;

$smarty->assign("blocage", $blocage);
$smarty->assign("blocs"  , $blocs);

$smarty->display("inc_edit_blocage.tpl");

?>