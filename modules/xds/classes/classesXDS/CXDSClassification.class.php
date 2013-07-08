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
 * -classification interne catégorise la classe RegistryPackage comme un lot de
 * soumission ou un classeur ;
 * -classification externe regroupe des objets similaires dans un domaine
 * particulier comme par exemple des listes de codes constituées de codes, libellés
 * et domaines de vocabulaire ; ces regroupements peuvent être inclus dans une
 * fiche, un lot de soumission ou un classeur ;
 */
class CXDSClassification extends CXDSRegistryObject {

  public $classificationScheme;
  public $classifiedObject;
  public $classificationNode;
  public $nodeRepresentation;

  /**
   * @see parent::__construct()
   */
  function __construct($id) {
    parent::__construct($id);
    $this->objectType = "urn:oasis:names:tc:ebxmlregrep:ObjectType:RegistryObject:Classification";
  }

  /**
   * Génération du xml pour l'instance en cours
   *
   * @param bool $submissionSet false
   *
   * @return CXDSXmlDocument
   */
  function toXML($submissionSet = false) {
    $xml = new CXDSXmlDocument();
    if ($submissionSet) {
      $xml->createSubmissionRoot($this->id, $this->classificationNode, $this->classifiedObject);
    }

    $xml->createClassificationRoot($this->id, $this->classificationScheme, $this->classifiedObject, $this->nodeRepresentation);
    return $xml;
  }
}
