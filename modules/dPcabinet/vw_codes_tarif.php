<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$tarif_id = CValue::getOrSession("tarif_id");

$tarif = new CTarif;
$tarif->load($tarif_id);
$tarif->loadActes();

$tab = array();
if (CAppui::conf("dPccam CCodeCCAM use_cotation_ccam")) {
  $tab["ccam"] = "_ref_code_ccam";
  $tab["ngap"] = "_libelle";
}
if (CModule::getActive("tarmed") && CAppui::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
  $tab["tarmed"] = "_ref_tarmed";
  $tab["caisse"] = "_ref_prestation_caisse";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tarif", $tarif);
$smarty->assign("tab",   $tab);

$smarty->display("vw_codes_tarif.tpl");