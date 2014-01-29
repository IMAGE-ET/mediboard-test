<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Bloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();

/** @var CBlocOperatoire[] $listBlocs */
$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$date_suivi = CAppUI::pref("suivisalleAutonome") ? CValue::get("date", CMbDT::date()) : CValue::getOrSession("date", CMbDT::date());

$smarty = new CSmartyDP();
$smarty->assign("blocs"     , $listBlocs);
$smarty->assign("first_bloc", reset($listBlocs));
$smarty->assign("date"      , $date_suivi);
$smarty->display("vw_suivi_salles.tpl");