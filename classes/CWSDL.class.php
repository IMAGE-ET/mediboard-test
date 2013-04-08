<?php

/**
 * Web Services Description Language
 *  
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CWSDL
 * Web Services Description Language
 */
class CWSDL extends CMbXMLDocument {
  /**
   * @var array
   */
  public $xsd = array(
    "string"       => "string",
    "bool"         => "boolean",
    "boolean"      => "boolean",
    "int"          => "integer",
    "integer"      => "integer",
    "double"       => "double",
    "float"        => "float",
    "number"       => "float",
    "resource"     => "anyType",
    "mixed"        => "anyType",
    "unknown_type" => "anyType",
    "anyType"      => "anyType",
    "xml"          => "anyType",
  );

  /**
   * @var CSOAPHandler
   */
  public $_soap_handler;

  /**
   * Construct
   *
   * @return CWSDL
   */
  function __construct() {
    parent::__construct();
    $this->documentfilename = "tmp/document.wsdl";
    $this->addComment($this, "WSDL Mediboard genere permettant de decrire le service web.");
    $this->addComment($this, "Partie 1 : Definitions");
    $definitions = $this->addElement($this, "definitions", null, "http://schemas.xmlsoap.org/wsdl/");
    $this->addNameSpaces($definitions);
  }

  /**
   * Add namespaces
   *
   * @param DOMNode $elParent Parent element
   *
   * @return void
   */
  function addNameSpaces(DOMNode $elParent) {
    // Ajout des namespace
    $this->addAttribute($elParent, "name"           , "MediboardWSDL");
    $this->addAttribute($elParent, "targetNamespace", "http://soap.mediboard.org/wsdl/");
    $this->addAttribute($elParent, "xmlns:typens"   , "http://soap.mediboard.org/wsdl/");
    $this->addAttribute($elParent, "xmlns:xsd"      , "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($elParent, "xmlns:soap"     , "http://schemas.xmlsoap.org/wsdl/soap/");
    $this->addAttribute($elParent, "xmlns:soapenc"  , "http://schemas.xmlsoap.org/soap/encoding/");
    $this->addAttribute($elParent, "xmlns:wsdl"     , "http://schemas.xmlsoap.org/wsdl/");
  }

  /**
   * Add text
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $elName    Element name
   * @param string  $elValue   The value of the element.
   * @param int     $elMaxSize Maximum size
   *
   * @return mixed
   */
  function addTexte($elParent, $elName, $elValue, $elMaxSize = 100) {
    $elValue = substr($elValue, 0, $elMaxSize);
    return $this->addElement($elParent, $elName, $elValue);
  }

  /**
   * Add service
   *
   * @param string $username  Username
   * @param string $password  Password
   * @param string $module    Module name
   * @param string $tab       Tab name
   * @param string $classname Class name
   *
   * @return void
   */
  function addService($username, $password, $module, $tab, $classname) {
    $definitions = $this->documentElement;
    $this->addComment($definitions, "Partie 7 : Service");
    
    $service = $this->addElement($definitions, "service");
    $this->addAttribute($service, "name", "MediboardService");
    
    $this->addTexte($service, "documentation", "Documentation du WebService");
    
    $partie8 = $this->createComment("partie 8 : Port");
    $service->appendChild($partie8);
    $port = $this->addElement($service, "port");
    $this->addAttribute($port, "name", "MediboardPort");
    $this->addAttribute($port, "binding", "typens:MediboardBinding");
    
    $soapaddress = $this->addElement($port, "soap:address", null, "http://schemas.xmlsoap.org/wsdl/soap/");

    $url = "/?login=1&username=$username&password=$password&m=$module&a=$tab&class=$classname&suppressHeaders=1";
    $this->addAttribute($soapaddress, "location", CApp::getBaseUrl().$url);
  }

  /**
   * Dumps the internal XML tree back into a string
   *
   * @return void
   */
  function saveFileXML() {
    parent::saveXML();
  }
}
