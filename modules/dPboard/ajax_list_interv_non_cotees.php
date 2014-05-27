<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Board
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$chirSel   = CValue::getOrSession("praticien_id", null);
$all_prats = CValue::get("all_prats", 0);
$fin       = CValue::get("fin", CMbDT::date());
$fin       = CMbDT::date("+1 day", $fin);
$debut     = CValue::get("debut", CMbDT::date("-1 week", $fin));

$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chirSel = $mediuser->_id;
}

$user = new CMediusers();
$user->load($chirSel);

$ljoin = array();
$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where = array();
$where["operations.date"] = "BETWEEN '$debut' AND '$fin'";
$where["operations.annulee"] = "= '0'";

if ($all_prats) {
  $prats = $user->loadPraticiens(PERM_READ);
  $in_prats = CSQLDataSource::prepareIn(array_keys($prats));
  $where["operations.chir_id"] = "$in_prats";
  $where[] = "operations.anesth_id IS NULL OR operations.anesth_id $in_prats";
}
else {
  if ($user->isAnesth()) {
    $where[] = "operations.chir_id = '$user->_id' OR
      operations.anesth_id = '$user->_id' OR
      (operations.anesth_id IS NULL && plagesop.anesth_id = '$user->_id')";
  }
  else {
    $where["operations.chir_id"] = "= '$user->_id'";
  }
}

/** @var COperation[] $interventions */
$operation = new COperation();
$interventions = $operation->loadList($where, null, null, null, $ljoin);

CStoredObject::massLoadFwdRef($interventions, "plageop_id");
/** @var CSejour[] $sejours */
$sejours = CStoredObject::massLoadFwdRef($interventions, "sejour_id");
CStoredObject::massLoadFwdRef($sejours, "patient_id");

/** @var CMediusers[] $chirs */
$chirs = CStoredObject::massLoadFwdRef($interventions, "chir_id");
CStoredObject::massLoadFwdRef($chirs, "function_id");

$where = array();
if (!$all_prats) {
  //$where["executant_id"] = "= '$user->_id'";
  $where["code_activite"] = $user->_is_anesth ? "= '4'" : "!= '4'";
}

CStoredObject::massLoadBackRefs($interventions, "actes_ccam", null, $where);

foreach ($interventions as $key => $_interv) {
  $_plage = $_interv->loadRefPlageOp();

  $_interv->loadExtCodesCCAM();
  $codes_ccam = $_interv->_ext_codes_ccam;

  // Nombre d'acte cotés par le praticien et réinitialisation du count pour le cache
  $nb_actes_ccam = $_interv->_count["actes_ccam"];
  $_interv->_count["actes_ccam"] = null;

  // Aucun acte prévu ou coté
  if (!count($codes_ccam) && !$_interv->_count_actes) {
    $_interv->loadRefSejour();
    $_interv->loadRefChir()->loadRefFunction();
    $_interv->loadRefAnesth()->loadRefFunction();
    $_interv->loadRefPatient();
    continue;
  }

  // Actes prévus restant en suspend
  $activites = CMbArray::pluck($codes_ccam, "activites");

  $nbCodes = 0;
  foreach ($activites as $_activite) {
    if ($all_prats) {
      $nbCodes += count($_activite);
      continue;
    }
    foreach ($_activite as $_key_activite => $_type_activite) {
      if ($user->_is_anesth && $_key_activite == 4) {
        $nbCodes++;
      }
      if (!$user->_is_anesth && $_key_activite != 4) {
        $nbCodes++;
      }
    }
  }

  // Si tout est coté, on n'affiche pas l'opération
  if ($nb_actes_ccam >= $nbCodes) {
    unset($interventions[$key]);
    continue;
  }

  $_interv->_actes_non_cotes = $nbCodes - $nb_actes_ccam;
  $_interv->loadRefSejour();
  $_interv->loadRefChir()->loadRefFunction();
  $_interv->loadRefAnesth()->loadRefFunction();
  $_interv->loadRefPatient();

  // Liste des actes CCAM cotés
  foreach ($_interv->loadRefsActesCCAM() as $_acte) {
    $_acte->loadRefExecutant();
  }
}

$interventions = CModelObject::naturalSort($interventions, array("_datetime"));

$ljoin = array();
$ljoin["sejour"] = "consultation.sejour_id = sejour.sejour_id";
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

$where = array();
$where["sejour.entree"] = " BETWEEN '$debut' AND '$fin'";
$where["sejour.annule"] = "= '0'";
$where["consultation.annule"] = "= '0'";

if ($all_prats) {
  $prats = $user->loadPraticiens(PERM_READ);
  $where["plageconsult.chir_id"]   = CSQLDataSource::prepareIn(array_keys($prats));
}
else {
  $where["plageconsult.chir_id"] = "= '$user->_id'";
}

/* @var CConsultation[] $consultations*/
$consultation = new CConsultation();
$consultations = $consultation->loadList($where, null, null, null, $ljoin);

/** @var CPlageConsult[] $plages */
$plages = CStoredObject::massLoadFwdRef($consultations, "plageconsult_id");
CStoredObject::massLoadFwdRef($consultations, "sejour_id");
CStoredObject::massLoadFwdRef($consultations, "patient_id");
// Pré-chargement des users
$where = array("user_id" => CSQLDataSource::prepareIn(CMbArray::pluck($plages, "chir_id")));
$user->loadList($where);
/** @var CMediusers[] $chirs */
$chirs = CStoredObject::massLoadFwdRef($plages, "chir_id");
CStoredObject::massLoadFwdRef($chirs, "function_id");
CStoredObject::massLoadBackRefs($consultations, "actes_ccam");

foreach ($consultations as $key => $_consult) {
  // On ignore les consultation ayant des actes NGAP
  if ($_consult->countBackRefs("actes_ngap")) {
    unset($consultations[$key]);
    continue;
  }

  // Chargemement des codes CCAM
  $_consult->loadExtCodesCCAM();
  $codes_ccam = $_consult->_ext_codes_ccam;

  // Nombre d'acte cotés par le praticien et réinitialisation du count pour le cache
  $nb_actes_ccam = count($_consult->loadRefsActesCCAM());

  // Aucun acte prévu ou coté
  if (!count($codes_ccam) && !$_consult->_count_actes) {
    $_consult->loadRefSejour();
    $_consult->loadRefPraticien()->loadRefFunction();
    $_consult->loadRefPatient();
    continue;
  }

  // Actes prévus restant en suspend
  $activites = CMbArray::pluck($codes_ccam, "activites");
  $nbCodes = 0;
  foreach ($activites as $_activite) {
    if ($all_prats) {
      $nbCodes += count($_activite);
      continue;
    }
    foreach ($_activite as $_key_activite => $_type_activite) {
      if ($user->_is_anesth && $_key_activite == 4) {
        $nbCodes++;
      }
      if (!$user->_is_anesth && $_key_activite != 4) {
        $nbCodes++;
      }
    }
  }

  // Si tout est coté, on n'affiche pas l'opération
  if ($nb_actes_ccam >= $nbCodes) {
    unset($consultations[$key]);
    continue;
  }

  $_consult->_actes_non_cotes = $nbCodes - $nb_actes_ccam;
  $_consult->loadRefsFwd();
  $_consult->loadRefSejour();
  $_consult->loadRefPraticien()->loadRefFunction();

  // Liste des actes CCAM cotées
  foreach ($_consult->loadRefsActesCCAM() as $_acte) {
    $_acte->loadRefExecutant();
  }
}

$consultations = CModelObject::naturalSort($consultations, array("_date"));

$smarty = new CSmartyDP();

$smarty->assign("interventions", $interventions);
$smarty->assign("consultations", $consultations);
$smarty->assign("debut"        , $debut);
$smarty->assign("fin"          , $fin);
$smarty->assign("all_prats"    , $all_prats);
$smarty->assign("board"        , CValue::get("board", 0));

$smarty->display("../../dPboard/templates/inc_list_interv_non_cotees.tpl");
