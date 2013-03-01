<?php

/**
 * $Id$
 *  
 * @category ${Module}
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
    return $domDataType->schemaValidate("modules/cda/resources/AllDataType.xsd");
  }

  function getName() {
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

  function toXML() {
    $dom = new DOMDocument();
    $name = $this->getName();
    $dom->appendChild($dom->createElement($name));

    $spec = $this->getSpecs();


    foreach ($spec as $key => $value) {
      if (CMbArray::get($value, "attribute")) {
        if (empty($this->$key)) {
          continue;
        }
        $dom->getElementsByTagName($name)->item(0)->appendChild($dom->createAttribute($key));
        $dom->getElementsByTagName($name)->item(0)->attributes->getNamedItem($key)->nodeValue = $this->$key;
      }
      else {
        if (CMbArray::get($value, "data")) {
          $dom->getElementsByTagName($name)->item(0)->nodeValue = $this->data;
        }
      }
    }
    //mbTrace($dom->saveXML());
    return $dom;
  }

  function sample($description, $resultAttendu) {

    $arrayReturn = array("description" => $description,
                         "resultatAttendu" => $resultAttendu,
                         "resultat" => "");
    $result = @$this->validate();
    if ($result) {
      $arrayReturn["resultat"] = "Document valide";
    }
    else {
      $arrayReturn["resultat"] = "Document invalide";
    }
    return $arrayReturn;
  }
}
