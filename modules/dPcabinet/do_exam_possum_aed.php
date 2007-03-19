<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CExamPossum", "exampossum_id");
$do->createMsg = "Examen score possum créé";
$do->modifyMsg = "Examen score possum modifié";
$do->deleteMsg = "Examen score possum supprimé";
$do->redirect = null;
$do->doIt();
?>