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

$CCDATools = new CCdaTools();
$result = $CCDATools->createTest();
$resultSynth = $CCDATools->syntheseTest($result);

$smarty = new CSmartyDP();

$smarty->assign("result", $result);
$smarty->assign("resultsynth", $resultSynth);

$smarty->display("vw_testdatatype.tpl");