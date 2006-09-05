<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: $
* @author Thomas Despoix
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("view_identifiants", "Identifiants Santé400", 1);
$default = "view_identifiants";

$index = new CTabIndex($tabs, $default);
$index->show();

?>