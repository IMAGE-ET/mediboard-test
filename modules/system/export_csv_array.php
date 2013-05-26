<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$data     = CValue::post("data");
$filename = CValue::post("filename", "data");

$data = stripslashes($data);
$data = json_decode(utf8_encode($data), true);

$csv = new CCSVFile(null, "excel");

foreach ($data as $_line) {
  $csv->writeLine($_line);
}

$csv->stream($filename);

CApp::rip();
