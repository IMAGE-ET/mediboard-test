<?php /* $Id: do_favoris_aed.php,v 1.5 2005/10/15 16:58:45 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPcim10", "favoricim10"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CFavoricim10", "favoris_id");
$do->createMsg = "Favori créé";
$do->modifyMsg = "Favori modifié";
$do->deleteMsg = "Favori supprimé";
$do->doIt();

?>