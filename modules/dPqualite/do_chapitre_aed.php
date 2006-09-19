<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;


$do = new CDoObjectAddEdit("CChapitreDoc", "doc_chapitre_id");
$do->createMsg = "Chapitre créé";
$do->modifyMsg = "Chapitre modifié";
$do->deleteMsg = "Chapitre supprimé";
$do->doIt();

?>