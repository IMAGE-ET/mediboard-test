<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


class CRPUXMLDocument extends CMbXMLDocument {
  var $schemapath             = null;
  var $schemafilename         = null;
  var $documentfilename       = null;
  var $finalpath              = null;
  var $documentfinalprefix    = null;
  var $documentfinalfilename  = null;
  var $now                    = null;
  
  var $datedebut              = null;
  var $datefin                = null;
  
  function __construct($sender = null) {
    parent::__construct();
    
    $this->schemapath = "modules/$sender/xsd";
    $this->schemafilename = "$this->schemapath/$sender.xsd";
    $this->documentfilename = "tmp/document.xml";
    $this->finalpath = CFile::$directory . "/rpuxml/$sender";
    
    $this->now = time();
  }
  
  function schemaValidate($filename = null, $returnErrors = false) {
    if (!CAppUI::conf("dPurgences rpu_xml_validation")) {
      return true;
    }
    return parent::schemaValidate($filename, $returnErrors);
  }
  
  function checkSchema() {
    if (!is_dir($this->schemapath)) {
      trigger_error("RPU XML schemas are missing. Please extract them from archive in '$this->schemapath/' directory", E_USER_WARNING);
      return false;
    }
    
    if (!is_file($this->schemafilename)) {
      $schema = new CRPUXMLSchema();
      $schema->importSchemaPackage($this->schemapath);
      $schema->purgeIncludes();
      $schema->purgeImportedNamespaces();
      $schema->save($this->schemafilename);
    }
    
    return true;
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = null) {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }
 
  function saveTempFile() {
    parent::save(utf8_encode($this->documentfilename));
  }
  
  function saveFinalFile() {
    $this->documentfinalfilename = "$this->finalpath/$this->documentfinalprefix-$this->now.xml";
    CMbPath::forceDir(dirname($this->documentfinalfilename));
    parent::save($this->documentfinalfilename);
  }
  
  function addRPU($elParent, $mbObject) { 
    $this->addElement($elParent, "CP", $mbObject->_cp);
    $this->addElement($elParent, "COMMUNE", $mbObject->_ville);
    $this->addElement($elParent, "NAISSANCE", mbTransformTime($mbObject->_naissance, null, "%d/%m/%Y"));
    $this->addElement($elParent, "SEXE", strtoupper($mbObject->_sexe));
    
    $this->addElement($elParent, "ENTREE", mbTransformTime($mbObject->_entree, null, "%d/%m/%Y %H:%M"));
    $this->addElement($elParent, "MODE_ENTREE", $mbObject->mode_entree);
    $this->addElement($elParent, "PROVENANCE", $mbObject->provenance);
    if ($mbObject->transport == "perso_taxi") {
      $mbObject->transport = "PERSO";
    }
    if ($mbObject->transport == "ambu_vsl") {
      $mbObject->transport = "AMBU";
    }
    $this->addElement($elParent, "TRANSPORT", strtoupper($mbObject->transport));
    $this->addElement($elParent, "TRANSPORT_PEC", strtoupper($mbObject->pec_transport));
    $this->addElement($elParent, "MOTIF", $mbObject->motif);
    $this->addElement($elParent, "GRAVITE", strtoupper($mbObject->ccmu));

    $this->addElement($elParent, "DP", $mbObject->_DP[0].preg_replace("/[^\d]/", "", substr($mbObject->_DP, 1)));
        
    $liste_da = $this->addElement($elParent, "LISTE_DA");
    if ($dr = $mbObject->_ref_sejour->_ext_diagnostic_relie) {
      $this->addDiagAssocie($liste_da, $dr->code[0].preg_replace("/[^\d]/", "", substr($dr->code, 1)));
    }
    $das = $mbObject->_ref_sejour->_diagnostics_associes;
    if (is_array($das)) {
      foreach ($das as $_da) {
        $_da = $_da[0].preg_replace("/[^\d]/", "", substr($_da, 1));
        $this->addDiagAssocie($liste_da, $_da);
      }
    }
    
    $liste_actes = $this->addElement($elParent, "LISTE_ACTES");
    $codes_ccam = $mbObject->_ref_sejour->_ref_consult_atu->_codes_ccam;
    if (is_array($codes_ccam)) {
      foreach ($codes_ccam as $_code_ccam) {
        $this->addActeCCAM($liste_actes, $_code_ccam);
      }
    }
    
    $this->addElement($elParent, "SORTIE", mbTransformTime($mbObject->_sortie, null, "%d/%m/%Y %H:%M"));
    $this->addElement($elParent, "MODE_SORTIE", $mbObject->_mode_sortie);
    $this->addElement($elParent, "DESTINATION", $mbObject->destination);
    $this->addElement($elParent, "ORIENT", strtoupper($mbObject->orientation));
  }
  
  function addDiagAssocie($elParent, $elValue) {
    $this->addElement($elParent, "DA", $elValue);
  }
  
  function addActeCCAM($elParent, $elValue) {
    $this->addElement($elParent, "ACTE", $elValue);
  }
}

?>