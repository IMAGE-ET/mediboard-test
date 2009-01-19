<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author Thomas Despoix
 */

global $AppUI;

mbExport($_POST);

$docItem = CMbObject::loadFromGuid($_POST["docitem_guid"]);
mbTrace($docItem->getProps(), "Doc Item");

// Simulating Export
$doc_ecap_id = rand(time());
$AppUI->setMsg("Simulating export with returned id : '$doc_ecap_id'");

CMedicap::makeTags();
$idExt = new CIdSante400;
$idExt->loadLatestFor($docItem, CMedicap::$tags["DOC"]);
$idExt->id400 = $doc_ecap_id;
if ($msg = $idExt->store()) {
  $AppUI->setMsg("Erreur sauvegarde de l'identifiant externe : '$msg'", UI_MSG_ERROR);
}
else {
  $AppUI->setMsg("Identifiant externe sauvegardé", UI_MSG_ERROR);
}

mbTrace($idExt->getProps(), "Id e-Cap");

if (null == $ajax = mbGetValueFromPost("ajax")) {
//  $AppUI->redirect();
}

?>