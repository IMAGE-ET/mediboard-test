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

$list  = mbGetValueFromGet('id', array());
$owner = mbGetValueFromGet('owner');

$out = fopen('php://output', 'w');
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Aides saisie'. ($owner ? " - $owner" : '') .'.csv"');

$aide = new CAideSaisie();

$line = array();
foreach($aide->getDBFields() as $key => $value) {
  if (!$aide->_specs[$key] instanceof CRefSpec)
    $line[] = $key;
}
fputcsv($out, $line);

foreach($list as $id) {
  if (!$aide->load($id)) continue;
  
  $line = array();
  foreach($aide->getDBFields() as $key => $value) {
    if (!$aide->_specs[$key] instanceof CRefSpec)
      $line[] = $value;
  }
  fputcsv($out, $line);
}