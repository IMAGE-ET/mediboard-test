<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canView, $m;

$tabs = array();
$tabs[] = array("vw_idx_mediusers", "Utilisateurs", 0);
$tabs[] = array("vw_idx_functions", "Fonctions des utilisateurs", 0);
$tabs[] = array("vw_idx_disciplines", "Spécialités médicales", 0);
$default = "vw_idx_mediusers";

?>