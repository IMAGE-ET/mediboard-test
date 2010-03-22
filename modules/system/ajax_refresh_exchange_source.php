<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsRead();

$type                 = CValue::get('type');
$exchange_source_name = CValue::get('exchange_source_name');

$exchange_source      = CExchangeSource::get($exchange_source_name, $type, true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("source", $exchange_source);

$smarty->display("inc_config_exchange_source.tpl");

?>