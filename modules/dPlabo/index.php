<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_prescriptions"   , null, TAB_READ);
$module->registerTab("vw_resultats"            , null, TAB_READ);
$module->registerTab("add_pack_exams"          , null, TAB_READ);
$module->registerTab("vw_edit_packs"           , null, TAB_READ);
$module->registerTab("vw_edit_catalogues"      , null, TAB_EDIT);
$module->registerTab("vw_edit_examens"         , null, TAB_EDIT);
$module->registerTab("vw_edit_idLabo"          , null, TAB_EDIT);

?>