<?php

/**
 * $Id$
 *
 * @category PlanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/////////////////
CApp::rip(); // Pour eviter les mauvaises surprises, supprimer cette ligne pour utiliser le script
/////////////////

CCanDo::checkAdmin();

$query = <<<SQL
SELECT user_log_id, object_id FROM user_log WHERE
  object_class = 'CSejour' AND
  extra     LIKE '%"entree_reelle":""%' AND
  extra     LIKE '%"etablissement_entree_id":%'AND
  extra NOT LIKE '%"etablissement_entree_id":""%'
SQL;

$ds = CSQLDataSource::get("std");
$logs = $ds->loadList($query);

foreach ($logs as $_log) {
  $user_log = new CUserLog();
  $user_log->load($_log["user_log_id"]);

  $values = $user_log->getOldValues();

  /** @var CSejour $sejour */
  $sejour = $user_log->loadTargetObject();
  $sejour->etablissement_entree_id = $values["etablissement_entree_id"];
  $sejour->mode_entree = "transfert";

  if ($msg = $sejour->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
}

echo CAppUI::getMsg();
