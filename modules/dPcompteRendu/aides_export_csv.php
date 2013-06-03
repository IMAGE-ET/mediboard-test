<?php

/**
 * Export CSV des aides à la saisie
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$list         = CValue::post('id', array());
$owner        = CValue::post('owner');
$object_class = CValue::post('object_class');

CMbObject::$useObjectCache = false;

if (!is_array($list)) {
  $list = explode("-", $list);
}

$filename = 'Aides saisie'. ($owner ? " - $owner" : '') . ($object_class ? " - ".CAppUI::tr($object_class) : '') . '.csv';

$out = fopen("php://output", "w");
header("Content-Type: application/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");

$aide = new CAideSaisie();
fputcsv($out, array_keys($aide->getCSVFields()));

foreach ($list as $id) {
  if (!$aide->load($id)) {
    continue;
  }
  
  fputcsv($out, $aide->getCSVFields());
}
