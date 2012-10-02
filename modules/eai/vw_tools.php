<?php 
/**
 * View tools EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkAdmin();

$exchanges_classes = array();
foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {  
  foreach (CApp::getChildClasses($_exchange_class, array(), true) as $under_key => $_under_class) {
    $class = new $_under_class;
    $class->countExchanges();
    $exchanges_classes[$_exchange_class][] = $class;
  }
  if ($_exchange_class == "CExchangeAny") {
    $class = new CExchangeAny();
    $class->countExchanges();
    $exchanges_classes["CExchangeAny"][] = $class;
  }
}

$group = new CGroups();
$groups = $group->loadList();
foreach ($groups as $_group) {
  $_group->loadConfigValues(); 
}  

$tools = array(
  "exchanges" => array(
    "reprocessing",
    "detect_collision"
  )
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchanges_classes", $exchanges_classes);
$smarty->assign("groups"           , $groups);
$smarty->assign("tools"            , $tools);
$smarty->display("vw_tools.tpl");

?>