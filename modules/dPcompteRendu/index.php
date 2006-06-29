<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_modeles", "liste des modèles", 0);
$tabs[] = array("addedit_modeles", "Edition des modèles", 0);
$tabs[] = array("vw_idx_aides", "Aides à la saisie", 0);
$tabs[] = array("vw_idx_listes", "Listes de choix", 0);
$tabs[] = array("vw_idx_packs", "Packs d'hospitalisation", 0);
$default = "vw_modeles";

$index = new CTabIndex($tabs, $default);
$index->show();

?>