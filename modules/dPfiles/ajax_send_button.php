<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("notext", "");
$smarty->assign("_doc_item", mbGetObjectFromGet(null, null, "item_guid"));
$smarty->assign("onComplete", CValue::get("onComplete"));

$smarty->display("inc_file_send_button.tpl");

