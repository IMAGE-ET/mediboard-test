<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPmateriel", "fournisseur"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFournisseur", "fournisseur_id");
$do->createMsg = "Fournisseur créé";
$do->modifyMsg = "Fournisseur modifié";
$do->deleteMsg = "Fournisseur supprimé";
$do->doIt();

?>