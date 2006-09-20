<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_find_code"  , "Rechercher un code"  , TAB_READ);
$module->registerTab("vw_full_code"  , "Rechercher un code"  , TAB_READ);
$module->registerTab("vw_idx_chapter", "Sommaire de la CIM10", TAB_READ);
$module->registerTab("vw_idx_favoris", "Mes favoris"         , TAB_READ);

?>