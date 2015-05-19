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
  function __construct($sender = null, $schemafilename = null) {
    parent::__construct();
    
    $this->schemapath       = "modules/$sender/xsd";
    $this->schemafilename   = $schemafilename ? "$this->schemapath/$schemafilename.xsd" : "$this->schemapath/$sender.xsd";
    $this->documentfilename = "tmp/document.xml";
    $this->finalpath        = CFile::$directory . "/rpuxml/$sender";
    
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
    $sejour = $mbObject->loadRefSejour();

    $this->addElement($elParent, "CP", $mbObject->_cp);
    $this->addElement($elParent, "COMMUNE", $mbObject->_ville);
    $this->addElement($elParent, "NAISSANCE", CMbDT::transform($mbObject->_naissance, null, "%d/%m/%Y"));
    $this->addElement($elParent, "SEXE", strtoupper($mbObject->_sexe));
    
    $this->addElement($elParent, "ENTREE", CMbDT::transform($sejour->entree, null, "%d/%m/%Y %H:%M"));
    $this->addElement($elParent, "MODE_ENTREE", $sejour->mode_entree);

    $this->addElement($elParent, "PROVENANCE",  ($sejour->provenance == "8") ? "5" : $sejour->provenance);
    if ($sejour->transport == "perso_taxi") {
      $sejour->transport = "perso";
    }
    if ($sejour->transport == "ambu_vsl") {
      $sejour->transport = "ambu";
    }
    $this->addElement($elParent, "TRANSPORT", strtoupper($sejour->transport));
    $this->addElement($elParent, "TRANSPORT_PEC", strtoupper($mbObject->pec_transport));

    $motif = CMbString::htmlSpecialChars($mbObject->motif);
    if (CAppUI::conf("dPurgences CRPU gestion_motif_sfmu", $sejour->loadRefEtablissement()) == 2 || $mbObject->motif_sfmu) {
      $motif = $mbObject->loadRefMotifSFMU()->code;
    }
    $this->addElement($elParent, "MOTIF", $motif);

    if (CModule::getActive("oscour") && CAppUI::conf("dPurgences gerer_circonstance") && CAppUI::conf("oscour version_complete")) {
      $circonstance = $mbObject->loadRefCirconstance();
      $this->addElement($elParent, "CIRCONSTANCE", $circonstance->code);
    }
    $this->addElement($elParent, "GRAVITE", strtoupper($mbObject->ccmu));

    $this->addElement($elParent, "DP", $mbObject->_DP[0].preg_replace("/[^\d]/", "", substr($mbObject->_DP, 1)));
        
    $liste_da = $this->addElement($elParent, "LISTE_DA");
    if ($dr = $sejour->_ext_diagnostic_relie) {
      $this->addDiagAssocie($liste_da, $dr->code[0].preg_replace("/[^\d]/", "", substr($dr->code, 1)));
    }
    $das = $sejour->_diagnostics_associes;
    if (is_array($das)) {
      foreach ($das as $_da) {
        $_da = $_da[0].preg_replace("/[^\d]/", "", substr($_da, 1));
        $this->addDiagAssocie($liste_da, $_da);
      }
    }
    
    $liste_actes = $this->addElement($elParent, "LISTE_ACTES");
    $codes_ccam = $sejour->_ref_consult_atu->_codes_ccam;
    if (is_array($codes_ccam)) {
      foreach ($codes_ccam as $_code_ccam) {
        $this->addActeCCAM($liste_actes, $_code_ccam);
      }
    }

    $sortie = null;
    if ($sejour->sortie_reelle) {
      $sortie = $sejour->sortie_reelle;
    }
    else {
      // on recherche la première affectation qui n'est pas dans un service d'urgences ou externe
      $affectation = new CAffectation();
      $ljoin["service"] = "`service`.`service_id` = `affectation`.`service_id`";
      $where = array();
      $where["sejour_id"]         = " = '$sejour->_id'";
      $where["service.cancelled"] = " = '0'";
      $where["service.uhcd"]      = " = '0'";
      $where["service.urgence"]   = " = '0'";

      $affectation->loadObject($where, "entree ASC", null, $ljoin);

      if ($affectation->_id) {
        $sortie = $affectation->entree;
      }
    }

    if ($sortie) {
      $this->addElement($elParent, "SORTIE", CMbDT::transform($sortie, null, "%d/%m/%Y %H:%M"));
    }

    if (CModule::getActive("cerveau")) {
      // on recherche la première affectation vers UHCD
      $affectation = new CAffectation();
      $ljoin["service"] = "`service`.`service_id` = `affectation`.`service_id`";
      $ljoin["sejour"]  = "`affectation`.`sejour_id` = `sejour`.`sejour_id`";
      $where = array();
      $where["affectation.sejour_id"] = " = '$sejour->_id'";
      $where["service.cancelled"]     = " = '0'";
      $where["service.uhcd"]          = " = '1'";
      $where["sejour.uhcd"]           = " = '1'";

      $affectation->loadObject($where, "entree ASC", null, $ljoin);

      if (!$affectation->_id) {
        $mode_sortie = $mbObject->_mode_sortie;
        $destination = $sejour->destination;
        $orientation = $mbObject->orientation;

        // Dans le cas où l'on ne créé pas un relicat, on va aller chercher les valeurs sur l'affectation de médecine
        if ($mbObject->mutation_sejour_id && CAppUI::conf("dPurgences create_sejour_hospit")) {
          // on recherche la première affectation qui ni UHCD, ni URG
          $affectation_medecine = new CAffectation();
          $ljoin["service"] = "`service`.`service_id` = `affectation`.`service_id`";
          $ljoin["sejour"]  = "`affectation`.`sejour_id` = `sejour`.`sejour_id`";
          $where = array();
          $where["affectation.sejour_id"] = " = '$sejour->_id'";
          $where["service.cancelled"]     = " = '0'";
          $where["service.uhcd"]          = " != '1'";
          $where["service.urgence"]       = " != '1'";

          $affectation_medecine->loadObject($where, "entree ASC", null, $ljoin);
          if ($affectation_medecine) {
            $service = $affectation_medecine->loadRefService();

            $mode_sortie = "6";
            $destination = $service->default_destination;
            $orientation = $service->default_orientation;
          }
        }
      }
      else {
        // Dans le cas où l'on a eu une mutation les données du RPU concerne la mut. UHCD
        $mode_sortie = "6";
        $destination = "1";
        $orientation = "UHCD";
      }

      $this->addElement($elParent, "MODE_SORTIE", $mode_sortie);
      $this->addElement($elParent, "DESTINATION", $destination);
      $this->addElement($elParent, "ORIENT"     , strtoupper($orientation));

      if ($affectation->_id) {
        $this->addElement($elParent, "ENTREE_UHCD"     , CMbDT::transform($affectation->entree, null, "%d/%m/%Y %H:%M"));
        $this->addElement($elParent, "MODE_SORTIE_UHCD", $mbObject->_mode_sortie);
        $this->addElement($elParent, "DESTINATION_UHCD", $sejour->destination);
        $this->addElement($elParent, "ORIENT_UHCD"     , strtoupper($mbObject->orientation));
      }
    }
    else {
      if (!$sortie) {
        $this->addElement($elParent, "SORTIE", CMbDT::transform($sejour->sortie_prevue, null, "%d/%m/%Y %H:%M"));
      }
      $this->addElement($elParent, "MODE_SORTIE", $mbObject->_mode_sortie);
      $this->addElement($elParent, "DESTINATION", $sejour->destination);
      $this->addElement($elParent, "ORIENT", strtoupper($mbObject->orientation));
    }
  }
  
  function addDiagAssocie($elParent, $elValue) {
    $this->addElement($elParent, "DA", $elValue);
  }
  
  function addActeCCAM($elParent, $elValue) {
    $this->addElement($elParent, "ACTE", str_replace(" ", "", $elValue));
  }
}
