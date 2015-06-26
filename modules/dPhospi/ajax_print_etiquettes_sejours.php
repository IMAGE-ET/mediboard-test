<?php 

/**
 * $Id$
 *  
 * @category Hospitalisation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object_class        = CValue::post("object_class");
$sejours_ids         = CValue::post("sejours_ids");
$modele_etiquette_id = CValue::post("modele_etiquette_id");

$sejours_ids = explode("-", $sejours_ids);

$sejour = new CSejour();

$where = array();
$where["sejour_id"] = CSQLDataSource::prepareIn($sejours_ids);

$sejours = $sejour->loadList($where, "FIELD(sejour_id, " . implode(",", $sejours_ids) . ")");

CStoredObject::massLoadFwdRef($sejours, "patient_id");
CStoredObject::massLoadFwdRef($sejours, "praticien_id");

$modele_etiquette = new CModeleEtiquette();
$modele_etiquette->load($modele_etiquette_id);

$etiquettes = array();

$uniqid = uniqid();

CSejour::massLoadNDA($sejours);
CSejour::massLoadNRA($sejours);

foreach ($sejours as $_sejour) {
  $fields = array();
  $_sejour->completeLabelFields($fields, array());

  $_modele = unserialize(serialize($modele_etiquette));
  $_modele->completeLabelFields($fields, array());
  $_modele->replaceFields($fields);
  $etiquettes[$_sejour->_id] = tempnam("", "etiq_$uniqid");
  file_put_contents($etiquettes[$_sejour->_id], $_modele->printEtiquettes(null, 0));
}

$pdf = new CMbPDFMerger();

foreach ($etiquettes as $_etiquette) {
  $pdf->addPDF($_etiquette);
}

try {
  $pdf->merge('browser', 'etiquettes.pdf');

  foreach ($etiquettes as $_etiquette) {
    unlink($_etiquette);
  }
}
catch(Exception $e) {
  CApp::rip();
}

