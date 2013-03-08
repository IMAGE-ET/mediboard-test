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
  var $validateSchematron = null;

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
   * @param DOMNode $node Node
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
   * @param DOMNode $node Node
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
   * @param String $message message
   *
   * @return void
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
   * @param String $message message
   *
   * @return void
   */
  function showxml($message) {
    $this->xml = CMbString::highlightCode("xml", $message);
  }

  function schematronValidate ($message) {
    $xsltsche = new XSLTProcessor();

    $domSche = new DOMDocument();
    $domSche->loadXML($message);

    $domXSLSche = new DOMDocument();
    $domXSLSche->load("modules/cda/resources/schematron/CI-SIS_StructurationCommuneCDAr2.xsl");

    $xsltsche->importStylesheet($domXSLSche);
    $XSLValid = $xsltsche->transformToXml($domSche);
/*
    $xslt = new XSLTProcessor();
    $xslt->importStylesheet($XSLValid);
    $domcda = new CMbXMLDocument("UTF-8");
    $domcda->loadXMLSafe($message);
    $this->validateSchematron = $xslt->transformToXml($domcda);*/
  }

  function parseElement($node) {
    $tabElement = array();
    foreach ($node as $_element) {
      $tabElement[] = $this->parseattribute($_element);
    }
    return $tabElement;
  }

  /**
   * Fonction de création des classes voc
   *
   * @return string
   */
  function createClass() {
    $dom = new CMbXMLDocument("UTF-8");
    $dom->load("modules/cda/resources/voc.xsd");

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");

    $nodeList = $xpath->query("//xs:complexType|xs:simpleType");
    $node = $nodeList->item(1);
    $listvoc = array();
    $node->attributes->getNamedItem("name")->nodeValue;

    foreach ($nodeList as $_node) {
      $name = $_node->attributes->getNamedItem("name")->nodeValue;
      $documentation = $xpath->queryUniqueNode("//xs:*[@name='".$name."']//xs:documentation");
      if ($documentation) {
        $documentation = $documentation->nodeValue;
      }
      $union = $xpath->queryUniqueNode("//xs:*[@name='".$name."']//xs:union");
      if ($union) {
        $union = $union->attributes->getNamedItem("memberTypes")->nodeValue;
      }

      $restriction = $xpath->queryUniqueNode("//xs:*[@name='".$name."']//xs:restriction");
      if ($restriction) {
        $restriction = $restriction->attributes->getNamedItem("base")->nodeValue;
      }

      $enumeration = $xpath->query("//xs:*[@name='".$name."']//xs:enumeration");
      $listEnumeration = array();

      foreach ($enumeration as $_enumeration) {
        array_push($listEnumeration, $_enumeration->attributes->getNamedItem("value")->nodeValue);
      }
      $listvoc[] = array( "name" => $name,
                          "documentation" => $documentation,
                          "union" => $union,
                          "restriction" => $restriction,
                          "enumeration" => $listEnumeration);
    }

    $cheminBase = "modules/cda/classes/datatypes/voc/";

    foreach ($listvoc as $voc) {
      $nameFichier = "CCDA".$voc["name"].".class.php";
      $smarty = new CSmartyDP();
      $smarty->assign("documentation", $voc["documentation"]);
      $smarty->assign("name", $voc["name"]);
      $smarty->assign("enumeration", $this->formatArray($voc["enumeration"]));
      $union = $this->formatArray(array());
      if (CMbArray::get($voc, "union")) {
        $union = $this->formatArray(explode(" ", $voc["union"]));
      }
      $smarty->assign("union", $union);
      $smarty->assign("extend", $voc["restriction"]);
      $data = $smarty->fetch("defaultClassVoc.tpl");

      file_put_contents($cheminBase.$nameFichier, $data);
    }
  }

  function formatArray($array) {
    return preg_replace('/\)$/', "  )", preg_replace("/\d+ => /", "  ", var_export($array, true)));
  }

  function showNodeXSD($name) {
    $dom = new CMbXMLDocument();
    $dom->load("modules/cda/resources/datatypes-base.xsd");

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");
    $node = $xpath->queryUniqueNode("//xs:*[@name='".$name."']");
    return $dom->saveXML($node);
  }

  function createTestSchemaClasses() {
    $nameFile = "modules/cda/resources/TestClasses.xsd";
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->load($nameFile);

    $xpath = new DOMXPath($dom);
    $nodeList = $xpath->query("//xs:element");

    foreach ($nodeList as $_node) {
      $dom->documentElement->removeChild($_node);
    }

    file_put_contents($nameFile, $dom->saveXML());

    $file = glob("modules/cda/classes/datatypes/{voc,base}/*.class.php", GLOB_BRACE);

    foreach ($file as $_file) {
      $element = $dom->createElement("xs:element");
      $_file = CMbArray::get(explode(".", $_file), 0);
      $_file = substr($_file, strrpos($_file, "/")+1);
      $instanceClass = new $_file;
      $_file = $instanceClass->getNameClass();
      $element->setAttribute("name", $_file);
      $element->setAttribute("type", $_file);
      $dom->documentElement->appendChild($element);
      $dom->documentElement->appendChild($dom->createTextNode("\n"));
    }
    file_put_contents($nameFile, $dom->saveXML());
  }

  function syntheseTest($result) {
    $resultSynth = array("total" => 0,
                         "succes" => 0,
                         "erreur" => array());
    foreach ($result as $keyClass => $valueClass) {
      foreach ($valueClass as $_test) {
        if ($_test["resultat"] === $_test["resultatAttendu"]) {
          $resultSynth["succes"]++;
        }
        else {
          array_push($resultSynth["erreur"], $keyClass);
        }
        $resultSynth["total"]++;
      }
    }
    $resultSynth["erreur"] = array_unique($resultSynth["erreur"]);
    return $resultSynth;
  }

  function createTest() {
    $file = glob("modules/cda/classes/datatypes/{voc,base}/*.class.php", GLOB_BRACE);

    $result = array();
    foreach ($file as $_file) {
      $_file = CMbArray::get(explode(".", $_file), 0);
      $_file = substr($_file, strrpos($_file, "/")+1);
      $class = new $_file;
      $result[$class->getNameClass()] = $class->test();
    }
    return $result;

  }

  function returnType($schema) {
    $dom = new CMbXMLDocument();
    $dom->load($schema);

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");
    $nodelist = $xpath->query("//xs:simpleType[@name]|xs:complexType[@name]");
    $listName =  array();
    foreach ($nodelist as $_node) {
      array_push($listName, $_node->attributes->getNamedItem("name")->nodeValue);
    }

    return $listName;
  }

  function missclass() {
    $listAllType = array();
    $listAllType = $this->returnType("modules/cda/resources/datatypes-base.xsd");
    $listAllType = array_merge($listAllType, $this->returnType("modules/cda/resources/voc.xsd"));
    $file = glob("modules/cda/classes/datatypes/{voc,base}/*.class.php", GLOB_BRACE);

    $result = array();
    foreach ($file as $_file) {
      $_file = CMbArray::get(explode(".", $_file), 0);
      $_file = substr($_file, strrpos($_file, "/")+1);
      $class = new $_file;
      array_push($result, $class->getNameClass());
    }
    return array_diff($listAllType, $result);
  }

  function clearXSD() {
    $pathSource = "modules/cda/resources/datatypes-base_original.xsd";
    $pathDest = "modules/cda/resources/datatypes-base.xsd";
    $copyFile = false;
    $copyFile = copy($pathSource, $pathDest);

    if (!$copyFile) {
      return false;
    }

    $dom = new DOMDocument();
    $dom->load($pathDest);

    $xpath = new DOMXPath($dom);
    $nodeList = $xpath->query("//xs:complexType[@abstract]|xs:simpleType[@abstract]");

    foreach ($nodeList as $_node) {
      $_node->removeAttribute("abstract");
    }

    $nodeList = $xpath->query("//xs:element[@maxOccurs=\"0\"]");
    foreach ($nodeList as $_node) {
      $_node->parentNode->removeChild($_node);
    }
    file_put_contents($pathDest, $dom->saveXML());
  }
}