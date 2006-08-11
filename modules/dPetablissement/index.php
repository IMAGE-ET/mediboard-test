<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: $
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_idx_groups", "Groupes d'utilisateurs", 0);
$default = "vw_idx_groups";

$index = new CTabIndex($tabs, $default);
$index->show();

?>