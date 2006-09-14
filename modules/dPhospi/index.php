<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

$tabs = array();
$tabs[] = array("form_print_planning", "Impression des plannings", 0);
$tabs[] = array("edit_sorties", "Déplacements / Sorties", 0);
$tabs[] = array("vw_recherche", "Chercher une chambre", 0);
$tabs[] = array("vw_affectations", "Affectations", 0);
$tabs[] = array("vw_idx_chambres", "Chambres", 1);
$tabs[] = array("vw_idx_services", "Services", 1);
$default = "form_print_planning";

$index = new CTabIndex($tabs, $default);
$index->show();

?>