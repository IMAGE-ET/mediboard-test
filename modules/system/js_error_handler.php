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

errorHandler(E_JS_ERROR, $errorMsg, $url, $lineNumber);
