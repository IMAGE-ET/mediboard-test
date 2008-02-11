<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CExamIgs", "examigs_id");
$do->createMsg = "Examen IGS créé";
$do->modifyMsg = "Examen IGS modifié";
$do->deleteMsg = "Examen IGS supprimé";
$do->redirect = null;
$do->doIt();
?>