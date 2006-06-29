<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_activite", "Activite", 0);
$tabs[] = array("vw_hospitalisation", "Hospitalisation", 0);
$tabs[] = array("vw_bloc", "Bloc opératoire", 0);
$tabs[] = array("vw_time_op", "Temps opératoires", 0);
$tabs[] = array("vw_users", "Utilisateurs", 0);
$default = "vw_activite";

$index = new CTabIndex($tabs, $default);
$index->show();

?>