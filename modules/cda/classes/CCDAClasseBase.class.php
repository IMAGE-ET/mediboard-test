<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * CCDAClasseBase Class
 */
class CCDAClasseBase {

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
  }

  /**
   * Récupère le nom de la classe
   *
   * @return String
   */
  function getNameClass() {
  }

  /**
   * Retourne la données
   *
   * @return String
   */
  function getData(){
  }

  /**
   * Retourne le résultat de la validation par le xsd de la classe appellée
   *
   * @return bool
   */
  function validate() {

    $domDataType = $this->toXML(null, "urn:hl7-org:v3");
    /*if (get_class($this) === "CCDAPOCD_MT000040_Act") {
      mbTrace($domDataType->saveXML());
      return $domDataType->schemaValidate("modules/cda/resources/TestClassesCDA.xsd");
    }*/
    return @$domDataType->schemaValidate("modules/cda/resources/TestClassesCDA.xsd");
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
   * @param null $nameParent String
   * @param null $namespace  String
   *
   * @return CCDADomDocument
   */
  function toXML($nameParent = null, $namespace = null) {

    $dom = new CCDADomDocument();
    //on affecte le nom de la classe comme noeud racine
    $name = $this->getNameClass();
    /**
     * Si le nom parent est spécifié, on utilisera ce nom pour le noeud racine
     */
    if (!empty($nameParent)) {
      $name = $nameParent;
    }

    //on créé le nom racine
    $dom->createNodeRoot($name, $namespace);

    //on récupère les specifications définie dans les props
    $spec = $this->getSpecs();
    $baseXML = $dom->getElement($name);

    //On parcours les specs
    foreach ($spec as $key => $value) {
      //on récupère une instance d'une classe stocké dans la variable
      /** @var CCDA_Datatype $classInstance */
      $classInstance = $this->$key;
      //on effectue différente action selon ce qui est définir dans la prop XML
      switch ($value["xml"]) {
        case "attribute":
          //On vérifie la présence d'une instance
          if (empty($classInstance)) {
            continue;
          }
          if ($key === "identifier") {
            $key = "ID";
          }
          //On créé l'attribut
          $dom->appendAttribute($baseXML, $key, $classInstance->getData());
          break;
        case "data":
          //on insert la donnée avant tous les éléments
          $dom->insertTextFirst($baseXML, $this->getData());
          break;
        case "element":
          //on vérifie l'existence d'une instance
          if (empty($classInstance)) {
            continue;
          }
          //on vérifie si l'instance est un tableau
          if (is_array($classInstance)) {
            //on parcours les différentes instance
            /** @var CCDA_Datatype[] $classInstance */
            foreach ($classInstance as $_class) {
              //on récupère le code xml de l'instance en spécifiant le nom du noeud racine
              $xmlClass = $_class->toXML($key, $namespace);
              //on ajoute à notre document notre instance
              $dom->importDOMDocument($baseXML, $xmlClass);
            }
          }
          else {
            //on récupère le code xml de l'instance en spécifiant le nom du noeud racine
            $xmlClass = $classInstance->toXML($key, $namespace);
            //on ajoute à notre document notre instance
            $dom->importDOMDocument($baseXML, $xmlClass);
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
        if (!empty($namespace)) {
          $xpath->registerNamespace("cda", $namespace);
          $nodeKey = $xpath->query("//cda:".$key);
        }
        else {
          $nodeKey = $xpath->query("//".$key);
        }

        $nodeKey = $nodeKey->item(0);

        if (is_array($classInstance)) {
          foreach ($classInstance as $_class) {
            /**
             * on spécifie le type de l'élément (on cast)
             */
            $dom->castElement($nodeKey, $_class->getNameClass());
          }
        }
        else {
          /**
           * on spécifie le type de l'élément (on cast)
           */
          $dom->castElement($nodeKey, $classInstance->getNameClass());
        }
      }
    }
    return $dom;
  }

  /**
   * Appelle la méthode validate et retourne un tableau avec le résultat
   *
   * @param String $description   String
   * @param String $resultAttendu String
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
