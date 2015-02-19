<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDO::checkRead();

$user = CUser::get();

$now = CMbDT::date();

$filterOp = new COperation();
$filterOp->salle_id      = CValue::getOrSession("salle_id");
$filterOp->_date_min     = CValue::get("_date_min", $now);
$filterOp->_date_max     = CValue::get("_date_max", $now);
$filterOp->_prat_id      = CValue::getOrSession("_prat_id");
$filterOp->_plage        = CValue::getOrSession("_plage");
$filterOp->_ranking      = CValue::getOrSession("_ranking");
$filterOp->_cotation     = CValue::getOrSession("_cotation");
$filterOp->_specialite   = CValue::getOrSession("_specialite");
$filterOp->_codes_ccam   = CValue::getOrSession("_codes_ccam");
$filterOp->_ccam_libelle = CValue::getOrSession("_ccam_libelle");

$filterSejour = new CSejour();
$filterSejour->type = CValue::getOrSession("type");
$filterSejour->ald  = CValue::getOrSession("ald");
$yesterday  = CMbDT::date("-1 day", $now);

$mediuser = new CMediusers();
$listPrat = $mediuser->loadPraticiens(PERM_READ);

$function = new CFunctions();
$listSpec = $function->loadSpecialites(PERM_READ);

// Récupération des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("chir"         , $user->_id);
$smarty->assign("filter"       , $filterOp);
$smarty->assign("filterSejour" , $filterSejour);
$smarty->assign("now"          , $now);
$smarty->assign("yesterday"    , $yesterday);
$smarty->assign("listPrat"     , $listPrat);
$smarty->assign("listSpec"     , $listSpec);
$smarty->assign("listBlocs"    , $listBlocs);

$smarty->display("print_plannings/vw_print_planning.tpl");
