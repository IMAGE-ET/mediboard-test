<?php

/**
 * Impression d'une sélection de documents
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$nbDoc = CView::get("nbDoc", "str");
CView::checkin();

$documents = array();
$pdf = new CMbPDFMerger();

CMbArray::removeValue("0", $nbDoc);

if (!count($nbDoc)) {
  CAppUI::stepAjax("Aucun document à imprimer !");
  CApp::rip();
}

$compte_rendu = new CCompteRendu();
$where = array("compte_rendu_id" => CSQLDataSource::prepareIn(array_keys($nbDoc)));

/** @var $_compte_rendu CCompteRendu */
foreach ($compte_rendu->loadList($where) as $_compte_rendu) {
  $_compte_rendu->date_print = CMbDT::dateTime();
  $_compte_rendu->store();
  $_compte_rendu->makePDFpreview(1);

  $nb_print = $nbDoc[$_compte_rendu->_id];
  for ($i = 1; $i <= $nb_print; $i++) {
    $pdf->addPDF($_compte_rendu->_ref_file->_file_path);
  }
}

// Stream du PDF au client avec ouverture automatique
// Si aucun pdf, alors PDFMerger génère une exception que l'on catche
try {
  $pdf->merge("browser", "documents.pdf");
}
catch(Exception $e) {
  CApp::rip();
}
