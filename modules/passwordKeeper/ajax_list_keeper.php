<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

// Recupération de la liste des trousseaux
$keeper = new CPasswordKeeper();
$user   = CMediusers::get();

$keeper->user_id = $user->_id;

$keepers = $keeper->loadList("user_id = '$user->_id'","keeper_name");
foreach ($keepers as $_keeper) {
  $_keeper->loadBackRefs("categories", "category_name");
}

$smarty = new CSmartyDP();
$smarty->assign("keepers", $keepers);
$smarty->display("inc_list_keeper.tpl");