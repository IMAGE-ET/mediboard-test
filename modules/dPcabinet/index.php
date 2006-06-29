<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_planning", "Programmes de consultation", 0);
$tabs[] = array("edit_planning", "Créer / Modifier un rendez-vous", 0);
$tabs[] = array("edit_consultation", "Consultation", 0);
$tabs[] = array("vw_dossier", "Dossiers", 0);
$tabs[] = array("form_print_plages", "Impression des plannings", 0);
$tabs[] = array("vw_compta", "Comptabilité", 0);
$default = "vw_planning";

$index = new CTabIndex($tabs, $default);
$index->show();

?>