<?php 

/**
 * $Id$
 *  
 * @category Etablissement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$uid     = CValue::post("file_uid");
$from_db = CValue::post("fromdb");
$options = CValue::post("options");

$options = stripslashes_deep($options);

$uid = preg_replace('/[^\d]/', '', $uid);
$temp = CAppUI::getTmpPath("group_import");
$file = "$temp/$uid";

$import = new CGroupsImport($file);
try {
  $import->import($from_db, $options);
}
catch (Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING);
}