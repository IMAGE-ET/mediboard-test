<?php 
/**
 * Test print
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$id = CValue::get("id");
$class_name = CValue::get("class_name");

$source = new $class_name;

$source->load($id);

$file = new CFile;
$file->_file_path = "modules/printing/samples/test_page.pdf";

$source->sendDocument($file);

?>