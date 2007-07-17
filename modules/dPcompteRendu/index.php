<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_modeles"           , null, TAB_READ);
$module->registerTab("addedit_modeles"      , null, TAB_READ);
$module->registerTab("vw_idx_aides"         , null, TAB_READ);
$module->registerTab("vw_idx_listes"        , null, TAB_READ);
$module->registerTab("vw_idx_packs"         , null, TAB_READ);

?>