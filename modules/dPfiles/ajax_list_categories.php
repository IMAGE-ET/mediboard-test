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

CCanDo::checkRead();

// Chargement de la catégorie demandé
$category = new CFilesCategory;
$categories = $category->loadList(null, "class, nom");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("categories"  , $categories);
$smarty->display("inc_list_categories.tpl");
