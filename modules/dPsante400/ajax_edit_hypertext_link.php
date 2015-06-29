<?php
/**
 * $Id:$
 *  
 * @package    Mediboard
 * @subpackage dPsante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 * @link       http://www.mediboard.org
 */

$object_id         = CValue::get('object_id');
$object_class      = CValue::get('object_class');
$hypertext_link_id = CValue::get('hypertext_link_id', 0);

$hypertext_link = new CHyperTextLink();
$hypertext_link->object_id = $object_id;
$hypertext_link->object_class = $object_class;

if ($hypertext_link_id) {
  $hypertext_link->load($hypertext_link_id);
}

$smarty = new CSmartyDP();
$smarty->assign('hypertext_link', $hypertext_link);
$smarty->assign('show_widget'   , CValue::get('show_widget', 0));
$smarty->display('inc_edit_hypertext_link.tpl');