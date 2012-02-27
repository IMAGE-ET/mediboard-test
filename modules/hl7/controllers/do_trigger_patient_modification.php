<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$action = CValue::post("action", "modify");

$patient = new CPatient;

switch($action) {
  case "modify":
    while(!$patient->load(rand(1, 5000)));
    
    // randomize name
    $nom = str_split($patient->nom);
    shuffle($nom);
    $patient->nom = implode("", $nom);
  break;
  
  case "create":
    $patient->sample();
    //$patient->updateFormFields();
  break;
}

CAppUI::displayMsg($patient->store(), "CPatient-msg-$action");

//mbTrace($patient);

echo CAppUI::getMsg();

CApp::rip();
