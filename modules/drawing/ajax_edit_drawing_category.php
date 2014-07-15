<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$object_id = CValue::get("object_id");

$cat = new CDrawingCategory();
$cat->load($object_id);
$nb_files = $cat->loadRefsFiles();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("cat", $cat);
$smarty->display("inc_edit_category.tpl");