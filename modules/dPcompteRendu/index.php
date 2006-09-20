<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_modeles"     , "liste des modèles"      , TAB_READ);
$module->registerTab("addedit_modeles", "Edition des modèles"    , TAB_READ);
$module->registerTab("vw_idx_aides"   , "Aides à la saisie"      , TAB_READ);
$module->registerTab("vw_idx_listes"  , "Listes de choix"        , TAB_READ);
$module->registerTab("vw_idx_packs"   , "Packs d'hospitalisation", TAB_READ);

?>