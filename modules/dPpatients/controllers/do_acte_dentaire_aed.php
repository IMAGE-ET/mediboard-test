<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$_POST['devenir_dentaire_id'] = CDevenirDentaire::devenirDentairelId($_POST['_patient_id']);

/*if (!preg_match("/[A-Z]{4}[0-9]{3}/i", $_POST["code"])) {
  CAppUI::setMsg("Le code CCAM '".$POST['code']."' n'est pas valide", UI_MSG_ERROR);
}*/


$do = new CDoObjectAddEdit('CActeDentaire');

$do->doIt();

?>