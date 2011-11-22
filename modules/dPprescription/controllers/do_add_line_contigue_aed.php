<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/*
 * Ajout d'une ligne et passage de la ligne
 * courante en historique
 */

CCanDo::checkRead();

$user = CUser::get();

$line_guid            = CValue::post("line_guid");
$prescription_id      = CValue::post("prescription_id");
$praticien_id         = CValue::post("praticien_id", $user->_id);
$mode_pharma          = CValue::post("mode_pharma");


$prescriptionLine = CMbObject::loadFromGuid($line_guid);
$new_line_guid = $prescriptionLine->duplicateLine($praticien_id, $prescription_id);

echo "<script type='text/javascript'>Prescription.reloadLine('$new_line_guid','','$mode_pharma')</script>";
echo CAppUI::getMsg();
CApp::rip();
?>