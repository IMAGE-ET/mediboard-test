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

CCanDo::checkAdmin();

$category_id = CValue::getOrSession("category_id", 0);

$smarty = new CSmartyDP();
$smarty->assign("category_id", $category_id);
$smarty->display("vw_categories.tpl");

