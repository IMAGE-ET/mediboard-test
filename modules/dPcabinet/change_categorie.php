<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$prat = $consult->loadRefPlageConsult()->loadRefChir();

$categorie = new CConsultationCategorie();
$categorie->function_id = $prat->function_id;
$order = "nom_categorie ASC";
$categories = $categorie->loadMatchingList($order);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("consult"   , $consult);
$smarty->assign("categories", $categories);
$smarty->display("change_categorie.tpl");
