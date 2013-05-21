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

$list  = CValue::get('id', array());
$owner = CValue::get('owner');
$object_class = CValue::get('object_class');

if (!is_array($list)) {
  $list = explode("-", $list);
}

$out = fopen('php://output', 'w');
header('Content-Type: application/csv');
header(
  'Content-Disposition: attachment;'.
  'filename="Aides saisie'. ($owner ? " - $owner" : '') . ($object_class ? " - ".CAppUI::tr($object_class) : '') . '.csv"'
);

$aide = new CAideSaisie();
fputcsv($out, array_keys($aide->getCSVFields()));

foreach ($list as $id) {
  if (!$aide->load($id)) {
    continue;
  }
  
  fputcsv($out, $aide->getCSVFields());
}
