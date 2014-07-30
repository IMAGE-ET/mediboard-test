<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Droit en écriture sur les plages
$plage = new CPlageconsult();
$canEditPlage = $plage->getPerm(PERM_EDIT);

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// function selected
$function_id = CValue::getOrSession("function_id");
$listFnc = array();
if ($function_id) {
  $listChir = CConsultation::loadPraticiens(PERM_EDIT, $function_id);
  foreach ($listChir as $_chir) {
    $_chir->loadRefFunction();
  }
}
else {
  $listChir = CConsultation::loadPraticiens(PERM_EDIT);
}

// Liste des consultations a avancer si desistement
$ds = $plage->getDS();
$now = CMbDT::date();

// get desistements
$count_si_desistement = CConsultation::countDesistementsForDay($function_id ? array_keys($listChir) : array($chirSel), $now);

// Liste des praticiens

$fnc = new CFunctions();
$listFnc = $fnc->loadListWithPerms(PERM_READ, null, 'text');
$mediuser = new CMediusers();
foreach ($listFnc as $id => $_fnc) {
  $users = $mediuser->loadProfessionnelDeSanteByPref(PERM_READ, $_fnc->_id);
  if (!count($users)) {
    unset($listFnc[$id]);
  }
}

// Période
$today = CMbDT::date();

$debut = CValue::getOrSession("debut", $today);

$debut = CMbDT::date("last sunday", $debut);
$fin   = CMbDT::date("next sunday", $debut);
$debut = CMbDT::date("+1 day", $debut);

$prev = CMbDT::date("-1 week", $debut);
$next = CMbDT::date("+1 week", $debut);

$smarty = new CSmartyDP();

$smarty->assign("listChirs"           , $listChir);
$smarty->assign("today"               , $today);
$smarty->assign("debut"               , $debut);
$smarty->assign("fin"                 , $fin);
$smarty->assign("prev"                , $prev);
$smarty->assign("next"                , $next);
$smarty->assign("chirSel"             , $chirSel);
$smarty->assign("function_id"         , $function_id);
$smarty->assign("listFnc"             , $listFnc);
$smarty->assign("canEditPlage"        , $canEditPlage);
$smarty->assign("count_si_desistement", $count_si_desistement);

$smarty->display("vw_planning_new.tpl");
