<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCatalogueLabo", "catalogue_labo_id");
$do->createMsg = "Catalogue créé";
$do->modifyMsg = "Catalogue modifié";
$do->deleteMsg = "Catalogue supprimé";
$do->doIt();

?>