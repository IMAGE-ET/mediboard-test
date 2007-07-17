<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_admission", null, TAB_READ);
$module->registerTab("vw_idx_sortie"   , null, TAB_READ);
$module->registerTab("vw_idx_consult"  , null, TAB_READ);

?>