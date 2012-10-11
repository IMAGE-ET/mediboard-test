<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_class_event_id = CValue::get("ex_class_event_id");
$keywords = CValue::get("_host_field_view");

$ex_class_event = new CExClassEvent;
$ex_class_event->load($ex_class_event_id);
$list = $ex_class_event->buildHostFieldsList();

$show_views = false;

// filtrage
if ($keywords) {
  $show_views = true;
  
  $re = preg_quote($keywords);
  $re = CMbString::allowDiacriticsInRegexp($re);
  $re = str_replace("/", "\\/", $re);
  $re = "/($re)/i";

  foreach($list as $_key => $element) {
    if (!preg_match($re, $element["title"])) {
      unset($list[$_key]);
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("ex_class_event", $ex_class_event);
$smarty->assign("host_fields", $list);
$smarty->assign("show_views", $show_views);
$smarty->display("inc_autocomplete_hostfields.tpl");
