<?php 
/**
 * View Edit Source
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$source_id  = CValue::getOrSession("source_id", 0);
CValue::setSession("class", "CSourceLPR");

$source_lpr = new CSourceLPR;

if ($source_id) {
  $source_lpr->load($source_id);
}
else {
  $source_lpr->valueDefaults();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("source_lpr", $source_lpr);
$smarty->display("inc_edit_source_lpr.tpl");
