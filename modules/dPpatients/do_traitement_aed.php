<?php /* $Id: do_traitement_aed.php,v 1.1 2006/01/20 22:09:27 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('dPpatients', 'traitement') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CTraitement", "traitement_id");
$do->createMsg = "Traitement créé";
$do->modifyMsg = "Traitement modifié";
$do->deleteMsg = "Traitement supprimé";
$do->doIt();

?>