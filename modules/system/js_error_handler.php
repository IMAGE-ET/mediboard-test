<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$errorMsg   = CValue::post('errorMsg');
$url        = CValue::post('url');
$lineNumber = CValue::post('lineNumber');
$stack      = CValue::post('stack');

$stackTrace = array();
$stack = explode("\n", $stack);

foreach($stack as $trace) {
	if (preg_match("/(?P<function>.*)\((?P<args>.*)\)@(?P<file>.*):(?P<line>.*)/", $trace, $matches)) {
		if (empty($matches["function"])) {
		  $matches["function"] = "[anonymous]";
    }
		$stackTrace[] = $matches;
	}
}

if (!count($stackTrace))
	$errorMsg .= " Extended message: ".implode(" -- \n", $stack);

errorHandler(E_JS_ERROR, $errorMsg, $url, $lineNumber, null, $stackTrace);
