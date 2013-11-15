<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe mère des classes RegistryPackage et ExtrinsicObject
 */
class CXDSExtrinsicPackage extends CXDSRegistryObject {

  /** @var  CXDSLocalizedString */
  public $title;
  /** @var  CXDSLocalizedString */
  public $comments;
  /** @var  CXDSDocumentEntryAuthor[] */
  public $documentEntryAuthor = array();
  /** @var  CXDSPatientID */
  public $patientId;
  /** @var  CXDSUniqueID */
  public $uniqueId;


  /**
   * Setter comments
   *
   * @param String $comments String
   *
   * @return void
   */
  public function setComments($comments) {
    $this->comments = new CXDSDescription($comments);
  }

  /**
   * Setter title
   *
   * @param String $title String
   *
   * @return void
   */
  public function setTitle($title) {
    $this->title = new CXDSName($title);
  }

  /**
   * Setter DocumentEntryAuthor
   *
   * @param CXDSDocumentEntryAuthor $entry CXDSDocumentEntryAuthor
   *
   * @return void
   */
  function appendDocumentEntryAuthor($entry) {
    array_push($this->documentEntryAuthor, $entry);
  }

  /**
   * Retourne les variables présent dans la classe
   *
   * @return array
   */
  function getPropertie() {
    $vars        = array_keys(get_class_vars(get_class($this)));
    $parent_vars = array_keys(get_class_vars(get_parent_class(get_parent_class($this))));

    $my_child_vars = array_diff($vars, $parent_vars);

    return $my_child_vars;
  }

  /**
   * Génère le XML
   *
   * @param boolean $registry XML pour le lot de soumission
   *
   * @return CXDSXmlDocument|void
   */
  function toXML($registry) {
    $xml       = new CXDSXmlDocument();
    $variables = $this->getPropertie();

    if ($registry) {
      $xml->createRegistryPackageRoot($this->id);
    }
    else {
      $xml->createExtrinsicObjectRoot($this->id, $this->mimeType, $this->objectType, $this->lid);
    }

    $base_xml  = $xml->documentElement;

    foreach ($variables as $_variable) {
      $class = $this->$_variable;
      if (!$class || $_variable === "mimeType" || $_variable === "lid") {
        continue;
      }
      if (is_array($this->$_variable)) {
        foreach ($this->$_variable as $_instance) {
          $xml->importDOMDocument($base_xml, $_instance->toXML());
        }
      }
      else {
        $xml->importDOMDocument($base_xml, $class->toXML());
      }
    }
    return $xml;
  }
}