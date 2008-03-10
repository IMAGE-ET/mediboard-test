<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLineComment", "prescription_line_comment_id");
$do->createMsg = "Commentaire ajouté";
$do->modifyMsg = "Commentaire modifié";
$do->deleteMsg = "Commentaire supprimé";
$do->doIt();

?>