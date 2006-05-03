<?php /* $Id: do_medecins_aed.php,v 1.2 2005/10/04 10:56:49 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.2 $
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('dPpatients', 'medecin') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CMedecin", "medecin_id");
$do->createMsg = "Medecin créé";
$do->modifyMsg = "Medecin modifié";
$do->deleteMsg = "Medecin supprimé";
$do->doIt();

?>