<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::getOrSession("sejour_id");

if ($sejour_id) {
  echo CApp::fetch("soins", "ajax_vw_dossier_sejour", array("sejour_id" => $sejour_id, "popup" => 1));
}

?>