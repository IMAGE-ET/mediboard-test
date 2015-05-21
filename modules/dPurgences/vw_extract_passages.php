<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$page     = CValue::get('page'    , 0);
$date_min = CValue::get("date_min", CMbDT::dateTime("-7 day"));
$date_max = CValue::get("date_max", CMbDT::dateTime("+1 day"));
$type     = CValue::get("type"    , null);

// Création du template
$smarty = new CSmartyDP("modules/dPurgences");

$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("type"    , $type);
$smarty->assign("page"    , $page);

$smarty->display("vw_extract_passages.tpl");
