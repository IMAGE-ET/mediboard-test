<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id  = CValue::post("prescription_id");

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement des medicaments
$prescription->loadRefsLinesMed();
foreach($prescription->_ref_prescription_lines as &$_line_med) {
  if(!$_line_med->date_arret && $_line_med->signee) {
    $_line_med->date_arret = mbDate();
    $_line_med->time_arret = mbTime();
    CAppUI::displayMsg($_line_med->store(), "{$_line_med->_class}-msg-store");
  }
}

// Chargement des elements
$prescription->loadRefsLinesElement();
foreach($prescription->_ref_prescription_lines_element as &$_line_elt) {
  if(!$_line_elt->date_arret && $_line_elt->signee) {
    $_line_elt->date_arret = mbDate();
    $_line_elt->time_arret = mbTime();
    CAppUI::displayMsg($_line_elt->store(), "{$_line_elt->_class}-msg-store");
  }
}

// Chargement des perfusions
$prescription->loadRefsPrescriptionLineMixes();
foreach($prescription->_ref_prescription_line_mixes as &$_line_mix) {
  if(!$_line_mix->date_arret && $_line_mix->signature_prat) {
    $_line_mix->date_arret = mbDate();
    $_line_mix->time_arret = mbTime();
    CAppUI::displayMsg($_line_mix->store(), "{$_line_mix->_class}-msg-store");
  }
}

CAppUI::getMsg();
CApp::rip();

?>