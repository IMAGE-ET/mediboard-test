<?php 
/**
 * Refresh exchange
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

// Chargement de l'échange demandé
$exchange = CMbObject::loadFromGuid($exchange_guid);

if (!$exchange) {
  // Création du template
  $smarty = new CSmartyDP();
  $smarty->assign("object", null);
  $smarty->display("inc_exchange.tpl");
  
  return;
}

$exchange->loadRefs(); 
$exchange->loadRefsInteropActor();
$exchange->getObservations();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $exchange);
$smarty->display("inc_exchange.tpl");

