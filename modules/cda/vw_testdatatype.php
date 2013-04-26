<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
* @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
* @link     http://www.mediboard.org
*/

$action = CValue::get("action", "null");

$CCDATools = new CCdaTools();
$result = null;
$resultSynth = null;

if ($action !== "null") {
  $result = $CCDATools->createTest($action);
  $resultSynth = $CCDATools->syntheseTest($result);
}

$smarty = new CSmartyDP();

$smarty->assign("result", $result);
$smarty->assign("resultsynth", $resultSynth);
$smarty->assign("action", $action);

$smarty->display("vw_testdatatype.tpl");