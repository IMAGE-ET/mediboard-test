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

$facture_id  = CValue::get("facture_id");

$facture = new CFactureCabinet();
$facture->load($facture_id);
$facture->loadRefsObjects();
$facture->loadRefsReglements();
$facture->loadRefsNotes();

$reglement = new CReglement();

// Chargement des banques
$order = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"       , $facture);
$smarty->assign("banques"       , $banques);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("date"          , CMbDT::date());

$smarty->display("inc_vw_reglements.tpl");
