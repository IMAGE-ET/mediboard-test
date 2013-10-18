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

foreach ($all_name_sources as $_name_source) {
  $class = new $_name_source;
  $all_sources[$_name_source] = $class->loadList();
}

$smarty = new CSmartyDP();
$smarty->assign("all_sources", $all_sources);
$smarty->display("vw_sources.tpl");