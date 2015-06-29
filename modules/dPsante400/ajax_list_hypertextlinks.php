<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage dPsante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$object_id    = CValue::get('object_id', 0);
$object_class = CValue::get('object_class', '');
$show_widget  = CValue::get('show_widget', 0);

$smarty = new CSmartyDP();
if ($object_id && $object_class) {
  $filter = new CHyperTextLink();
  $filter->object_id    = $object_id;
  $filter->object_class = $object_class;
  $hypertext_links = $filter->loadMatchingList();
}
else {
  $hypertext_links = array();
}

$smarty->assign('hypertext_links' , $hypertext_links);
$smarty->assign('object_id'       , $object_id);
$smarty->assign('object_class'    , $object_class);
$smarty->assign('show_only'       , CValue::getOrSession('show_only', 0));
$smarty->assign('count_links'     , CValue::get('count_links', 0));

if (!$show_widget) {
  $smarty->display('inc_list_hypertext_links.tpl');
}
else {
  $smarty->display('inc_widget_list_hypertext_links.tpl');
}