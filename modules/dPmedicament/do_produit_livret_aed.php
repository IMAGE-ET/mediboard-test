<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CProduitLivretTherapeutique", "produit_livret_id");
$do->createMsg = "Element créé";
$do->modifyMsg = "Element modifié";
$do->deleteMsg = "Element supprimé";
$do->doIt();

?>