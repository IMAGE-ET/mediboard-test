<?php

/**
 * aphmOdonto
 *  
 * @category aphmOdonto
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$mail = CValue::get("mail");
$exchange_source = CExchangeSource::get("mediuser-" . CAppUI::$user->_id);

$exchange_source->init();

try {
  $exchange_source->setRecipient($mail);
  $exchange_source->setSubject("Mediboard - Alerte d'espace disque");
  $exchange_source->setBody("L'espace disque du serveur est inférieur à 6 Go. Il faut y remédier au plus vite.");
  $exchange_source->send();
}
catch(phpmailerException $e) {
  throw new CMbException($e->errorMessage());
}
catch(CMbException $e) {
  throw new CMbException($e->stepAjax());
}
?>