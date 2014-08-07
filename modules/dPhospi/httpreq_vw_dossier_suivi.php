<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
$show_header = CValue::get("show_header", 0);

if ($cible != "") {
  $_show_obs = $_show_const = 0;
}

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadSuiviMedical();
$sejour->loadRefPraticien();

if ($show_header) {
  $sejour->loadRefPatient()->loadRefPhotoIdentite();
}

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
    continue;
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
  }
  $_suivi->canEdit();
}

//TODO: Revoir l'ajout des constantes dans le suivi de soins
//Ajout des constantes
$group_guid = CGroups::loadCurrent()->_guid;
if (!$cible &&CAppUI::conf("soins CConstantesMedicales constantes_show", $group_guid) && $_show_const) {
  $sejour->loadRefConstantes($user_id);
}

//mettre les transmissions dans un tableau dont l'index est le datetime
$list_trans_const = array();

$trans_compact = CAppUI::conf("soins Transmissions trans_compact",$group_guid);
$forms_active = CModule::getActive("forms");
CExObject::$_load_lite = true;

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
    // On n'affiche pas les consultations annulées
    if ($_trans_const->annule) {
      unset($sejour->_ref_suivi_medical[$_key]);
      continue;
    }

    if ($forms_active) {
      foreach ($_trans_const->_refs_dossiers_anesth as $key => $_dossier_anesth) {
        $_dossier_anesth->loadRefOperation();
      }
      if ($_trans_const->type == "entree") {
        $has_obs_entree = 1;
      }

      $forms = CExObject::loadExObjectsFor($_trans_const);

      foreach ($_trans_const->_refs_dossiers_anesth as $_dossier_anesth) {
        $_forms = CExObject::loadExObjectsFor($_dossier_anesth);
        $forms += $_forms;
      }
      $_trans_const->_list_forms = $forms;
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
      $list_trans_const[$sort_key] = array("data" => array(), "action" => array(), "result" => array());
    }
    if (!isset($list_trans_const[$sort_key][0])) {
      $list_trans_const[$sort_key][0] = $_trans_const;
    }
    $list_trans_const[$sort_key][$_trans_const->type][] = $_trans_const;
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

ksort($cibles["opened"]);
ksort($cibles["closed"]);
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
$smarty->assign("show_header" , $show_header);

$smarty->display("inc_vw_dossier_suivi.tpl");
