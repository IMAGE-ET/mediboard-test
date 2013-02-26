<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPurgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkRead();
$chapitre_id  = CValue::get("_chapitre_id");
$motif_id     = CValue::get("_motif_id");
$rpu_id       = CValue::getOrSession("rpu_id");
$sejour_id    = CValue::get("sejour_id");

$rpu    = new CRPU;
if ($rpu_id && !$rpu->load($rpu_id)) {
  global $m, $tab;
  CAppUI::setMsg("Ce RPU n'est pas ou plus disponible", UI_MSG_WARNING);
  CAppUI::redirect("m=$m&tab=$tab&rpu_id=0");
}

// Création d'un RPU pour un séjour existant
if ($sejour_id) {
  $rpu = new CRPU;
  $rpu->sejour_id = $sejour_id;
  $rpu->loadMatchingObject();
  $rpu->updateFormFields();
}

if ($rpu->_id || $rpu->sejour_id) {
  // Mise en session de l'id de la consultation, si elle existe.
  $rpu->loadRefConsult();
  if ($rpu->_ref_consult->_id) {
    CValue::setSession("selConsult", $rpu->_ref_consult->_id);
  }
  $sejour  = $rpu->_ref_sejour;
  $sejour->loadNDA();
  $sejour->loadRefPraticien(1);
  $sejour->loadRefsNotes();
}
else {
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
}

// Chargement des boxes 
$services = array();
// Urgences pour un séjour "urg"
$services = CService::loadServicesUrgence();
// UHCD pour un séjour "comp" et en UHCD
if ($sejour->type == "comp" && $sejour->UHCD) {
  $services = CService::loadServicesUHCD();
}

$rpu->loadRefMotif();
$chapitre = new CChapitreMotif();
$chapitres = $chapitre->loadList();
$motif    = new CMotif();
if ($motif_id) {
  $motif_tmp = new CMotif();
  $motif_tmp->load($motif_id);
  $motif->chapitre_id = $motif_tmp->chapitre_id;
  $chapitre_id        = $motif_tmp->chapitre_id;
  $rpu->code_diag     = $motif_tmp->code_diag;
  $rpu->_ref_motif    = $motif_tmp;
}
elseif ($chapitre_id) {
  $motif->chapitre_id = $chapitre_id;
}
$motifs = $motif->loadMatchingList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("chapitre_id"         , $chapitre_id);
$smarty->assign("chapitres"           , $chapitres);
$smarty->assign("motif_id"            , $motif_id);
$smarty->assign("motifs"              , $motifs);
$smarty->assign("services"            , $services);
$smarty->assign("rpu"                 , $rpu);
$smarty->assign("sejour"              , $sejour);

$smarty->display("inc_form_complement.tpl");
