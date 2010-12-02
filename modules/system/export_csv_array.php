<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$data     = CValue::post("data");
$filename = CValue::post("filename", "data");

$data = stripslashes($data);
$data = json_decode(utf8_encode($data), true);

$out = fopen("php://output", "w");
header("Content-Type: application/csv");
header("Content-Disposition: attachment; filename=\"$filename.csv\"");

foreach($data as $_line) {
  fputcsv($out, array_map("utf8_decode", $_line), ";");
}
