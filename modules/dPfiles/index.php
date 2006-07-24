<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("test", "test", 0);
$default = "test";

$index = new CTabIndex($tabs, $default);
$index->show();

?>