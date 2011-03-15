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
$patient_id         = CValue::get("patient_id");
$patient->nom       = CValue::get("patient_nom");
$patient->prenom    = CValue::get("patient_prenom");
$patient->naissance = CValue::get("patient_date_naissance");
$patient->sexe      = CValue::get("patient_sexe");
$patient->adresse   = CValue::get("patient_adresse");
$patient->cp        = CValue::get("patient_code_postal");
$patient->ville     = CValue::get("patient_ville");
$patient->tel       = CValue::get("patient_telephone");
$patient->tel2      = CValue::get("patient_mobile");

$sejour = new CSejour();
$sejour->libelle       = CValue::get("sejour_libelle");
$sejour->type          = CValue::get("sejour_type");
$sejour->entree_prevue = CValue::get("sejour_entree_prevue");
$sejour->sortie_prevue = CValue::get("sejour_sortie_prevue");
$sejour->rques         = CValue::get("sejour_remarques");

$sejour_intervention = CValue::get("sejour_intervention");

$intervention = new COperation();
$intervention->_datetime       = CValue::get("intervention_date");
$intervention->temp_operation = CValue::get("intervention_duree");
$intervention->cote            = CValue::get("intervention_cote");
$intervention->horaire_voulu   = CValue::get("intervention_horaire_souhaite");
$intervention->codes_ccam      = CValue::get("intervention_codes_ccam");
$intervention->materiel        = CValue::get("intervention_materiel");
$intervention->rques           = CValue::get("intervention_remarques");

$msg_error = null;

$list_fields = array();
$patient_existant = new CPatient();
$patient_resultat = new CPatient();
$patient_ok       = false;
$sejour_ok        = false;
$intervention_ok  = false;
if($patient_id) {
  $patient_resultat->load($patient_id);
  if($patient_resultat->_id) {
    $patient_ok = true;
  }
}
if ($praticien_id && !$patient_ok) {
  if ($patient->nom && $patient->prenom && $patient->sexe && $patient->naissance) {
    // Recherche d'un patient existant
    $patient_existant = clone $patient;
    $patient_existant->loadMatchingPatient();
    // S'il n'y est pas, on le store
    if(!$patient_existant->_id) {
      if (!$msg_error = $patient_existant->store()) {
        $patient = $patient_existant;
        $patient_ok = true;
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
        if (!$msg_error = $patient_existant->store()) {
          $patient = $patient_existant;
          $patient_ok = true;
        }
      }
    }
    // Sinon on propose à l'utilisateur de régler les problèmes
    if(!$msg_error && !$patient_ok) {
      $patient_resultat = clone $patient;
      foreach($list_fields as $_field => $_state) {
        $patient_resultat->$_field = CValue::first($patient->$_field, $patient_existant->$_field);
      }
    }
  } else {
    $msg_error = "champ(s) obligatoire(s) manquant(s) :";
    if(!$patient->nom) {
      $msg_error .="<br />- Nom";
    }
    if(!$patient->prenom) {
      $msg_error .="<br />- Prénom";
    }
    if(!$patient->sexe) {
      $msg_error .="<br />- Sexe";
    }
    if(!$patient->naissance) {
      $msg_error .="<br />- Naissance";
    }
  }
  if(!$patient_ok) {
    $msg_error = "<strong>Impossible de sauvegarder le patient :<strong> ".$msg_error;
    // Création du template
    $smarty = new CSmartyDP();
    $smarty->assign("praticien_id"       , $praticien_id);
    $smarty->assign("list_fields"        , $list_fields);
    $smarty->assign("patient"            , $patient);
    $smarty->assign("sejour"             , $sejour);
    $smarty->assign("intervention"       , $intervention);
    $smarty->assign("sejour_intervention", $sejour_intervention);
    $smarty->assign("patient_existant"   , $patient_existant);
    $smarty->assign("patient_resultat"   , $patient_resultat);
    $smarty->assign("msg_error"          , $msg_error);
    $smarty->display("dhe_externe.tpl");
    return;
  }

  // Gestion du séjour
  if($sejour->libelle && $patient_ok && $sejour->entree_prevue && $sejour->sortie_prevue
     && $sejour->entree_prevue <= $sejour->sortie_prevue && $sejour->type && $praticien_id) {
    $sejour->group_id = CGroups::loadCurrent()->_id;
    $sejour->praticien_id = $praticien_id;
    $sejour->patient_id = $patient->_id;
    $sejour->updateDBFields();
    $collisions = $sejour->getCollisions();
    $sejour_existant = new CSejour();
    if(count($collisions)) {
      $sejour_existant = reset($collisions);
      $sejour_existant->libelle = $sejour->libelle;
      if($sejour->rques) {
        $sejour_existant->rques .= "\n".$sejour->rques;
      }
      $sejour_existant->entree_prevue = $sejour->entree_prevue;
      $sejour_existant->sortie_prevue = $sejour->sortie_prevue;
      $sejour_existant->type = $sejour->type;
      if(mbDate($sejour_existant->entree_prevue) == mbDate($sejour_existant->sortie_prevue)
         && $sejour_existant->type == "comp") {
        $sejour_existant->type = "ambu";
      } elseif(mbDate($sejour_existant->entree_prevue) != mbDate($sejour_existant->sortie_prevue)
         && $sejour_existant->type == "ambu") {
        $sejour_existant->type = "comp";
      }
    } else {
      $sejour_existant = clone $sejour;
    }
    if (!$msg_error = $sejour_existant->store()) {
      $sejour = $sejour_existant;
      $sejour_ok = true;
    }
  } elseif($sejour->libelle) {
    $msg_error = "champ(s) obligatoire(s) manquant(s) :";
    if(!$sejour->entree_prevue) {
      $msg_error .="<br />- Entree_prevue";
    }
    if(!$sejour->sortie_prevue) {
      $msg_error .="<br />- Sortie prévue";
    }
    if($sejour->entree_prevue && $sejour->sortie_prevue
     && !($sejour->entree_prevue <= $sejour->sortie_prevue)) {
      $msg_error .="<br />- La sortie doit être supérieur à l'entrée";
    }
    if(!$sejour->type) {
      $msg_error .="<br />- Type de séjour";
    }
  }
  if($sejour->libelle && !$sejour_ok) {
    $msg_error = "<strong>Impossible de sauvegarder le séjour :</strong> ".$msg_error;
    // Création du template
    $smarty = new CSmartyDP();
    $smarty->assign("praticien_id"       , $praticien_id);
    $smarty->assign("patient"            , $patient);
    $smarty->assign("sejour"             , $sejour);
    $smarty->assign("sejour_intervention", $sejour_intervention);
    $smarty->assign("msg_error"          , $msg_error);
    $smarty->display("dhe_externe.tpl");
    return;
  }
  // Gestion de l'intervention
  if($sejour_intervention && $intervention->_datetime && $intervention->temp_operation && $intervention->cote) {
    $intervention->chir_id = $praticien_id;
    // Est-ce que la date permet de planifier
    if(mbDaysRelative(mbDate(), mbDate($intervention->_datetime)) > CAppUI::conf("dPbloc CPlageOp days_locked")) {
      $plage_op = new CPlageOp();
      $plage_op->date = mbDate($intervention->_datetime);
      $plage_op->chir_id = $praticien_id;
      $listPlages = $plage_op->loadMatchingList();
      if(count($listPlages)) {
        $intervention->plageop_id = reset($listPlages)->_id;
      }
    }
    if(!$intervention->plageop_id && mbDaysRelative(mbDate(), mbDate($intervention->_datetime)) > 2) {
      $msg_error = "aucune vacation de disponible à cette date";
    } else {
      $intervention->libelle = $sejour->libelle;
      $intervention->sejour_id = $sejour->_id;
      if (!$msg_error = $intervention->store()) {
        $intervention_ok = true;
      }
    }
  } elseif($sejour_intervention) {
    $msg_error = "champ(s) obligatoire(s) manquant(s) :";
    if(!$intervention->_datetime) {
      $msg_error .="<br />- Date de l'intervention";
    }
    if(!$intervention->temp_operation) {
      $msg_error .="<br />- Durée de l'intervention";
    }
    if(!$intervention->cote) {
      $msg_error .="<br />- Cote de l'intervention";
    }
  }
  if($sejour_intervention && !$intervention_ok) {
    $msg_error = "<strong>Impossible de sauvegarder l'intervention :</strong> ".$msg_error;
    // Création du template
    $smarty = new CSmartyDP();
    $smarty->assign("praticien_id"       , $praticien_id);
    $smarty->assign("patient"            , $patient);
    $smarty->assign("sejour"             , $sejour);
    $smarty->assign("intervention"       , $intervention);
    $smarty->assign("sejour_intervention", $sejour_intervention);
    $smarty->assign("msg_error"          , $msg_error);
    $smarty->display("dhe_externe.tpl");
    return;
  }
}

if($patient_ok && !$sejour_existant->libelle) {
  CAppUI::redirect("m=dPplanningOp&a=vw_edit_planning&chir_id=$praticien_id&pat_id=".$patient_existant->_id);
} elseif($patient_ok && $sejour_ok && !$sejour_intervention) {
  CAppUI::redirect("m=dPplanningOp&tab=vw_edit_sejour&sejour_id=".$sejour_existant->_id);
} elseif($patient_ok && $sejour_ok && $intervention_ok && $intervention->plageop_id) {
  CAppUI::redirect("m=dPplanningOp&tab=vw_edit_planning&operation_id=".$intervention->_id);
}  elseif($patient_ok && $sejour_ok && $intervention_ok && !$intervention->plageop_id) {
  CAppUI::redirect("m=dPplanningOp&tab=vw_edit_urgence&operation_id=".$intervention->_id);
} else {
    mbTrace("erreur indéfinie");
    $msg_error = "Erreur indéfinie";
    // Création du template
    $smarty = new CSmartyDP();
    $smarty->assign("praticien_id", $praticien_id);
    $smarty->assign("msg_error"   , $msg_error);
    $smarty->display("dhe_externe.tpl");
}

?>
