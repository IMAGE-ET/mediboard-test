<?php 
/**
 * View Print Sources
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$source_id = CValue::getOrSession("source_id", 0);

$sources = array();

// Récupération des sources
$source_lpr = new CSourceLPR;
$sources = $source_lpr->loadlist();

$source_smb = new CSourceSMB;
$sources = array_merge($sources, $source_smb->loadlist());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sources"  , $sources);
$smarty->assign("source_id", $source_id);
$smarty->display("inc_list_sources.tpl");

?>