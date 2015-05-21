<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$facture_guid = CValue::get("object_guid");

/* @var CFactureEtablissement $facture*/
$facture = CMbObject::loadFromGuid($facture_guid);

$facture->loadRefsObjects();
$facture->updateMontants();
$facture->loadRefsReglements();

// Ajout de reglements
$facture->_new_reglement_patient = new CReglement();
$facture->_new_reglement_patient->setObject($facture);
$facture->_new_reglement_patient->montant = $facture->_du_restant;
$use_mode_default = CAppUI::conf("dPfacturation CReglement use_mode_default");
$facture->_new_reglement_patient->mode = $use_mode_default != "none"  ? $use_mode_default : "autre";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);
$smarty->assign("reload" , 1);

$smarty->display("inc_vw_reglements_etab.tpl");