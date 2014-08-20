<?php 

/**
 * $Id$
 *  
 * @category Forms
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$list_id = CValue::get("list_id");

$list = new CExList();
$list->load($list_id);
$list->loadRefItems();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list", $list);
$smarty->display("inc_ex_list_info.tpl");