<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

function viewMsg($msg, $action, $txt = ""){
  global $m, $tab;
  $action = CAppUI::tr($action);
  if ($msg) {
    CAppUI::setMsg("$action: $msg", UI_MSG_ERROR );
    CAppUI::redirect("m=$m&tab=$tab");
    return;
  }
  CAppUI::setMsg("$action $txt", UI_MSG_OK );
}

//récupération de l'identifiant du séjour à fusionner
$sejour_id_merge = CValue::post("sejour_id_merge");
// Récupération du rpu
$rpu_id = CValue::post("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);

$sejour            = $rpu->loadRefSejour();
$sejour_rpu        = $sejour;
$properties_sejour = $sejour->getProperties();

//Cas d'une collision ou d'un rattachement d'un séjour
if ($sejour_id_merge) {
  $sejour_merge = new CSejour();
  $sejour_merge->load($sejour_id_merge);

  $sejour_merge->entree_reelle  = $sejour->entree_reelle;
  $sejour_merge->mode_entree_id = $sejour->mode_entree_id;
  $sejour_merge->mode_entree    = $sejour->mode_entree;
  $sejour_merge->provenance     = $sejour->provenance;

  $msg = $sejour_merge->merge(array($sejour));
  viewMsg($msg, "Fusion");
  $sejour = $sejour_merge;
  $rpu->sejour_id = $sejour_merge->_id;
}

// Creation d'un séjour reliquat
if (!CAppUI::conf("dPurgences create_sejour_hospit")) {
  // Clonage
  $sejour_rpu = new CSejour;
  foreach ($properties_sejour as $name => $value) {
    $sejour_rpu->$name = $value;
  }

  // Forcer le reliquat du séjour en urgences
  $sejour_rpu->type = "urg";

  // Enregistrement
  $sejour_rpu->_id = null;

  // Pas de génération du NDA, et pas de synchro (handler) du séjour
  $sejour_rpu->_generate_NDA = false;
  $sejour_rpu->_no_synchro   = true;
  $msg = $sejour_rpu->store();
  viewMsg($msg, "Séjour reliquat enregistré");

  // Transfert du RPU sur l'ancien séjour
  $rpu->sejour_id = $sejour_rpu->_id;
}

//Cas d'une hospitalisation normale sans collision et sans rattachement
if (!$sejour_id_merge) {
  // Passage en séjour d'hospitalisation
  $sejour->type = "comp";
  $sejour->_en_mutation = $sejour_rpu->_id;
  // La synchronisation était désactivée après la sauvegarde du RPU
  $sejour->_no_synchro = false;
  $msg = $sejour->store();
  viewMsg($msg, "CSejour-title-modify");
}

//Problème sur le séjour, aucune action fait sur le rpu
if ($msg) {
  return;
}
// Modification du RPU
$rpu->mutation_sejour_id = $sejour->_id;
$rpu->sortie_autorisee = "1";
$rpu->gemsa = "4";
$msg = $rpu->store();
viewMsg($msg, "CRPU-title-close");


CAppUI::callbackAjax("Sejour.editModal", $sejour->_id);