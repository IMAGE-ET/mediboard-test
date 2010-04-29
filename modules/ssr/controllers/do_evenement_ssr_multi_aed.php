<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id     = CValue::post("sejour_id");
$cdarr         = CValue::post("cdarr");
$equipement_id = CValue::post("equipement_id");
$therapeute_id = CValue::post("therapeute_id");
$line_id       = CValue::post("line_id");

$_days = CValue::post("_days");
$_heure = CValue::post("_heure");
$duree = CValue::post("duree");

// Chargement de la ligne et recuperation de l'id de l'element de prescription
$line_element = new CPrescriptionLineElement();
$line_element->load($line_id);
$element_prescription_id = $line_element->element_prescription_id;

if(count($_days)){
	foreach($_days as $_day){
		if(!$_heure || !$duree){
			continue;
		}
		$evenement_ssr = new CEvenementSSR();
		$evenement_ssr->sejour_id = $sejour_id;
		$evenement_ssr->code = $cdarr;
	  $evenement_ssr->equipement_id = $equipement_id;
	  $evenement_ssr->therapeute_id = $therapeute_id;
		$evenement_ssr->debut = "$_day $_heure";
	  $evenement_ssr->duree = $duree;
		$evenement_ssr->element_prescription_id = $element_prescription_id;
	  $msg = $evenement_ssr->store();
		CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
	}
}
echo CAppUI::getMsg();
CApp::rip();

?>