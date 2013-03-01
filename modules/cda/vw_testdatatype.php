<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */


$result = array();
$CCDAANY = new CCDAANY();
$result[] = $CCDAANY->test();

$CCDAANYNonNull = new CCDAANYNonNull();
$result[] = $CCDAANYNonNull->test();

$CCDA_cs = new CCDA_cs();
$result[] = $CCDA_cs->test();

$test = new CCDAAddressPartType();
$result[] = $test->test();

$smarty = new CSmartyDP();

$smarty->assign("result", $result);

$smarty->display("vw_testdatatype.tpl");

/*$CCDATools = new CCdaTools();
$CCDATools->createClass();*/