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

$csv = new CCSVFile(null, "excel");

foreach($data as $_line) {
  $csv->writeLine($_line);
}

$csv->stream($filename);

CApp::rip();
