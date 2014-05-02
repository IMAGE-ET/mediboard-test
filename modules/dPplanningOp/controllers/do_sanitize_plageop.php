<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$ds = CSQLDataSource::get("std");

$query = "UPDATE `operations`
            LEFT JOIN plagesop ON plagesop.plageop_id = `operations`.`plageop_id`
            SET `operations`.`date` = plagesop.date
            WHERE `operations`.plageop_id IS NOT NULL";

if (!$ds->exec($query)) {
  return CAppUI::stepAjax("Sanitize-failed", UI_MSG_ERROR, $ds->error());
}

CAppUI::stepAjax("Sanitize-ok");

CApp::rip();