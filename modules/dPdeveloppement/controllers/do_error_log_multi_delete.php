<?php

/**
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$ids = CValue::post("log_ids");

if ($ids) {
  $ids = explode("-", $ids);

  $error_log = new CErrorLog();
  $error_log->deleteMulti($ids);
}