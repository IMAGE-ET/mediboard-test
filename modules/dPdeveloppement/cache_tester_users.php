<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$chrono = new Chronometer();
$chrono->start();

if (CView::get("purge", "bool default|0")) {
  SHM::rem("mediusers");
  $chrono->step("purge");
}

if (!SHM::exists("mediusers")) {
  $chrono->step("acquire (not yet)");
  $mediuser = new CMediusers();
  $mediusers = $mediuser->loadListFromType();
  $chrono->step("load");
  SHM::put("mediusers", $mediusers, true);
  $chrono->step("put");
}

/** @var CMediusers[] $mediusers */
$mediusers = SHM::get("mediusers");
$chrono->step("get");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mediusers", $mediusers);
$smarty->assign("chrono", $chrono);
$smarty->display("cache_tester_users.tpl");

