<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
global $AppUI;

mbExport($_POST);

$docItem = CMbObject::loadFromGuid($_POST["docitem_guid"]);
mbTrace($docItem->getValues(), "Doc Item");

// Simulating Export
$doc_ecap_id = rand(time());
$AppUI->setMsg("Simulating export with returned id : '$doc_ecap_id'");

$idExt = new CIdSante400;
$idExt->loadLatestFor($docItem, CMedicap::getTag("DO"));
$idExt->id400 = $doc_ecap_id;
if ($msg = $idExt->store()) {
  $AppUI->setMsg("Erreur sauvegarde de l'identifiant externe : '$msg'", UI_MSG_ERROR);
}
else {
  $AppUI->setMsg("Identifiant externe sauvegardé", UI_MSG_ERROR);
}

mbTrace($idExt->getValues(), "Id e-Cap");

if (null == $ajax = mbGetValueFromPost("ajax")) {
//  $AppUI->redirect();
}

?>