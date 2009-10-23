<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$pack_protocole_id    = mbGetValueFromPost("pack_protocole_id");

$date_sel        = mbGetValueFromPost("debut", mbDate());
$praticien_id    = mbGetValueFromPost("praticien_id", $AppUI->user_id);
$operation_id    = mbGetValueFromPost("operation_id");
$pratSel_id      = mbGetValueFromPost("pratSel_id");
// Si aucun pack/protocole selectionne, on ne fait rien
if (!$pack_protocole_id){
  CApp::rip();
}

// Chargement de la prescription
$prescription = new CPrescription();
if ($prescription_id) {
  $prescription->load($prescription_id);
} else {
  $operation = new COperation();
  $operation->load($operation_id);
  $prescription->object_class = 'CSejour';
  $prescription->object_id = $operation->sejour_id;
  $prescription->type = 'sejour';

  if ($msg = $prescription->store()) {
	  $AppUI->setMsg($msg, UI_MSG_ERROR);
	}

}

// On applique le protocole ou le pack
$prescription->applyPackOrProtocole($pack_protocole_id, $praticien_id, $date_sel, $operation_id);

$lite = CAppUI::pref('mode_readonly') ? 0 : 1;

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id, null, null, null, null, null, null, true, $lite, null, '$pratSel_id', null, '$praticien_id')</script>";
echo $AppUI->getMsg();
CApp::rip();
?>

