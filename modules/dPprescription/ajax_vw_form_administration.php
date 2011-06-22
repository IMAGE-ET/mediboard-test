<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_guid = CValue::getOrSession("object_guid");
$datetime = CValue::getOrSession("datetime");

// Chargement de l'objet
$line = CMbObject::loadFromGuid($object_guid);

$line->loadRefPrescription();

if($line instanceof CPrescriptionLineMedicament){
	$line->_ref_produit->loadUnitePresentation();
}

$transmission = new CTransmissionMedicale;
$transmission->sejour_id = $line->_ref_prescription->_ref_object->_id;
$transmission->user_id = CAppUI::$user->_id;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("datetime", $datetime);
$smarty->assign("administration", new CAdministration());
$smarty->assign("transmission", $transmission);
$smarty->assign("date", mbDate());
$smarty->assign("hour", mbTransformTime(null, mbTime(), "%H"));
$smarty->assign("user_id", CUser::get()->_id);
$smarty->display("inc_form_administration.tpl");

?>