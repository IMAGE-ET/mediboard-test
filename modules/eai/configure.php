<?php 

/**
 * Configure
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$object_servers = array(
  "sip" => "CSipObjectHandler",
  "smp" => "CSmpObjectHandler",
  "sms" => "CSmsObjectHandler",
  "sa"  => "CSaObjectHandler",
);

$group = new CGroups();
$groups = $group->loadList();
foreach ($groups as $_group) {
  $_group->loadConfigValues(); 
}      

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object_servers", $object_servers);
$smarty->assign("groups"        , $groups);
$smarty->display("configure.tpl");

?>