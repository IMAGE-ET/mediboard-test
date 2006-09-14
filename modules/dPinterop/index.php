<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

$tabs = array();
$tabs[] = array("export_hprim", "Export HPRIM", 0);
$tabs[] = array("send_mail", "Envoie de mails", 0);
$tabs[] = array("benchmark", "Montée en charge", 0);

//$tabs[] = array("import_orl", "Import ORL", 0);
//$tabs[] = array("import_dermato", "Import Dermato", 0);

//$tabs[] = array("consult_anesth", "maj consult anesth", 0);
//$tabs[] = array("codes_ccam", "maj codes ccam", 0);
//$tabs[] = array("diag_patient", "maj diagnostics patients", 0);

$default = "send_mail";

$index = new CTabIndex($tabs, $default);
$index->show();

?>