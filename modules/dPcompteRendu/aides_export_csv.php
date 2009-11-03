<?php /* $Id: cellSaver.class.php 6103 2009-04-16 13:36:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: 6103 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$list  = CValue::get('id', array());
$owner = CValue::get('owner');
$object_class = CValue::get('object_class');

$out = fopen('php://output', 'w');
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="Aides saisie'. ($owner ? " - $owner" : '') . ($object_class ? " - ".CAppUI::tr($object_class) : '') . '.csv"');

$aide = new CAideSaisie();
fputcsv($out, array_keys($aide->getCSVFields()));

foreach($list as $id) {
  if (!$aide->load($id)) continue;
  fputcsv($out, $aide->getCSVFields());
}