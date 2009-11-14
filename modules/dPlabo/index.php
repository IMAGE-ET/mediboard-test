<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_prescriptions"   , TAB_READ);
$module->registerTab("vw_resultats"            , TAB_READ);
$module->registerTab("add_pack_exams"          , TAB_READ);
$module->registerTab("vw_edit_packs"           , TAB_READ);
$module->registerTab("vw_edit_catalogues"      , TAB_EDIT);
$module->registerTab("vw_edit_examens"         , TAB_EDIT);
$module->registerTab("vw_edit_idLabo"          , TAB_EDIT);

?>