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

$file_category_id = CValue::getOrSession("file_category_id");

// Chargement de la catégorie demandé
$category = new CFilesCategory;
$category->load($file_category_id);
$category->countDocItems();
$category->loadRefsNotes();

// Liste des Catégories
$categories = $category->loadList(null, "class, nom");

// Liste des Classes disponibles
$listClass = CApp::getChildClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories"  , $categories);
$smarty->assign("category"    , $category    );
$smarty->assign("listClass"   , $listClass   );

$smarty->display("vw_category.tpl");

