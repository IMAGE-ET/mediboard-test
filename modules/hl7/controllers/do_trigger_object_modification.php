<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$patient = new CPatient;
while(!$patient->load(rand(1, 5000)));

// randomize name
$nom = str_split($patient->nom);
shuffle($nom);
$patient->nom = implode("", $nom);

mbTrace($patient->_id, $patient->_view);

CAppUI::displayMsg($patient->store(), "CPatient-msg-modify");
echo CAppUI::getMsg();

CApp::rip();
