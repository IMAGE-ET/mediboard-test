<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("send_mail"     , "Envoie de mails"   , TAB_READ);
$module->registerTab("export_hprim"  , "Export HPRIM"      , TAB_READ);
$module->registerTab("benchmark"     , "Montée en charge"  , TAB_READ);
$module->registerTab("import_ami"    , "Import AMI"        , TAB_READ);

//$module->registerTab("import_orl"    , "Import ORL"        , TAB_READ);
//$module->registerTab("import_dermato", "Import Dermato"    , TAB_READ);

//$module->registerTab("consult_anesth", "maj consult anesth", TAB_READ);
//$module->registerTab("codes_ccam"    , "maj codes ccam"    , TAB_READ);
//$module->registerTab("diag_patient"  , "maj diags patients", TAB_READ);

?>