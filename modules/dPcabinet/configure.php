<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$hours = range(0, 23);
$intervals = array("05","10","15","20","30");

$function = new CFunctions();
$function->group_id = CGroups::loadCurrent()->_id;
$functions = $function->loadMatchingList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hours"     , $hours);
$smarty->assign("date"      , CMbDT::date());
$smarty->assign("intervals" , $intervals);

$smarty->assign("debut"     , CMbDT::date("+ 5 YEAR"));
$smarty->assign("limit"     , "100");
$smarty->assign("praticiens", CMediusers::get()->loadPraticiens());
$smarty->assign("anesths"   , CMediusers::get()->loadAnesthesistes());
$smarty->assign("functions_id", $functions);
$smarty->assign("user"      , CUser::get());

$smarty->display("configure.tpl");
