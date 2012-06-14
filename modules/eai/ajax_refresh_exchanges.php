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

$exchange_class    = CValue::getOrSession("exchange_class");
$exchange_type     = CValue::getOrSession("exchange_type");
$exchange_group_id = CValue::getOrSession("exchange_group_id");

$keywords          = CValue::getOrSession("keywords");

$_date_min = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max = CValue::getOrSession('_date_max', mbDateTime("+1 day"));
$page      = CValue::getOrSession('value', 0);

// Types filtres qu'on peut prendre en compte
$filtre_types = array('no_date_echange', 'emetteur', 'destinataire', 'message_invalide', 'acquittement_invalide');

$types = array();
foreach ($filtre_types as $type) {
  $types[$type] = !isset($t) || in_array($type, $t);
}

$exchange = new $exchange_class;
$exchange->_date_min = $_date_min;
$exchange->_date_max = $_date_max;
$exchange->type      = $exchange_type;
$exchange->group_id  = $exchange_group_id;

$messages = $exchange->getFamily();
$evenements = array();
foreach ($messages as $_message => $_evt_class) { 
  $evt  = new $_evt_class;
  $evts = $evt->getEvenements(); 
  $keys       = array_map_recursive(array("CMbString" , "removeDiacritics"),array_keys($evts));
  $values     = array_values($evts);
  $evenements[$_message] = ($keys && $values) ? array_combine($keys, $values) : array();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchange"  , $exchange);
$smarty->assign("types"     , $types);
$smarty->assign("page"      , $page);
$smarty->assign("messages"  , $messages);
$smarty->assign("evenements", $evenements);
$smarty->assign("keywords"  , $keywords);
$smarty->display("inc_filters_exchanges.tpl");


?>