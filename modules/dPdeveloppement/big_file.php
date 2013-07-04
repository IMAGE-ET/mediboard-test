<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
*/

CCanDo::checkRead();

$size = CValue::get("size", 1024 * 1024);

// max = 10MB
$size = min($size, 1024 * 1024 * 10);

$lorem = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. ";

$lorem_size = strlen($lorem);

$string = str_repeat($lorem, $size / $lorem_size);

ob_clean();

echo $string;

CApp::rip();