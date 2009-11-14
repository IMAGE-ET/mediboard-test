<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_modeles"           , TAB_READ);
$module->registerTab("addedit_modeles"      , TAB_READ);
$module->registerTab("vw_idx_aides"         , TAB_READ);
$module->registerTab("vw_idx_listes"        , TAB_READ);
$module->registerTab("vw_idx_packs"         , TAB_READ);

?>