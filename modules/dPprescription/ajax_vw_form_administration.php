<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_guid = CValue::getOrSession("object_guid");
$datetime    = CValue::getOrSession("datetime");
$administration = new CAdministration();

// Chargement de l'objet
$line = CMbObject::loadFromGuid($object_guid);

$line->loadRefPrescription();

if($line instanceof CPrescriptionLineMedicament){
	$line->_ref_produit->loadUnitePresentation();
}

// Si la quantité est obligatoire, pré-remplissage de la quantité à 1 pour les soins
if (CAppUI::conf("dPprescription CPrescription qte_obligatoire_inscription") &&
    $line instanceof CPrescriptionLineElement &&
    $line->_ref_element_prescription->_ref_category_prescription->chapitre == "soin") {
  $administration->quantite = 1;
}

$transmission = new CTransmissionMedicale;
$transmission->sejour_id = $line->_ref_prescription->_ref_object->_id;
$transmission->user_id = CAppUI::$user->_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("line"          , $line);
$smarty->assign("datetime"      , $datetime);
$smarty->assign("administration", $administration );
$smarty->assign("transmission"  , $transmission);
$smarty->assign("date"          , mbDate());
$smarty->assign("hour"          , mbTransformTime(null, mbTime(), "%H"));

$smarty->display("inc_form_administration.tpl");

?>