<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_idx_admission", "Consultation des admissions", 0);
$tabs[] = array("vw_idx_sortie", "Validation des sorties", 0);
$default = "vw_idx_admission";

$index = new CTabIndex($tabs, $default);
$index->show();

?>