<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * POCD_MT000040_Product Class
 */
class CCDAPOCD_MT000040_Product extends CCDARIMParticipation {

  /**
   * @var CCDAPOCD_MT000040_ManufacturedProduct
   */
  public $manufacturedProduct;

  /**
   * Setter manufacturedProduct
   *
   * @param CCDAPOCD_MT000040_ManufacturedProduct $inst CCDAPOCD_MT000040_ManufacturedProduct
   *
   * @return void
   */
  function setManufacturedProduct(CCDAPOCD_MT000040_ManufacturedProduct $inst) {
    $this->manufacturedProduct = $inst;
  }

  /**
   * Getter manufacturedProduct
   *
   * @return CCDAPOCD_MT000040_ManufacturedProduct
   */
  function getManufacturedProduct() {
    return $this->manufacturedProduct;
  }

  /**
   * Assigne typeCode à PRD
   *
   * @return void
   */
  function setTypeCode() {
    $partType = new CCDAParticipationType();
    $partType->setData("PRD");
    $this->typeCode = $partType;
  }

  /**
   * Getter typeCode
   *
   * @return CCDAParticipationType
   */
  function getTypeCode() {
    return $this->typeCode;
  }


  /**
   * Retourne les propriétés
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["typeId"] = "CCDAPOCD_MT000040_InfrastructureRoot_typeId xml|element max|1";
    $props["manufacturedProduct"] = "CCDAPOCD_MT000040_ManufacturedProduct xml|element required";
    $props["typeCode"] = "CCDAParticipationType xml|attribute fixed|PRD";
    return $props;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec un manufacturedProduct correct
     */

    $manu = new CCDAPOCD_MT000040_ManufacturedProduct();
    $mat = new CCDAPOCD_MT000040_Material();
    $mat->setClassCode();
    $manu->setManufacturedMaterial($mat);
    $this->setManufacturedProduct($manu);
    $tabTest[] = $this->sample("Test avec un manufacturedProduct correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeCode correct
     */

    $this->setTypeCode();
    $tabTest[] = $this->sample("Test avec un typeCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}