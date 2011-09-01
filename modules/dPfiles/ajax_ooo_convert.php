<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$file_data = base64_decode(CValue::post("file_data"));

$pdf_path  = CValue::post("pdf_path");

$temp_name_from = tempnam("./tmp", "from");
file_put_contents($temp_name_from, $file_data);

$path_python = CAppUI::conf("dPfiles CFile python_path") ? CAppUI::conf("dPfiles CFile python_path") ."/": "";
$res = exec("{$path_python}python ./modules/dPfiles/script/doctopdf.py {$temp_name_from} {$pdf_path}");

@unlink($temp_name_from);

echo $res;
?>