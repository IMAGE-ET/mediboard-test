<?php
/**
 * Messages supported
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$actor_guid     = CValue::getOrSession("actor_guid");
$exchange_class = CValue::getOrSession("exchange_class");

/** @var CExchangeDataFormat $data_format */
$data_format = new $exchange_class;
$messages    = $data_format->getMessagesSupported($actor_guid);

$all_messages = array();
foreach ($data_format->getMessagesSupported($actor_guid) as $_family => $_messages_supported) {
  $family = new $_family;
  $events = $family->getEvenements();

  $categories = array();
  if (isset($family->_categories) && !empty($family->_categories)) {
    foreach ($family->_categories as $_category => $events_name) {
      foreach ($events_name as $_event_name) {
        foreach ($_messages_supported as $_message_supported) {
          if (!array_key_exists($_event_name, $events)) {
            continue;
          }

          if ($_message_supported->message != $events[$_event_name]) {
            continue;
          }

          $categories[$_category][] = $_message_supported;
        }
      }
    }
  }
  else {
    $categories["none"] = $_messages_supported;
  }

  // On reformate un peu le tableau des catégories
  $family->_categories = $categories;

  $domain = $family->domain ? $family->domain : $family->name;

  $all_messages[$domain][] = $family;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("messages"    , $messages);
$smarty->assign("all_messages", $all_messages);
$smarty->assign("actor_guid"  , $actor_guid);
$smarty->display("inc_messages_supported.tpl");