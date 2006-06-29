<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_idx_mediusers", "Utilisateurs", 0);
$tabs[] = array("vw_idx_functions", "Fonctions des utilisateurs", 0);
$tabs[] = array("vw_idx_groups", "Groupes d'utilisateurs", 0);
$tabs[] = array("vw_idx_disciplines", "Spécialités médicales", 0);
$default = "vw_idx_mediusers";

$index = new CTabIndex($tabs, $default);
$index->show();

?>