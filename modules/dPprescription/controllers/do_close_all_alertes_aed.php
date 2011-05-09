<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::post("prescription_id");
$level = CValue::post("level");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$prescription->loadRefsAlertes($level);

// Fermeture de toutes les alertes
foreach($prescription->_ref_alertes as $_alert){
	$_alert->handled = '1';
	$_alert->store();
}
CAppUI::stepAjax("Alertes fermées");
CApp::rip();

?>