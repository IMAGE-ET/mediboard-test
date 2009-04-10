<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit("CIdSante400", "id_sante400_id");

// Indispensable pour ne pas écraser les paramètes dans action
if(!isset($_POST["ajax"]) || !$_POST["ajax"]) {
  $do->redirect = null;
}
$do->doIt();

?>