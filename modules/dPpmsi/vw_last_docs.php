<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$cat_docs        = CValue::getOrSession("cat_docs");
$specialite_docs = CValue::getOrSession("specialite_docs");
$prat_docs       = CValue::getOrSession("prat_docs");
$date_docs_max   = CValue::getOrSession("date_docs_max", mbDate());
$date_docs_min   = CValue::getOrSession("date_docs_min", mbDate("-1 week"));
$entree_min      = CValue::getOrSession("entree_min");
$entree_max      = CValue::getOrSession("entree_max");
$sortie_min      = CValue::getOrSession("sortie_min");
$sortie_max      = CValue::getOrSession("sortie_max");
$intervention_min = CValue::getOrSession("intervention_min");
$intervention_max = CValue::getOrSession("intervention_max");
$prat_interv     = CValue::getOrSession("prat_interv");
$section_search  = CValue::getOrSession("section_search", "sejour");
$type            = CValue::getOrSession("type");
$page            = CValue::get('page', 0);

$categories = CFilesCategory::listCatClass();

$prat       = new CMediusers();
$prats      = $prat->loadUsers();

$specialite = new CFunctions();
$specialites = $specialite->loadSpecialites();

$sejour = new CSejour();
if ($type) {
  $sejour->type = $type;
}

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
$smarty->assign("sejour"     , $sejour);
$smarty->assign("entree_min", $entree_min);
$smarty->assign("entree_max", $entree_max);
$smarty->assign("sortie_min", $sortie_min);
$smarty->assign("sortie_max", $sortie_max);
$smarty->assign("intervention_min", $intervention_min);
$smarty->assign("intervention_max", $intervention_max);
$smarty->assign("section_search", $section_search);

$smarty->display("vw_last_docs.tpl");
