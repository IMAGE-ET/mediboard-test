<?php
/**
 * Execute method
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$method               = CValue::post("func");
$exchange_source_guid = CValue::post("exchange_source_guid");
$parameters           = CValue::post("parameters");

/** @var $exchange_source CExchangeSource */
$exchange_source = CMbObject::loadFromGuid($exchange_source_guid);
$exchange_source->setData($parameters);
$exchange_source->send($method, true);

echo $exchange_source->getACQ();

CApp::rip();