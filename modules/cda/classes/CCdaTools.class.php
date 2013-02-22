<?php 
/**
 * $Id$
 *
 * Classe permettant de manipuler le document CDA
 * 
 * @package    Mediboard
 * @subpackage CDA
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
/**
 * Class CCdaTools
 */
class CCdaTools {

  var $contain   = null;
  var $validate  = null;

  var $xml       = null;
  /**
   * @var CMbXMLDocument
   */
  var $domschema = null;
  /**
   * @var CMbXPath
   */
  var $xpath     = null;

  static $listDataType = array(
    "bl", "BL", "CD", "EN", "int", "INT", "PQ",
    "AD", "real", "REAL", "st", "ST", "ED", "II",
    "MO","ts","TS", "RTO", "TEL", "bin", "BIN",
    "ANY", "QTY", "oid", "url", "URL", "cs", "CS",
    "CE", "CV", "CR", "ADXP", "ENXP", "ON", "PN", "AD");

  /**
   * Permet de récupérer les attributs d'un noeud xml sous forme de tableau
   *
   * @param $node
   *
   * @return array[nom_attribut]
   */
  function parseattribute($node) {
    $tabAttribute = array();
    foreach ($node->attributes as $_attribute) {
      $tabAttribute[$_attribute->nodeName] = utf8_decode($_attribute->nodeValue);
    }
    return $tabAttribute;
  }

  /**
   * Permet de faire un parcours en profondeur du document
   * et renvoi le document sous forme d'un tableau
   *
   * @param $node
   *
   * @return array
   */
  function parsedeep ($node) {
    /**
     * On renseigne les informations de notre noeud dans un tableau
     */
    $tabNode = array("name" => $node->localName,
                     "child" => array(),
                     "data" => utf8_decode($node->nodeValue),
                     "attribute" => $this->parseattribute($node));
    /**
     * On vérifie que l'élément est un DOMElement pour éviter les noeuds Text et autres
     */
    if ($node instanceof DOMElement) {
      /**
       * On parcours les fils de notre noeud courant
       */
      foreach ($node->childNodes as $_childNode) {
        /**
         * On vérifie que notre noeud possèdent un nom pour éviter les noeud contenant
         * des commentaires
         */
        if ($_childNode->localName) {
          /**
           * On affecte à notre tableau ces fils en appelant notre fonction
           */
          $tabNode["child"][] = $this->parsedeep($_childNode);
        }
      }
    }
    /**
     * Retourne le tableau de notre noeud courant dans le child du noeud parent
     * ou retourne le tableau complet à la fin du processus
     */
    return $tabNode;
  }

  /**
   * Permet remplir la variable $_contain avec la structure du document
   *
   * @param $message
   *
   * @return nothing
   */
  function parse ($message) {
    $this->domschema = new CMbXMLDocument("UTF-8");
    $this->domschema->load("modules/cda/resources/CDA.xsd");

    $this->xpath = new CMbXPath($this->domschema);
    $this->xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");

    $dom = new CMbXMLDocument("UTF-8");
    $dom->loadXMLSafe($message);

    $this->validate = $dom->schemaValidate("modules/cda/resources/CDA.xsd", true, false);
    $this->contain = $this->parsedeep($dom->documentElement);
  }

  /**
   * Affiche le message au format xml
   *
   * @param $message
   *
   * @return nothing
   */
  function showxml($message) {
    $this->xml = CMbString::highlightCode("xml", $message);
  }
}
