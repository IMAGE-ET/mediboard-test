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

$sejour = new CSejour;

switch($action) {
  case "modify":
    while(!$sejour->load(rand(1, 5000)));
    
    // randomize libelle
    $sejour->libelle = $sejour->libelle ? $sejour->libelle : "un libelle pour le mettre dans l'ordre";
    $libelle = str_split($sejour->libelle);
    shuffle($libelle);
    $sejour->libelle = implode("", $libelle);
  break;
  
  case "create":
    //$sejour->sample();
    $sejour->group_id      = 1;
    $sejour->praticien_id  = 73;
    $sejour->patient_id    = rand(1, 5000);
    $sejour->entree_prevue = CMbDT::dateTime();
    $sejour->sortie_prevue = CMbDT::dateTime("+1 day");
    //$patient->updateFormFields();
  break;
}

CAppUI::displayMsg($sejour->store(), "CSejour-msg-$action");

mbTrace($sejour);

echo CAppUI::getMsg();

CApp::rip();
