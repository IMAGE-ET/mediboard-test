<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

$tabs = array();
$tabs[] = array("vw_dossier", "Dossiers patient", 0);
$tabs[] = array("edit_actes", "Codage des actes", 0);
$tabs[] = array("labo_groupage", "Groupage GHM", 0);
$tabs[] = array("vw_list_hospi", "Liste des hospitalisations", 0);
$default = "vw_dossier";

$index = new CTabIndex($tabs, $default);
$index->show();

?>