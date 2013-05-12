<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CRPUXMLDocument extends CMbXMLDocument {
  public $schemapath;
  public $schemafilename;
  public $documentfilename;
  public $finalpath;
  public $documentfinalprefix;
  public $documentfinalfilename;
  public $now;
  
  public $datedebut;
  public $datefin;

  /**
   * @see parent::__construct()
   */
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
  
  function addRPU($elParent, CRPU $mbObject) { 
    $this->addElement($elParent, "CP", $mbObject->_cp);
    $this->addElement($elParent, "COMMUNE", $mbObject->_ville);
    $this->addElement($elParent, "NAISSANCE", CMbDT::transform($mbObject->_naissance, null, "%d/%m/%Y"));
    $this->addElement($elParent, "SEXE", strtoupper($mbObject->_sexe));
    
    $this->addElement($elParent, "ENTREE", CMbDT::transform($mbObject->_entree, null, "%d/%m/%Y %H:%M"));
    $this->addElement($elParent, "MODE_ENTREE", $mbObject->_ref_sejour->mode_entree);
    $this->addElement($elParent, "PROVENANCE", $mbObject->_ref_sejour->provenance);
    if ($mbObject->_ref_sejour->transport == "perso_taxi") {
      $mbObject->_ref_sejour->transport = "perso";
    }
    if ($mbObject->_ref_sejour->transport == "ambu_vsl") {
      $mbObject->_ref_sejour->transport = "ambu";
    }
    $this->addElement($elParent, "TRANSPORT", strtoupper($mbObject->_ref_sejour->transport));
    $this->addElement($elParent, "TRANSPORT_PEC", strtoupper($mbObject->pec_transport));
    
    $motif = CMbString::htmlSpecialChars($mbObject->motif);
    if (CAppUI::conf("dPurgences gerer_circonstance")) {
      $module_orumip = CModule::getActive("orumip");
      $orumip_active = $module_orumip && $module_orumip->mod_active;
      
      $motif = $orumip_active ? $mbObject->circonstance : CMbString::htmlSpecialChars($mbObject->_libelle_circonstance);
    }
    
    $this->addElement($elParent, "MOTIF", $motif);
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
    
    $this->addElement($elParent, "SORTIE", CMbDT::transform($mbObject->_sortie, null, "%d/%m/%Y %H:%M"));
    $this->addElement($elParent, "MODE_SORTIE", $mbObject->_mode_sortie);
    $this->addElement($elParent, "DESTINATION", $mbObject->_ref_sejour->destination);
    $this->addElement($elParent, "ORIENT", strtoupper($mbObject->orientation));
  }
  
  function addDiagAssocie($elParent, $elValue) {
    $this->addElement($elParent, "DA", $elValue);
  }
  
  function addActeCCAM($elParent, $elValue) {
    $this->addElement($elParent, "ACTE", str_replace(" ", "", $elValue));
  }
}
