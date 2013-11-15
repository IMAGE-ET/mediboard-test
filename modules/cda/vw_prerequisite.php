<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$group      = CGroups::loadCurrent();
$tag        = "cda_association_code";
$category   = new CFilesCategory();
$categories = $category->loadGroupList();
$mb_oid     = CAppUI::conf("mb_oid");

$group->loadLastId400($tag);

$type_group = CCdaTools::loadJV("CI-SIS_jdv_healthcareFacilityTypeCode.xml");

$java = false;
$processorInstance = proc_open("java -verbose -version", array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
$processorResult = stream_get_contents($pipes[1]);
$processorErrors = stream_get_contents($pipes[2]);
proc_close($processorInstance);
if ($processorResult) {
  $java = true;
}

$smarty = new CSmartyDP();
$smarty->assign("group"     , $group);
$smarty->assign("mb_oid"    , $mb_oid);
$smarty->assign("java"      , $java);
$smarty->assign("type_group", $type_group);
$smarty->display("vw_prerequisite.tpl");