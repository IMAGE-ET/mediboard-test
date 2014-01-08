<?php /* $Id $ */

/**
 * Tooltip idex
 *
 * @category dPsante400
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

/** @var CIdSante400[] $identifiers */
$identifiers = $object->loadBackRefs("identifiants", "tag ASC, last_update DESC");

if ($identifiers) {
  foreach ($identifiers as $_idex) {
    $_idex->getSpecialType();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("identifiers", $identifiers);
$smarty->display("ajax_tooltip_identifiers.tpl");