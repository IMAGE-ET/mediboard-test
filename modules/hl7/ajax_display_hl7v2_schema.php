<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$path = CValue::get("path");

echo CMbString::purifyHTML("<h1>$path</h1>");
echo CMbString::highlightCode("xml", file_get_contents($path));
