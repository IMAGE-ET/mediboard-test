<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_operations", "Salles d'opération", 0);
$tabs[] = array("vw_reveil"    , "Salle de reveil"   , 0);
$tabs[] = array("vw_urgences"  , "Liste des urgences", 0);
$default = "vw_operations";

$index = new CTabIndex($tabs, $default);
$index->show();

?>