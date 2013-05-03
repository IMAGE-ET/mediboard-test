<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

$user = CMediusers::get();

if (!$user->isPraticien()) {
  CCanDo::checkRead();
}

$sejour_id   = CValue::get("sejour_id", 0);
$user_id     = CValue::get("user_id");
$cible       = CValue::getOrSession("cible", "");
$_show_obs   = CValue::getOrSession("_show_obs", 1);
$_show_trans = CValue::getOrSession("_show_trans", 1);
$_show_const = CValue::getOrSession("_show_const", 0);

if ($cible != "") {
  $_show_obs = $_show_const = 0;
}

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadSuiviMedical();
$sejour->loadRefPraticien();

$sejour->loadRefsConsultations();

$sejour->loadRefPrescriptionSejour();
$prescription =& $sejour->_ref_prescription_sejour;

$is_praticien   = $user->isPraticien();
$has_obs_entree = 0;

$cible = stripslashes($cible);

$cibles = array(
  "opened" => array(),
  "closed" => array()
);
$users = array();
$last_trans_cible = array();

foreach ($sejour->_ref_suivi_medical as $_key => $_suivi) {
  if (is_array($_suivi)) {
    $_suivi = $_suivi[0];
  }
  // Elements et commentaires
  if ($_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment) {
    $_suivi->loadRefPraticien();
    $users[$_suivi->praticien_id] = $_suivi->_ref_praticien;
    if ($user_id && $_suivi->praticien_id != $user_id) {
      unset($sejour->_ref_suivi_medical["$_suivi->debut $_suivi->time_debut $_suivi->_guid"]);
    }
  }
  // Transmissions et Observations
  elseif (!$_suivi instanceof CConsultation) {
    $users[$_suivi->user_id] = $_suivi->_ref_user;
    $type = ($_suivi instanceof CObservationMedicale) ? "obs" : "trans";
    if ($user_id && $_suivi->user_id != $user_id) {
      unset($sejour->_ref_suivi_medical[$_suivi->date.$_suivi->_id.$type]);
    }

    $_suivi->loadRefUser();
    if ($_suivi instanceof CTransmissionMedicale) {
      $trans = $_suivi;
      $trans->calculCibles($cibles);
      if ($cible && $_suivi->_cible != $cible) {
        unset($sejour->_ref_suivi_medical[$_suivi->date.$_suivi->_id.$type]);
      }

      if ($_suivi->libelle_ATC) {
        if (!isset($last_trans_cible[$_suivi->libelle_ATC])) {
          $last_trans_cible[$_suivi->libelle_ATC] = $_suivi;
        }
      }
      else if (!isset($last_trans_cible["$_suivi->object_class $_suivi->object_id"])) {
        $last_trans_cible["$_suivi->object_class $_suivi->object_id"] = $_suivi;
      }
    }
    $_suivi->canEdit();
  }
}

foreach ($last_trans_cible as $_last) {
  $_last->_log_lock = $_last->loadLastLogForField("locked");
  $_last->_log_lock->loadRefUser()->loadRefMediuser()->loadRefFunction();
}

//TODO: Revoir l'ajout des constantes dans le suivi de soins
//Ajout des constantes
if (!$cible && CAppUI::conf("soins constantes_show") && $_show_const) {
  $sejour->loadRefConstantes($user_id);
}

//mettre les transmissions dans un tableau dont l'index est le datetime
$list_trans_const = array();

$trans_compact = CAppUI::conf("soins trans_compact");

foreach ($sejour->_ref_suivi_medical as $_key => $_trans_const) {
  if (is_array($_trans_const)) {
    $_trans_const = $_trans_const[0];
  }
  if (($_trans_const instanceof CObservationMedicale || $_trans_const instanceof CConsultation) && !$_show_obs) {
    continue;
  }
  if ($_trans_const instanceof CTransmissionMedicale && !$_show_trans) {
    continue;
  }
  if ($_trans_const instanceof CConstantesMedicales) {
    $sort_key = "$_trans_const->datetime $_trans_const->_guid";
    $list_trans_const[$sort_key] = $_trans_const;
  }
  elseif ($_trans_const instanceof CConsultation) {
    foreach ($_trans_const->_refs_dossiers_anesth as $key => $_dossier_anesth) {
      $_dossier_anesth->loadRefOperation();
    }
    if ($_trans_const->type == "entree") {
      $has_obs_entree = 1;
    }
    $list_trans_const[$_trans_const->_datetime] = $_trans_const;
  }
  elseif ($_trans_const instanceof CTransmissionMedicale) {
    $sort_key_pattern = "$_trans_const->_class $_trans_const->user_id $_trans_const->object_id $_trans_const->object_class $_trans_const->libelle_ATC";

    $sort_key = "$_trans_const->date $sort_key_pattern";

    $date_before = CMbDT::dateTime("-1 SECOND", $_trans_const->date);
    $sort_key_before = "$date_before $sort_key_pattern";

    $date_after  = CMbDT::dateTime("+1 SECOND", $_trans_const->date);
    $sort_key_after = "$date_after $sort_key_pattern";

    if (($_trans_const->libelle_ATC &&
      $last_trans_cible[$_trans_const->libelle_ATC] != $_trans_const &&
      ($last_trans_cible[$_trans_const->libelle_ATC]->locked || ($trans_compact &&
        !array_key_exists($sort_key, $list_trans_const) && !array_key_exists($sort_key_before, $list_trans_const) && !array_key_exists($sort_key_after, $list_trans_const)))) ||
      ($_trans_const->object_id &&
        ($last_trans_cible["$_trans_const->object_class $_trans_const->object_id"]->locked || ($trans_compact &&
            !array_key_exists($sort_key, $list_trans_const) && !array_key_exists($sort_key_before, $list_trans_const) && !array_key_exists($sort_key_after, $list_trans_const))) &&
        $last_trans_cible["$_trans_const->object_class $_trans_const->object_id"] != $_trans_const)
    ) {
      unset($sejour->_ref_suivi_medical[$_key]);
      continue;
    }

    // Aggrégation à -1 sec
    if (array_key_exists($sort_key_before, $list_trans_const)) {
      $sort_key = $sort_key_before;
    }
    // à +1 sec
    else if (array_key_exists($sort_key_after, $list_trans_const)) {
      $sort_key = $sort_key_after;
    }

    if (!isset($list_trans_const[$sort_key])) {
      $list_trans_const[$sort_key] = array();
      $list_trans_const[$sort_key][] = $_trans_const;
    }
    else {
      switch ($_trans_const->type) {
        case "data":
          @array_unshift($list_trans_const[$sort_key], $_trans_const);
          break;
        case "action":
          switch (count($list_trans_const[$sort_key])) {
            case 0:
              @array_push($list_trans_const[$sort_key], $_trans_const);
            case 1:
              $_trans = array_shift($list_trans_const[$sort_key]);
              @array_unshift($list_trans_const[$sort_key], $_trans_const);
              if ($_trans->type == "data") {
                @array_unshift($list_trans_const[$sort_key], $_trans);
              }
              else {
                @array_push($list_trans_const[$sort_key], $_trans);
              }
            case 2:
              $_trans = array_shift($list_trans_const[$sort_key]);
              @array_unshift($list_trans_const[$sort_key], $_trans_const);
              @array_unshift($list_trans_const[$sort_key], $_trans);
          }
          break;
        case "result":
          @array_push($list_trans_const[$sort_key], $_trans_const);
      }
    }
  }
  elseif ($_trans_const instanceof CObservationMedicale) {
    $sort_key = "$_trans_const->date $_trans_const->_guid";
    $list_trans_const[$sort_key] = $_trans_const;  
  }
  else {
    $sort_key = "$_trans_const->debut $_trans_const->time_debut $_trans_const->_guid";
    $list_trans_const[$sort_key] = $_trans_const;
  }
}

krsort($list_trans_const);

$count_trans = count($list_trans_const);
$sejour->_ref_suivi_medical = $list_trans_const;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("params"      , CConstantesMedicales::$list_constantes);
$smarty->assign("page_step"   , 20);
$smarty->assign("readOnly"    , CValue::get("readOnly", false));
$smarty->assign("count_trans" , $count_trans);
$smarty->assign("user"        , $user);
$smarty->assign("isPraticien" , $is_praticien);
$smarty->assign("isAnesth"    , $user->isAnesth());
$smarty->assign("sejour"      , $sejour);
$smarty->assign("prescription", $prescription);
$smarty->assign("cibles"      , $cibles);
$smarty->assign("cible"       , $cible);
$smarty->assign("users"       , $users);
$smarty->assign("user_id"     , $user_id);
$smarty->assign("has_obs_entree", $has_obs_entree);
$smarty->assign("last_trans_cible", $last_trans_cible);
$smarty->assign("_show_obs"   , $_show_obs);
$smarty->assign("_show_trans" , $_show_trans);
$smarty->assign("_show_const" , $_show_const);

$smarty->display("inc_vw_dossier_suivi.tpl");
