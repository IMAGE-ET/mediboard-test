<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_files", "Visualiser les fichiers", 0);
$tabs[] = array("configure", "Gérer les catégories", 0);
$default = "vw_files";

$index = new CTabIndex($tabs, $default);
$index->show();

?>