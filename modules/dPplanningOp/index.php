<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

$tabs = array();
$tabs[] = array("vw_idx_planning", "Consulter le planning", 1);
$tabs[] = array("vw_edit_planning", "Planifier / Modifier une intervention", 1);
$tabs[] = array("vw_edit_sejour", "Planifier / Modifier un séjour", 0);
$tabs[] = array("vw_edit_urgence", "Planifier / Modifier une urgence", 0);
$tabs[] = array("vw_protocoles", "Protocoles", 1);
$tabs[] = array("vw_edit_protocole", "Créer / Modifier un protocole", 1);
$tabs[] = array("vw_edit_typeanesth", "Gérer les types d'anesthesie", 1);
$default = 0;

$index = new CTabIndex($tabs, $default);
$index->show();

?>