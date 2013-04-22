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
$facture_id   = CValue::getOrSession("facture_id");
$object_class = CValue::getOrSession("object_class");

$facture = new $object_class;
$facture->load($facture_id);
$facture->loadRefsObjects();
$facture->loadRefsReglements();
$facture->loadRefsNotes();

$reglement = new CReglement();

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"       , $facture);
$smarty->assign("banques"       , $banques);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("date"          , CMbDT::date());

$smarty->display("inc_vw_reglements.tpl");
?>