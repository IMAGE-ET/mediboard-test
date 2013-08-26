<?php 
/**
 * View Printing Sources
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$source_id  = CValue::getOrSession("source_id" , 0 );
$class = CValue::getOrSession("class", 'CSourceLPR');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("source_id" , $source_id);
$smarty->assign("class", $class);
$smarty->display("vw_idx_sources.tpl");
