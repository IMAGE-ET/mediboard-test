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

  public $data;

  /**
   * Setter Data
   *
   * @param $data
   */
  function setData($data) {
    $this->data = $data;
  }

  /**
   * Getter Data
   *
   * @return mixed
   */
  function getData() {
    return $this->data;
  }

  /**
   * Retourne le résultat de la validation par le xsd de la classe appellée
   *
   * @return bool
   */
  function validate() {

    $domDataType = $this->toXML();
    /*if (get_class($this) === "CCDARTO_QTY_QTY") {
      mbTrace($domDataType->saveXML());
    }*/
    return @$domDataType->schemaValidate("modules/cda/resources/TestClasses.xsd");
  }

  /**
   * Initialise les props
   *
   * @return array
   */
  function getProps() {
    $props = array();

    return $props;
  }

  /**
   * Retourne le nom du type utilisé dans le XSD
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    if (strpos($name, "_") !== false) {
      $name = substr($name, 1);
    }
    return $name;
  }

  /**
   * retourne les props sous la forme d'un tableau
   *
   * @return array
   */
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

  /**
   * Transforme la classe en document XML
   *
   * @param null $nameParent
   *
   * @return DOMDocument
   */
  function toXML($nameParent = null) {

    $dom = new DOMDocument();
    //on affecte le nom de la classe comme noeud racine
    $name = $this->getNameClass();
    /**
     * Si le nom parent est spécifié, on utilisera ce nom pour le noeud racine
     */
    if(!empty($nameParent)) {
      $name = $nameParent;
    }
    //on créé le nom racine
    $dom->appendChild($dom->createElement($name));

    //on récupère les specifications définie dans les props
    $spec = $this->getSpecs();
    $baseXML = $dom->getElementsByTagName($name)->item(0);
    //On parcours les specs
    foreach ($spec as $key => $value) {
      //on récupère une instance d'une classe stocké dans la variable
      $classInstance = $this->$key;
      //on effectue différente action selon ce qui est définir dans la prop XML
      switch ($value["xml"]) {
        case "attribute":
          //On vérifie la présence d'une instance
          if (empty($classInstance)) {
            continue;
          }
          //on créé l'atribut avec le nom de la variable
          $baseXML->appendChild($dom->createAttribute($key));
          //on affecte la donnée stocké dans l'instance
          $baseXML->attributes->getNamedItem($key)->nodeValue = $classInstance->getData();
          break;
        case "data":
          //on récupère le premier fils
          $first = $baseXML->firstChild;
          //on insert la donnée avant tous les éléments
          $baseXML->insertBefore($dom->createTextNode($this->getData()), $first);
          break;
        case "element":
          //on vérifie l'existence d'une instance
          if (empty($classInstance)) {
            continue;
          }
          //on vérifie si l'instance est un tableau
          if (is_array($classInstance)) {
            //on parcours les différentes instance
            foreach ($classInstance as $_class) {
              //on récupère le code xml de l'instance en spécifiant le nom du noeud racine
              $xmlClass = $_class->toXML($key);
              //on ajoute à notre document notre instance
              $baseXML->appendChild($dom->importNode($xmlClass->documentElement));
            }
          }
          else {
            //on récupère le code xml de l'instance en spécifiant le nom du noeud racine
            $xmlClass = $classInstance->toXML($key);
            //on ajoute à notre document notre instance
            $baseXML->appendChild($dom->importNode($xmlClass->documentElement));
          }
          break;
      }
      //si la propriété abstract est spécifié
      if (CMbArray::get($value, "abstract")) {
        //on vérifie l'existence d'une instance
        if (empty($classInstance)) {
          continue;
        }
        //on cherche le noeud XML dans notre document
        $xpath = new DOMXPath($dom);
        $nodeKey = $xpath->query("//".$key);
        $nodeKey = $nodeKey->item(0);
        /**
         * on spécifie le type de l'élément (on cast)
         */
        $attribute = $dom->createAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:type");
        $attribute->nodeValue = $classInstance->getNameClass();
        $nodeKey->appendChild($attribute);
      }
    }

    return $dom;
  }

  /**
   * Appelle la méthode validate et retourne un tableau aevc le résultat
   *
   * @param $description
   * @param $resultAttendu
   *
   * @return array
   */
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