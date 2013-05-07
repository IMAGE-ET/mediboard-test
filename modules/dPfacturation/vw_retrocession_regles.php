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

$mediuser = new CMediusers();
$listPrat = $mediuser->loadPraticiens();

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($prat_id);
$praticien->loadRefsRetrocessions();

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("listPrat",   $listPrat);
$smarty->assign("praticien",  $praticien);

$smarty->display("vw_retrocession_regles.tpl");
