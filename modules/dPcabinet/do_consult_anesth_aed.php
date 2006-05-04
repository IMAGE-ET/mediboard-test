<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("dPcabinet", "consultAnesth"));

if ($chir_id = dPgetParam( $_POST, 'chir_id'))
  mbSetValueToSession('chir_id', $chir_id);

$do = new CDoObjectAddEdit("CConsultAnesth", "consultation_anesth_id");
$do->createMsg = "Consultation créée";
$do->modifyMsg = "Consultation modifiée";
$do->deleteMsg = "Consultation supprimée";
$do->doIt();

?>
