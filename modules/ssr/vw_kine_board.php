<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCando::checkEdit();

$date    = CValue::get("date", CMbDT::date());
$kine_id = CValue::getOrSession("kine_id", CAppUI::$instance->user_id);

// Chargement de la liste des utilisateurs
$user = new CMediusers();
$kines = CModule::getActive("dPprescription") ?
  CFunctionCategoryPrescription::getAllExecutants() :
  $user->loadKines();

$kine = new CMediusers();
$kine->load($kine_id);
$kine->loadRefIntervenantCdARR();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("kine", $kine);
$smarty->assign("kines", $kines);
$smarty->assign("kine_id", $kine_id);
$smarty->display("vw_kine_board.tpl");
