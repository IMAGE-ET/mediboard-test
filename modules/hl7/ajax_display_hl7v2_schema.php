<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireLibraryFile("geshi/geshi");

CCanDo::checkRead();

$path = CValue::get("path");

echo "<h1>$path</h1>";

$geshi = new Geshi(file_get_contents($path), "xml");
$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
$geshi->set_overall_style("max-height: 100%; white-space:pre-wrap;");
$geshi->enable_classes();
echo $geshi->parse_code();

?>