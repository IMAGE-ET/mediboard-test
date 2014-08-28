<?php 

/**
 * $Id$
 *  
 * @category Forms
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$uid     = CValue::post("file_uid");
$from_db = CValue::post("fromdb");
$options = CValue::post("options");

$options["ignore_disabled_fields"] = isset($options["ignore_disabled_fields"]);

$uid = preg_replace('/[^\d]/', '', $uid);
$temp = CAppUI::getTmpPath("ex_class_import");
$file = "$temp/$uid";

$import = new CExClassImport($file);
try {
  $import->import($from_db, $options);
}
catch (Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING);
} 