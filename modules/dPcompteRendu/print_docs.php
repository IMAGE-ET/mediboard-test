<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

CAppUI::requireLibraryFile("PDFMerger/PDFMerger");

$nbDoc     = CValue::get("nbDoc");
$documents = array();
$pdf       = new PDFMerger;


foreach($nbDoc as $compte_rendu_id => $nb_print){
  if ($nb_print > 0) {
    $compte_rendu = new CCompteRendu();
    $compte_rendu->load($compte_rendu_id);
    $compte_rendu->date_print = mbDateTime();
    $compte_rendu->store();
    $compte_rendu->makePDFpreview(1);
    
    for ($i = 1; $i <= $nb_print; $i++) {
      $pdf->addPDF($compte_rendu->_ref_file->_file_path, 'all');
    }
  }
}

// Stream du PDF au client avec ouverture automatique
// Si aucun pdf, alors PDFMerger génère une exception que l'on catche
try {
  $pdf->merge('browser', 'documents.pdf');
}
catch(Exception $e) {}
?>