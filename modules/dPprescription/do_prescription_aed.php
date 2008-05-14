<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Romain Ollivier
*/

//mbDump($_POST, "POST");
//die;

$do = new CDoObjectAddEdit("CPrescription", "prescription_id");
$do->doIt();

?>