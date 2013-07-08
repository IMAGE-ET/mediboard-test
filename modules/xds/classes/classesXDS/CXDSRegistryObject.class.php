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
 * Classe mère de RegistryPackage, ExtrinsicObject, externalIdentifier, Association, classification
 */
class CXDSRegistryObject {

  public $id;
  public $lid;
  public $objectType;
  public $versionInfo;

  /**
   * Création d'une instance de la classe
   *
   * @param String $id String
   */
  function __construct($id) {
    $this->id = $id;
  }

  /**
   * Setter generic
   *
   * @param String   $name  String
   * @param String[] $value String[]
   *
   * @return void
   */
  function setSlot($name, $value) {
    $this->$name = new CXDSSlot($name, $value);
  }
}