<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_idx_favoris", "Mes favoris", 0);
$tabs[] = array("vw_find_code", "Rechercher un code", 0);
$tabs[] = array("vw_full_code", "Affichage d'un code", 0);
$tabs[] = array("vw_idx_chapter", "Sommaire de la CIM10", 0);
$default = "vw_find_code";

$index = new CTabIndex($tabs, $default);
$index->show();

?>