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
class CXDSRegistryObject extends CMbMetaObject {

  public $id;
  public $objectType;
  public $versionInfo;

  public $_group_id;

  /**
   * @see parent::getProps
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["_group_id"] = "ref notNull class|CGroups";
    return $specs;
  }

  /**
   * Création d'une instance de la classe
   *
   * @param String $id String
   */
  function __construct($id) {
    parent::__construct();
    $this->id = $id;
    $this->_group_id = CGroups::loadCurrent()->_id;
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