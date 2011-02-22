<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

$printer_id = CValue::get("printer_id");
$file_id    = CValue::get("file_id");

$file = new CFile();
$file->load($file_id);

$printer = new CPrinter();
$printer->load($printer_id);

$printer->loadRefSource()->sendDocument($file);

?>