<?php

/**
 * maternite
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$cat_docs        = CValue::getOrSession("cat_docs");
$specialite_docs = CValue::getOrSession("specialite_docs");
$prat_docs       = CValue::getOrSession("prat_docs");
$date_docs_max   = CValue::getOrSession("date_docs_max", mbDate());
$date_docs_min   = CValue::getOrSession("date_docs_min", mbDate("-1 week"));
$page            = CValue::get('page', 0);

$categories = CFilesCategory::listCatClass();

$prat       = new CMediusers();
$prats      = $prat->loadUsers();

$specialite = new CFunctions();
$specialites = $specialite->loadSpecialites();

$smarty = new CSmartyDP;

$smarty->assign("categories" , $categories);
$smarty->assign("specialites", $specialites);
$smarty->assign("prats"      , $prats);
$smarty->assign("cat_docs"   , $cat_docs);
$smarty->assign("specialite_docs", $specialite_docs);
$smarty->assign("prat_docs"   , $prat_docs);
$smarty->assign("date_docs_min", $date_docs_min);
$smarty->assign("date_docs_max", $date_docs_max);
$smarty->assign("page"       , $page);

$smarty->display("vw_last_docs.tpl");

?>