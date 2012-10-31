<?php 

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
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
// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Type de vue
$hide_payees   = CValue::getOrSession("hide_payees"  , 0);
$hide_annulees = CValue::getOrSession("hide_annulees", 1);

// Plage de consultation selectionnée
$plageconsult_id = CValue::getOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
if (($plageconsult_id === null) && $chirSel && $is_in_period) {
  $nowTime = mbTime();
  $where = array(
    "chir_id = '$chirSel' OR remplacant_id = '$chirSel' OR pour_compte_id = '$chirSel'",
    "date"    => "= '$today'",
    "debut"   => "<= '$nowTime'",
    "fin"     => ">= '$nowTime'"
  );
  $plageSel->loadObject($where);
}
if (!$plageSel->plageconsult_id) {
  $plageSel->load($plageconsult_id);
}
else {
  $plageconsult_id = $plageSel->plageconsult_id;
}
$plageSel->loadRefsFwd(1);
$plageSel->loadRefsNotes();
$plageSel->loadRefsBack();

if ($plageSel->_affected) {
  $firstconsult = reset($plageSel->_ref_consultations);
  $_firstconsult_time = substr($firstconsult->heure, 0, 5);
  $lastconsult = end($plageSel->_ref_consultations);
  $_lastconsult_time  = substr($lastconsult->heure, 0, 5);
}

// Détails sur les consultation affichées
foreach ($plageSel->_ref_consultations as $keyConsult => &$consultation) {
  // Cache les payées
  if ($hide_payees && $consultation->patient_date_reglement) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  // Cache les annulées
  if ($hide_annulees && $consultation->annule) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  $consultation->loadRefSejour(1);
  $consultation->loadRefPatient(1);
  $consultation->loadRefCategorie(1);
  $consultation->countDocItems();    
}

if ($plageSel->chir_id != $chirSel && $plageSel->remplacant_id != $chirSel &&  $plageSel->pour_compte_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("hide_payees"       , $hide_payees);
$smarty->assign("hide_annulees"     , $hide_annulees);
$smarty->assign("mediuser"          , $mediuser);

$smarty->display("inc_consultations.tpl");

?>