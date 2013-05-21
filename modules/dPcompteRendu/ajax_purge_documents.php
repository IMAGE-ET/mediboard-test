<?php

/**
 * Tâche automatique de suppression de documents déclarés temporaires
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$modele = new CCompteRendu();
$limit = CValue::get("limit", 100);

$where = array();
$where["object_id"] = "IS NULL";
$where["purgeable"] = "= '1'";

$modeles = $modele->loadList($where);

foreach ($modeles as $_modele) {
  $documents = $_modele->loadBackRefs("documents_generated", null, $limit);
  foreach ($documents as $_doc) {
    $_doc->delete();
  }
}
