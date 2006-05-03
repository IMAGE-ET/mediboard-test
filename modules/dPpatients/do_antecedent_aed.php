<?php /* $Id: do_antecedent_aed.php,v 1.1 2005/10/18 10:43:44 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('dPpatients', 'antecedent') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CAntecedent", "antecedent_id");
$do->createMsg = "Antecedent créé";
$do->modifyMsg = "Antecedent modifié";
$do->deleteMsg = "Antecedent supprimé";
$do->doIt();

?>