<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CFournisseur", "fournisseur_id");
$do->createMsg = "Fournisseur créé";
$do->modifyMsg = "Fournisseur modifié";
$do->deleteMsg = "Fournisseur supprimé";
$do->doIt();

?>