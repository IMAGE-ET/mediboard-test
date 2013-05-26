<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$type                 = CValue::get('type');
$exchange_source_name = CValue::get('exchange_source_name');

$exchange_source      = CExchangeSource::get($exchange_source_name, $type, true, null, false);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("source", $exchange_source);

$smarty->display("inc_config_exchange_source.tpl");
