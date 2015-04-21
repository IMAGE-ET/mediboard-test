<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$size = CValue::get("size", 1024 * 1024);

// max = 10MB
$size = min($size, 1024 * 1024 * 10);

$lorem = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy ";
$lorem.= "nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. ";

$lorem_size = strlen($lorem);

$string = str_repeat($lorem, $size / $lorem_size);

ob_clean();

echo $string;

CApp::rip();