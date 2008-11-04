<?php

/**
* @package Mediboard
* @subpackage ecap
* @version $Revision: 2165 $
* @author Thomas Despoix
*/

class CEcDocsExporter {
  static $exportedFiles = array();
  static $exportedDocs  = array();
  
  /**
   * Export du séjour complet
   * @param void
   *    */
  static function exportSejour(CSejour &$sejour) {
	  // Suppression des actes
	  $sejour->loadNumDossier();
	  if ($sejour->_num_dossier == "-") {
      trigger_error("Pas de numéro de dossier pour le séjour [$sejour->_id] '$sejour->_view''", E_USER_WARNING);
	    return;
	  }
	  
	  // Opérations
    mbTrace("Preparing [$sejour->_num_dossier]");
	  $sejour->loadRefsOperations();
	  foreach ($sejour->_ref_operations as &$operation) {
	    $operation->_idat = "unset";
	    self::exportObject($operation);
	  }
	  
	  
  }
  
  /**
   * Export d'un objet quelconque
   * @param void
   */
  static function exportObject(CMbObject &$mbObject) {
    if (!$mbObject->_id) {
      return;
    }
    
    mbTrace($mbObject->_view, "Exporting");
    
	  $mbObject->loadRefsDocs();
	  foreach ($mbObject->_ref_documents as $document) {
	    self::$exportedDocs[$doc->_id] = "tested";
	  }

	  $mbObject->loadRefsFiles();
	  foreach ($mbObject->_ref_documents as $file) {
	    self::$exportedFiles[$file->_id] = "tested";
	  }
  }
}

?>