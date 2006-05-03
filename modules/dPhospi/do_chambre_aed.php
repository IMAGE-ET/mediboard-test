<?php /* $Id: do_chambre_aed.php,v 1.2 2005/04/04 09:07:16 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.2 $
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass($m, "chambre"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CChambre", "chambre_id");
$do->createMsg = "Chambre créée";
$do->modifyMsg = "Chambre modifiée";
$do->deleteMsg = "Chambre supprimée";
$do->doIt();
?>