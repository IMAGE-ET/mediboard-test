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
 * Defines the basic properties of every data value. This
 * is an abstract type, meaning that no value can be just
 * a data value without belonging to any concrete type.
 * Every concrete type is a specialization of this
 * general abstract DataValue type.
 */
class CCDAANY {

  /**
   * An exceptional value expressing missing information
   * and possibly the reason why the information is missing.
   * @var CCDANullFlavor
   */
  public $nullFlavor;

  function setNullFlavor($nullFlavor) {
    $this->nullFlavor = $nullFlavor;
  }

  function getProps() {
    $props = array();
    $props["nullFlavor"] = "CCDANullFlavor attribute";

    return $props;
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

  function getName() {
    $name = get_class($this);
    $name = substr($name, 4);

    if (strpos($name, "_")) {
      $name = substr($name, 1);
    }
    return $name;
  }

  function validate() {

    $domDataType = $this->toXML();
    return $domDataType->schemaValidate("modules/cda/resources/AllDataType.xsd");
  }

  function toXML() {
    $dom = new DOMDocument();
    $name = $this->getName();
    $dom->appendChild($dom->createElement($name));

    $spec = $this->getSpecs();


    foreach ($spec as $key => $value) {
      if ($value["attribute"]) {
          if (empty($this->$key)) {
            continue;
          }
          $dom->getElementsByTagName($name)->item(0)->appendChild($dom->createAttribute($key));
          $dom->getElementsByTagName($name)->item(0)->attributes->getNamedItem($key)->nodeValue = $this->$key;
      }
    }
    return $dom;
  }

  function sample($description, $resultAttendu) {

    $result = @$this->validate();
    if ($result) {
      $result = "Document valide";
    }
    else {
      $result = "Document invalide";
    }

    $smarty = new CSmartyDP();

    $smarty->assign("result", $result);
    $smarty->assign("description", $description);
    $smarty->assign("nameClass", $this->getName());
    $smarty->assign("resultAttendu", $resultAttendu);

    $smarty->display("vw_testdatatype.tpl");
  }

  function test() {
    /**
     * Test avec un nullFlavor null
     */

    $this->sample("Test avec un nullFlavor null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor erroné
     */
    $this->setNullFlavor("TESTEST");

    $this->sample("Test avec un nullFlavor erroné", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor bon
     */

    $this->setNullFlavor("NP");

    $this->sample("Test avec un nullFlavor bon", "Document valide");

    $this->changeclass();
  }

  function changeclass() {
    echo "--------------------------------------------------------<br/>";
    echo "Changement de classe<br/>";
    echo "--------------------------------------------------------<br/>";
  }
}
