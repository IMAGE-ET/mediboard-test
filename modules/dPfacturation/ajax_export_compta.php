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

$out = fopen('php://output', 'w');
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="ExportCompta.xls"');

$facture_class = CValue::get("facture_class", 'CFactureEtablissement');
$factures_id = CValue::get("factures", array());
$factures_id = explode("|", $factures_id);

$where = array();
$where["facture_id"] = CSQLDataSource::prepareIn(array_values($factures_id));

$facture = new $facture_class;
$factures = $facture->loadList($where);

// Ligne d'entête
$fields = array();
$fields[] = "Date";
$fields[] = "Facture";
$fields[] = "Patient";
$fields[] = "Montant";

fputcsv($out, $fields, ';');

foreach ($factures as $_facture) {
  /* @var CFactureEtablissement $_facture*/
  $_facture->loadRefPatient();
  $_facture->loadRefsObjects();
  $_facture->loadRefsReglements();
  $fields = array();
  $fields["Date"]     = CMbDT::format($_facture->cloture, "%d/%m/%Y");
  $fields["Facture"]  = $_facture->_id;
  $fields["Patient"]  = $_facture->_ref_patient;
  $fields["Montant"]  = sprintf("%.2f", $_facture->_montant_avec_remise);
  fputcsv($out, $fields, ';');
}
