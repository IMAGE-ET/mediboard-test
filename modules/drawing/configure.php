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

$cat = new CDrawingCategory();
$cats = $cat->loadList(null, "name");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("cats", $cats);
$smarty->display("configure.tpl");
