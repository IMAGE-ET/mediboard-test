<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$all_name_sources = CExchangeSource::getAll();
$all_sources = array();

$source_exchange = array("CSourceSFTP" => "CExchangeFTP",
                        "CSourceFTP" => "CExchangeFTP",
                        "CSourceSOAP" => "CEchangeSOAP");

$count_exchange = array();

foreach ($all_name_sources as $_name_source) {
  $class = new $_name_source;
  $all_sources[$_name_source] = $class->loadList();
  $count_exchange[$_name_source] = "";
  if (array_key_exists($_name_source, $source_exchange)) {
    $class_exchange = new $source_exchange[$_name_source];
    $count_exchange[$_name_source] = $class_exchange->countList();
  }

}
$smarty = new CSmartyDP();
$smarty->assign("all_sources", $all_sources);
$smarty->assign("count_exchange", $count_exchange);
$smarty->display("vw_sources.tpl");