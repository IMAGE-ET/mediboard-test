<?php
/**
 * Is extract passages exist ?
 *
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$extract_passages_id = CValue::get("extract_passages_id");

$extractPassages = new CExtractPassages();

$msg = "";
if ($extract_passages_id) {
  $extractPassages->load($extract_passages_id);

  if ($extractPassages->_id) {
    $msg = $extractPassages->_id;
  }
}

echo json_encode($msg);