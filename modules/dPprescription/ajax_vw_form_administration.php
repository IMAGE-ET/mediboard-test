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

if($line instanceof CPrescriptionLineMedicament){
	$line->_ref_produit->loadUnitePresentation();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("datetime", $datetime);
$smarty->assign("administration", new CAdministration());
$smarty->display("inc_form_administration.tpl");

?>