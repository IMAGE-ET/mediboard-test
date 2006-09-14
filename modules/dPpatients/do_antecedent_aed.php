<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CAntecedent", "antecedent_id");
$do->createMsg = "Antecedent créé";
$do->modifyMsg = "Antecedent modifié";
$do->deleteMsg = "Antecedent supprimé";
$do->doIt();

?>