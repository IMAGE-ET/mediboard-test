<?php /* $Id: do_exam_audio_aed.php,v 1.5 2005/12/02 09:59:14 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once($AppUI->getModuleClass("dPcabinet", "examaudio"));

mbSetValueToSession("_conduction", $_POST["_conduction"]);
mbSetValueToSession("_oreille", $_POST["_oreille"]);

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->createMsg = "Examen audio créé";
$do->modifyMsg = "Examen audio modifié";
$do->deleteMsg = "Examen audio supprimé";
$do->redirect = null;
$do->doIt();
?>
