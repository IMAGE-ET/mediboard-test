<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CActeDentaire');

if ((!isset($_POST["del"]) || $_POST["del"] == 0) && isset($_POST["code"])) {
  $_POST["ICR"] = CActeDentaire::searchICR($_POST["code"]);
}

$do->doIt();

?>