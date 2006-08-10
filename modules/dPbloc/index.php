<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_planning_week", "Planning de la semaine", 1);
$tabs[] = array("vw_edit_planning", "Planning du jour", 1);
$tabs[] = array("vw_edit_interventions", "Gestion des interventions", 1);
$tabs[] = array("vw_urgences", "Voir les urgences", 1);
$tabs[] = array("vw_idx_materiel", "Commande de matériel", 1);
$tabs[] = array("vw_idx_salles", "Gestion des salles", 1);
$tabs[] = array("print_planning", "Impression des plannings", 0);
$default = 0;

$index = new CTabIndex($tabs, $default);
$index->show();

?>