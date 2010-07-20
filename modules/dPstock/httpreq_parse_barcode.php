<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$barcode = CValue::get("barcode");

$parsed = CBarcodeParser::parse($barcode);

CApp::json($parsed);
