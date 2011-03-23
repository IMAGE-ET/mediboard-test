<?php 
/**
 * Formats available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid = CValue::getOrSession("actor_guid");

$formats_xml     = CExchangeDataFormat::getAll("CEchangeXML");
$formats_tabular = CExchangeDataFormat::getAll("CExchangeTabular");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("actor_guid"     , $actor_guid);
$smarty->assign("formats_xml"    , $formats_xml);
$smarty->assign("formats_tabular", $formats_tabular);
$smarty->display("inc_formats_available.tpl");

?>