<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
CCanDo::checkEdit();
$prat_id = CValue::getOrSession("prat_id", "0");
$retrocession_id = CValue::get("retrocession_id");

$retro = new CRetrocession();
if ($retrocession_id) {
  $retro->load($retrocession_id);
  $retro->loadRefPraticien();
}
else {
  $retro->praticien_id = $prat_id;
  $retro->type = "montant";
}

$mediuser = new CMediusers();
$listPrat = $mediuser->loadPraticiens();

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("listPrat",   $listPrat);
$smarty->assign("retrocession",  $retro);

$smarty->display("vw_edit_retrocession.tpl");
