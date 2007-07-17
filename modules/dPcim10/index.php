<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_find_code"  , null, TAB_READ);
$module->registerTab("vw_full_code"  , null, TAB_READ);
$module->registerTab("vw_idx_chapter", null, TAB_READ);
$module->registerTab("vw_idx_favoris", null, TAB_READ);

?>