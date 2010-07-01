<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$category = new CProductCategory();
$categories_list = $category->loadList(null, 'name');

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('categories_list', $categories_list);
$smarty->display("configure.tpl");

?>