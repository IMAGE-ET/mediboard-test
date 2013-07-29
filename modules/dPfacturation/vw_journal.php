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
CCanDo::checkRead();
$type     = CValue::get("type");
$prat_id  = CValue::get("prat_id");
$date_min = CValue::get("_date_min");
$date_max = CValue::get("_date_max");

$journal_pdf = new CEditJournal();
$journal_pdf->type_pdf = $type;
$journal_pdf->date_min = $date_min;
$journal_pdf->date_max = $date_max;
$where = array();

if ($type == "paiement") {
  $where["date"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:00'";
  $where["object_class"] = " = 'CFactureEtablissement'";
  $reglement = new CReglement();
  $journal_pdf->reglements = $reglement->loadList($where, "debiteur_id, debiteur_desc, date");
  foreach ($journal_pdf->reglements as $_reglement) {
    /** @var CReglement $_reglement */
    $fact = $_reglement->loadRefFacture();
    $fact->loadRefsReglements();
    if (!$fact->_id) {
      unset($journal_pdf->reglements[$_reglement->_id]);
    }
  }
}

if ($type == "rappel") {
  $where["date"] = "BETWEEN '$date_min' AND '$date_max'";
  $where["object_class"] = " = 'CFactureEtablissement'";
  $relance = new CRelance();
  $journal_pdf->relances = $relance->loadList($where, "statut, poursuite");
  foreach ($journal_pdf->relances as $_relance) {
    /** @var CRelance $_relance */
    $fact = $_relance->loadRefFacture();
    $fact->loadRefsObjects();
    $fact->loadRefPatient();
    $fact->loadRefPraticien();
    $fact->loadRefsReglements();
    $fact->isRelancable();
    if (!$fact->_id) {
      unset($journal_pdf->relances[$_relance->_id]);
    }
  }
}

if ($type == "debiteur") {
  $where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";
  $facture = new CFactureEtablissement();
  $journal_pdf->factures = $facture->loadList($where);
  foreach ($journal_pdf->factures as $fact) {
    /** @var CFacture $fact */
    $fact->loadRefsObjects();
    $fact->loadRefPatient();
    $fact->loadRefPraticien();
    $fact->loadRefsReglements();
    $fact->isRelancable();
  }
}

$journal_pdf->editJournal();
