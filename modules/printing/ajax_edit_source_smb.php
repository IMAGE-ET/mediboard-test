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
CValue::setSession("class", "CSourceSMB");

$source_smb = new CSourceSMB;

if ($source_id) {
  $source_smb->load($source_id);
}
else {
  $source_smb->valueDefaults();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("source_smb", $source_smb);
$smarty->display("inc_edit_source_smb.tpl");
