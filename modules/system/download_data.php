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
$filename = CValue::post("filename");
$mimetype = CValue::post("mimetype", "application/force-download");

$data = stripslashes($data);

ob_clean();

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-Disposition: attachment; filename=\"".rawurlencode($filename)."\"");
header("Content-Type: $mimetype");
header("Content-Length: ".strlen($data));
header("Content-Transfer-Encoding: binary");

echo $data;

CApp::rip();
