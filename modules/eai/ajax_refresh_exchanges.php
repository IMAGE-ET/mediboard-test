<?php 
/**
 * Refresh exchanges
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_class_name = CValue::getOrSession("exchange_class_name");

$_date_min = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max = CValue::getOrSession('_date_max', mbDateTime("+1 day"));
$page      = CValue::getOrSession('value', 0);

// Types filtres qu'on peut prendre en compte
$filtre_types = array('no_date_echange', 'emetteur', 'destinataire', 'message_invalide', 'acquittement_invalide');

$types = array();
foreach ($filtre_types as $type) {
  $types[$type] = !isset($t) || in_array($type, $t);
}

$exchange = new $exchange_class_name;
$exchange->_date_min = $_date_min;
$exchange->_date_max = $_date_max;

$class    = new ReflectionClass($exchange);
$statics  = $class->getStaticProperties();
$messages = $statics["messages"];

$evenements = array();
foreach ($statics["messages"] as $_message => $_evt_class) {
  $class = new ReflectionClass($_evt_class);
  $statics = $class->getStaticProperties();
  $evenements[$_message] = $statics["evenements"];
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchange"  , $exchange);
$smarty->assign("types"     , $types);
$smarty->assign("page"      , $page);
$smarty->assign("messages"  , $messages);
$smarty->assign("evenements", $evenements);
$smarty->display("inc_filters_exchanges.tpl");


?>