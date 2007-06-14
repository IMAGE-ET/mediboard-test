<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPackExamensLabo", "pack_examens_labo_id");
$do->createMsg = "Pack créé";
$do->modifyMsg = "Pack modifié";
$do->deleteMsg = "Pack supprimé";
$do->doIt();

?>