<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$praticien = new CMediusers();
$praticien_id = CValue::get("praticien_id");
$praticien->load($praticien_id);
if(!$praticien->canEdit() || !$praticien->isPraticien()) {
  $praticien_id = null;
}

$patient = new CPatient();
$patient->nom       = CValue::get("patient_nom");
$patient->prenom    = CValue::get("patient_prenom");
$patient->naissance = CValue::get("patient_date_naissance");
$patient->sexe      = CValue::get("patient_sexe");
$patient->adresse   = CValue::get("patient_adresse");
$patient->cp        = CValue::get("patient_code_postal");
$patient->ville     = CValue::get("patient_ville");
$patient->tel       = CValue::get("patient_telephone");
$patient->tel2      = CValue::get("patient_mobile");
$patient_existant = clone $patient;

$sejour = new CSejour();
$sejour->libelle       = CValue::get("sejour_libelle");
$sejour->type          = CValue::get("sejour_type");
$sejour->entree_prevue = CValue::get("sejour_entree_prevue");
$sejour->sortie_prevue = CValue::get("sejour_sortie_prevue");
$sejour->rques         = CValue::get("sejour_remarques");

$is_intervention = CValue::get("sejour_intervention");

$intervention = new COperation();
$intervention->_date          = CValue::get("intervention_date");
$intervention->time_operation = CValue::get("intervention_duree");
$intervention->cote           = CValue::get("intervention_cote");
$intervention->horaire_voulu  = CValue::get("intervention_horaire_souhaite");
$intervention->codes_ccam     = CValue::get("intervention_codes_ccam");
$intervention->materiel       = CValue::get("intervention_materiel");
$intervention->rques          = CValue::get("intervention_remarques");

$msg_patient = null;
$list_fields = array();
$patient_resultat = new CPatient();
if ($praticien_id) {
  if ($patient->nom && $patient->prenom && $patient->sexe && $patient->naissance) {
    // Recherche d'un patient existant
    $patient_existant->loadMatchingPatient();
    // S'il n'y est pas, on le store
    if(!$patient_existant->_id) {
      if (!$msg_patient = $patient_existant->store()) {
        CAppUI::redirect("m=dPplanningOp&a=vw_edit_planning&chir_id=$praticien_id&pat_id=".$patient_existant->_id);
      }
    // Sinon on vérifie qu'ils sont bien identiques
    } else {
      $list_fields = array(
                       "nom"       => true,
                       "prenom"    => true,
                       "naissance" => true,
                       "sexe"      => true,
                       "adresse"   => true,
                       "cp"        => true,
                       "ville"     => true,
                       "tel"       => true,
                       "tel2"      => true);
      $patient->updateFormFields();
      $equals  = true;
      foreach($list_fields as $_field => $_state) {
        $list_fields[$_field] = !$patient->$_field || !$patient_existant->$_field || ($patient->$_field == $patient_existant->$_field);
        $equals &= $list_fields[$_field];
      }
      // On complète éventuellement le patient existant avant de le storer
      if ($equals) {
        foreach($list_fields as $_field => $_state) {
          $patient_existant->$_field = CValue::first($patient_existant->$_field, $patient->$_field);
        }
        if (!$msg_patient = $patient_existant->store()) {
          CAppUI::redirect("m=dPplanningOp&a=vw_edit_planning&chir_id=$praticien_id&pat_id=".$patient_existant->_id);
        }
      }
    }
    // Sinon on propose à l'utilisateur de régler les problèmes
    if(!$msg_patient) {
      $patient_resultat = clone $patient;
      foreach($list_fields as $_field => $_state) {
        $patient_resultat->$_field = CValue::first($patient->$_field, $patient_existant->$_field);
      }
    }
  } else {
    $msg_patient = "champ(s) obligatoire(s) manquant(s) :";
    if(!$patient->nom) {
      $msg_patient .="<br />- Nom";
    }
    if(!$patient->prenom) {
      $msg_patient .="<br />- Prénom";
    }
    if(!$patient->sexe) {
      $msg_patient .="<br />- Sexe";
    }
    if(!$patient->naissance) {
      $msg_patient .="<br />- Naissance";
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id"    , $praticien_id);
$smarty->assign("list_fields"     , $list_fields);
$smarty->assign("patient"         , $patient);
$smarty->assign("patient_existant", $patient_existant);
$smarty->assign("patient_resultat", $patient_resultat);
$smarty->assign("msg_patient"     , $msg_patient);
$smarty->display("dhe_externe.tpl");

?>
