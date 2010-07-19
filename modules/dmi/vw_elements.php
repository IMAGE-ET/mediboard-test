<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$start_dmi    = CValue::getOrSession("start_CDMI", 0);
$start_dm     = CValue::getOrSession("start_CDM", 0);
$keywords_dmi = CValue::getOrSession("keywords_CDMI");
$keywords_dm  = CValue::getOrSession("keywords_CDM");

$dmi_category = new CDMICategory;
$list_dmi_categories = $dmi_category->loadList(null, "nom");

$dm_category = new CCategoryDM;
$list_dm_categories = $dm_category->loadList(null, "nom");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_categories_CDMI", $list_dmi_categories);
$smarty->assign("list_categories_CDM", $list_dm_categories);

$smarty->assign("start_CDMI", $start_dmi);
$smarty->assign("start_CDM", $start_dm);

$smarty->assign("keywords_CDMI", $keywords_dmi);
$smarty->assign("keywords_CDM", $keywords_dm);

$smarty->display("vw_elements.tpl");
