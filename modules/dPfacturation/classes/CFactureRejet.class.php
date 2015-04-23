<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Trace des rejets de facture par les assurances
 */
class CFactureRejet extends CMbObject {

  // DB Table key
  public $facture_rejet_id;

  // DB Fields
  public $praticien_id;
  public $file_name;
  public $num_facture;
  public $date;
  public $motif_rejet;
  public $statut;
  public $name_assurance;
  public $traitement;
  public $facture_id;
  public $facture_class;

  // Object References
  public $_ref_file_xml;
  public $_ref_facture;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_rejet';
    $spec->key   = 'facture_rejet_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["praticien_id"]  = "ref class|CMediusers";
    $props["file_name"]     = "str";
    $props["num_facture"]   = "str";
    $props["date"]          = "date";
    $props["motif_rejet"]   = "text";
    $props["name_assurance"]= "str";
    $props["statut"]        = "enum list|attente|traite default|attente";
    $props["traitement"]    = "dateTime";

    $props["facture_id"]    = "ref class|CFacture meta|facture_class";
    $props["facture_class"] = "enum list|CFactureCabinet|CFactureEtablissement show|0";
    return $props;
  }

  /**
   * Load file
   *
   * @return CFile
   */
  function loadRefFile() {
    $this->loadRefsFiles();
    return $this->_ref_file_xml = reset($this->_ref_files);
  }

  /**
   * Chargement de la facture
   *
   * @return CFacture
   */
  function loadRefFacture() {
    return $this->_ref_facture = $this->loadFwdRef("facture_id", true);
  }


  /**
   * Traitement des retours en erreur d'xml d'un praticien
   *
   * @return CFacture
   */
  static function traitementDossier($chir_id) {
    $files = array();
    $fs_source_reception = CExchangeSource::get("reception-tarmed-CMediusers-$chir_id", "file_system", true, null, false);
    if (!$fs_source_reception->_id || !$fs_source_reception->active) {
      return null;
    }
    $count_files = CMbPath::countFiles($fs_source_reception->host);
    if ($count_files < 100) {
      try {
        $files = $fs_source_reception->receive();
      } catch (CMbException $e) {
        return CAppUI::tr($e->getMessage());
      }
    }

    $delfile_read_reject = CAppUI::conf("dPfacturation Other delfile_read_reject", CGroups::loadCurrent());
    foreach ($files as $_file) {
      $fs = new CSourceFileSystem();

      $rejet = new self;
      $rejet->praticien_id = $chir_id;
      $rejet->file_name    = basename($_file);
      if ($msg = $rejet->store()) { return $msg;}
      $rejet->readXML($fs->getData($_file));

      //Sauvegarde du XML en CFile
      $new_file = new CFile();
      $new_file->setObject($rejet);
      $new_file->file_name      = basename($_file);
      $new_file->file_type  = "application/xml";
      $new_file->author_id = CAppUI::$user->_id;

      $new_file->fillFields();
      $new_file->updateFormFields();
      $new_file->forceDir();

      $new_file->putContent(trim($fs->getData($_file)));

      if ($msg = $new_file->store()) {
        mbTrace($msg);
      }

      //Suppression du fichier selon configuration
      if ($delfile_read_reject) {
        $fs->delFile($_file);
      }
    }
  }


  function readXML($content_file = null) {
    if (!$content_file) {
      $file = $this->loadRefFile();
      $file->updateFormFields();
      $content_file = file_get_contents($file->_file_path);
    }

    $doc = new CMbXMLDocument("UTF-8");
    $doc->loadXML($content_file);

    $xpath = new CMbXPath($doc);
    $xpath->registerNamespace("invoice", "http://www.forum-datenaustausch.ch/invoice");

    $payload = $xpath->queryUniqueNode("//invoice:payload");
    $timestamp = $xpath->getValueAttributNode($payload, "response_timestamp");
    $this->date = strftime ('%Y-%m-%d', $timestamp);

    $invoice = $xpath->queryUniqueNode("//invoice:invoice");
    $this->num_facture = $xpath->getValueAttributNode($invoice, "request_id");

    $insurance = $xpath->queryUniqueNode("//invoice:insurance");
    $ean_party = $xpath->getValueAttributNode($insurance, "ean_party");
    $corr = new CCorrespondantPatient();
    $corr->ean = $ean_party;
    $corr->loadMatchingObject();
    $this->name_assurance = $corr->nom;

    $pending = $xpath->query("//invoice:pending");
    foreach ($pending as $_pending) {
      $explanation = $xpath->queryTextNode("invoice:explanation", $_pending);
      $this->motif_rejet = "$explanation \r\n";

      $messages = $xpath->query("//invoice:message");
      foreach ($messages as $_message)  {
        $code = $xpath->getValueAttributNode($_message, "code");
        $text = $xpath->getValueAttributNode($_message, "text");
        $this->motif_rejet .= "$code: $text \r\n";

      }
    }

    $rejected = $xpath->query("//invoice:rejected");
    foreach ($rejected as $_rejected) {
      $explanation = $xpath->queryTextNode("invoice:explanation", $_rejected);
      $this->motif_rejet = "$explanation \r\n";

      $messages = $xpath->query("//invoice:error");
      foreach ($messages as $_message)  {
        $code = $xpath->getValueAttributNode($_message, "code");
        $text = $xpath->getValueAttributNode($_message, "text");
        $this->motif_rejet .= "$code: $text \r\n";
        if ($error_value = $xpath->getValueAttributNode($_message, "error_value")) {
          $valid_value = $xpath->getValueAttributNode($_message, "valid_value");
          $this->motif_rejet .= "($error_value/$valid_value)";
        }
      }
    }

    foreach ($rejected as $_rejet) {
      mbTrace($_rejet, "rejet");
    }

    if ($msg = $this->store()) {
      mbTrace($msg);
    }
  }
}