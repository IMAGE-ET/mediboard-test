<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id = CValue::post("sejour_id");

$evenement = new CEvenementSSR();
CAppUI::displayMsg("Presque terminée...", "Fonctionnalité");

echo CAppUI::getMsg();
CApp::rip();

?>