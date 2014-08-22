<?php /** $Id: **/

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$date = CValue::get("date", CMbDT::date());

// Plages d'astreinte pour l'utilisateur
$plage_astreinte = new CPlageAstreinte();
$where = array();
$where["start"] = "< '$date 23:59:00'";
$where["end"]   = "> '$date 00:00:00'";
$plages_astreinte = $plage_astreinte->loadList($where);

/** @var $plages_astreinte CPlageAstreinte[] */
foreach ($plages_astreinte as $_plage) {
  $_plage->loadRefUser();
  $_plage->loadRefColor();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plages_astreinte",   $plages_astreinte);
$smarty->assign("title", CAppUI::tr("CPlageAstreinte.For")." ".htmlentities(CMbDT::format($date, CAppUI::conf("longdate"))));
$smarty->assign("date",   $date);
$smarty->display("vw_list_day_astreinte.tpl");

