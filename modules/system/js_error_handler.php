<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$errorMsg   = mbGetValueFromPost('errorMsg');
$url        = mbGetValueFromPost('url');
$lineNumber = mbGetValueFromPost('lineNumber');
$stack      = mbGetValueFromPost('stack');

$stackTrace = array();
$stack = explode("\n", $stack);

foreach($stack as $trace) {
	if (preg_match("/(?<function>.*)\((?<args>.*)\)@(?<file>.*):(?<line>.*)/", $trace, $matches)) {
		if (empty($matches["function"]))
		  $matches["function"] = "[anonymous]";
			
		mbTrace($matches);
		$stackTrace[] = $matches;
	}
}

errorHandler(E_JS_ERROR, $errorMsg, $url, $lineNumber, null, $stackTrace);
