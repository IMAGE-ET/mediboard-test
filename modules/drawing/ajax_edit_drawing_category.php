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

$id       = CValue::get("id");
$mode_id  = CValue::get("mode_id");
$mode     = CValue::get("mode");

$cat = new CDrawingCategory();
$cat->load($id);
if (!$cat->_id) {
  $cat->$mode  = $mode_id;
}
$nb_files = $cat->loadRefsFiles();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("cat", $cat);
$smarty->display("inc_edit_category.tpl");