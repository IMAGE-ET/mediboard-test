<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Classe dont hériteront toutes les classes
 */
class CCDA_Datatype {

  function validate() {

    $domDataType = $this->toXML();
    /*if (get_class($this) === "CCDAINT") {
      mbTrace($domDataType->saveXML());
    }*/
    return @$domDataType->schemaValidate("modules/cda/resources/TestClasses.xsd");
  }

  function getProps() {
    $props = array();

    return $props;
  }

  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    if (strpos($name, "_") !== false) {
      $name = substr($name, 1);
    }
    return $name;
  }

  function getSpecs(){
    $specs = array();
    foreach ($this->getProps() as $_field => $_prop) {
      $parts = explode(" ", $_prop);
      $_type = array_shift($parts);

      $spec_options = array(
        "type" => $_type,
      );
      foreach ($parts as $_part) {
        $options = explode("|", $_part);
        $spec_options[array_shift($options)] = count($options) ? implode("|", $options) : true;
      }

      $specs[$_field] = $spec_options;
    }

    return $specs;
  }

  function toXML($nameParent = null) {
    $dom = new DOMDocument();
    $name = $this->getNameClass();
    if(!empty($nameParent)) {
      $name = $nameParent;
    }

    $dom->appendChild($dom->createElement($name));

    $spec = $this->getSpecs();

    foreach ($spec as $key => $value) {
      switch ($value["xml"]) {
        case "attribute":
          $classInstance = $this->$key;
          if (empty($classInstance)) {
            continue;
          }
          $baseXML = $dom->getElementsByTagName($name)->item(0);
          $baseXML->appendChild($dom->createAttribute($key));
          $baseXML->attributes->getNamedItem($key)->nodeValue = $classInstance->getData();

          break;
        case "data":
          $dom->getElementsByTagName($name)->item(0)->nodeValue = $this->getData();
          break;
        case "element":
          $classInstance = $this->$key;
          if (empty($classInstance)) {
            continue;
          }
          $baseXML = $dom->getElementsByTagName($name)->item(0);
          if (is_array($classInstance)) {
            foreach ($classInstance as $_class) {
              $xmlClass = $_class->toXML($key);
              $baseXML->appendChild($dom->importNode($xmlClass->documentElement));
            }
          }
          else {
            $xmlClass = $classInstance->toXML($key);
            $baseXML->appendChild($dom->importNode($xmlClass->documentElement));
          }
          break;
      }
    }

    return $dom;
  }

  function sample($description, $resultAttendu) {

    $arrayReturn = array("description" => $description,
                         "resultatAttendu" => $resultAttendu,
                         "resultat" => "");
    $result = $this->validate();

    if ($result) {
      $arrayReturn["resultat"] = "Document valide";
    }
    else {
      $arrayReturn["resultat"] = "Document invalide";
    }
    return $arrayReturn;
  }
}
