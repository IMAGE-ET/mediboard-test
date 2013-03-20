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
 * Mailing and home or office addresses. A sequence of
 * address parts, such as street or post office Box, city,
 * postal code, country, etc.
 *
 * @property $delimiter
 * @property $country
 * @property $state
 * @property $county
 * @property $city
 * @property $postalCode
 * @property $streetAddressLine
 * @property $houseNumber
 * @property $houseNumberNumeric
 * @property $direction
 * @property $streetName
 * @property $streetNameBase
 * @property $streetNameType
 * @property $additionalLocator
 * @property $unitID
 * @property $unitType
 * @property $careOf
 * @property $censusTract
 * @property $deliveryAddressLine
 * @property $deliveryInstallationType
 * @property $deliveryInstallationArea
 * @property $deliveryInstallationQualifier
 * @property $deliveryMode
 * @property $deliveryModeIdentifier
 * @property $buildingNumberSuffix
 * @property $postBox
 * @property $precinct
 * @property $useablePeriod
 */
class CCDAAD extends CCDAANY {

  var $delimiter = array();
  var $country = array();
  var $state = array();
  var $county = array();
  var $city = array();
  var $postalCode = array();
  var $streetAddressLine = array();
  var $houseNumber = array();
  var $houseNumberNumeric = array();
  var $direction = array();
  var $streetName = array();
  var $streetNameBase = array();
  var $streetNameType = array();
  var $additionalLocator = array();
  var $unitID = array();
  var $unitType = array();
  var $careOf = array();
  var $censusTract = array();
  var $deliveryAddressLine = array();
  var $deliveryInstallationType = array();
  var $deliveryInstallationArea = array();
  var $deliveryInstallationQualifier = array();
  var $deliveryMode = array();
  var $deliveryModeIdentifier = array();
  var $buildingNumberSuffix = array();
  var $postBox = array();
  var $precinct = array();

  /**
   * A General Timing Specification (GTS) specifying the
   * periods of time during which the address can be used.
   * This is used to specify different addresses for
   * different times of the year or to refer to historical
   * addresses.
   *
   * @var array
   */
  var $useablePeriod = array();

  /**
   * A set of codes advising a system or user which address
   * in a set of like addresses to select for a given purpose.
   *
   * @var CCDAset_PostalAddressUse
   */
  public $use;

  /**
   * A boolean value specifying whether the order of the
   * address parts is known or not. While the address parts
   * are always a Sequence, the order in which they are
   * presented may or may not be known. Where this matters, the
   * isNotOrdered property can be used to convey this
   * information.
   *
   * @var CCDA_base_bl
   */
  public $isNotOrdered;

  /**
   * Setter isNotOrdered
   *
   * @param \CCDA_base_bl $isNotOrdered \CCDA_base_bl
   *
   * @return void
   */
  public function setIsNotOrdered($isNotOrdered) {
    $this->isNotOrdered = $isNotOrdered;
  }

  /**
   * Getter isNotOrdered
   *
   * @return \CCDA_base_bl
   */
  public function getIsNotOrdered() {
    return $this->isNotOrdered;
  }

  /**
   * Setter use
   *
   * @param \CCDAset_PostalAddressUse $use \CCDAset_PostalAddressUse
   *
   * @return void
   */
  public function setUse($use) {
    $this->use = $use;
  }

  /**
   * Getter use
   *
   * @return \CCDAset_PostalAddressUse
   */
  public function getUse() {
    return $this->use;
  }

  /**
   * Ajoute l'instance dans le champ spécifié
   *
   * @param String $name  String
   * @param mixed  $value mixed
   *
   * @return void
   */
  function append($name, $value) {
    array_push($this->$name, $value);
  }

  /**
   * retourne le tableau du champ spécifié
   *
   * @param String $name String
   *
   * @return mixed
   */
  function get($name) {
    return $this->$name;
  }

  /**
   * Efface le tableau du champ spécifié
   *
   * @param String $name String
   *
   * @return void
   */
  function razListdata($name) {
    $this->$name = array();
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["delimiter"] = "CCDAadxp_delimiter xml|element";
    $props["country"] = "CCDAadxp_country xml|element";
    $props["state"] = "CCDAadxp_state xml|element";
    $props["county"] = "CCDAadxp_county xml|element";
    $props["city"] = "CCDAadxp_city xml|element";
    $props["postalCode"] = "CCDAadxp_postalCode xml|element";
    $props["streetAddressLine"] = "CCDAadxp_streetAddressLine xml|element";
    $props["houseNumber"] = "CCDAadxp_houseNumber xml|element";
    $props["houseNumberNumeric"] = "CCDAadxp_houseNumberNumeric xml|element";
    $props["direction"] = "CCDAadxp_direction xml|element";
    $props["streetName"] = "CCDAadxp_streetName xml|element";
    $props["streetNameBase"] = "CCDAadxp_streetNameBase xml|element";
    $props["streetNameType"] = "CCDAadxp_streetNameType xml|element";
    $props["additionalLocator"] = "CCDAadxp_additionalLocator xml|element";
    $props["unitID"] = "CCDAadxp_unitID xml|element";
    $props["unitType"] = "CCDAadxp_unitType xml|element";
    $props["careOf"] = "CCDAadxp_careOf xml|element";
    $props["censusTract"] = "CCDAadxp_censusTract xml|element";
    $props["deliveryAddressLine"] = "CCDAadxp_deliveryAddressLine xml|element";
    $props["deliveryInstallationType"] = "CCDAadxp_deliveryInstallationType xml|element";
    $props["deliveryInstallationArea"] = "CCDAadxp_deliveryInstallationArea xml|element";
    $props["deliveryInstallationQualifier"] = "CCDAadxp_deliveryInstallationQualifier xml|element";
    $props["deliveryMode"] = "CCDAadxp_deliveryMode xml|element";
    $props["deliveryModeIdentifier"] = "CCDAadxp_deliveryModeIdentifier xml|element";
    $props["buildingNumberSuffix"] = "CCDAadxp_buildingNumberSuffix xml|element";
    $props["postBox"] = "CCDAadxp_postBox xml|element";
    $props["precinct"] = "CCDAadxp_precinct xml|element xml|element";
    $props["useablePeriod"] = "CCDASXCM_TS xml|element";
    $props["use"] = "CCDAset_PostalAddressUse xml|attribute";
    $props["isNotOrdered"] = "CCDA_base_bl xml|attribute";
    $props["data"] = "str xml|data";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return array()
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec des données
     */

    $this->setData("test");
    $tabTest[] = $this->sample("Test avec des données", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec use incorrecte
     */

    $postal = new CCDAset_PostalAddressUse();
    $post = new CCDAPostalAddressUse();
    $post->setData("TESTTEST");
    $postal->addData($post);
    $this->setUse($postal);

    $tabTest[] = $this->sample("Test avec un use incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec use correcte
     */

    $post->setData("TMP");
    $postal->razlistData();
    $postal->addData($post);
    $this->setUse($postal);

    $tabTest[] = $this->sample("Test avec un use correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec isNotOrdered incorrecte
     */

    $order = new CCDA_base_bl();
    $order->setData("TESTTEST");
    $this->setIsNotOrdered($order);

    $tabTest[] = $this->sample("Test avec un isNotOrdered incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec isNotOrdered correcte
     */

    $order->setData("true");
    $this->setIsNotOrdered($order);

    $tabTest[] = $this->sample("Test avec un isNotOrdered correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec delimiter correcte
     */

    $adxp = new CCDA_adxp_delimiter();
    $this->append("delimiter", $adxp);
    $tabTest[] = $this->sample("Test avec un delimiter correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec delimiter correcte
     */

    $adxp = new CCDA_adxp_delimiter();
    $this->append("delimiter", $adxp);
    $tabTest[] = $this->sample("Test avec deux delimiter correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec country correcte
     */

    $adxp = new CCDA_adxp_country();
    $this->append("country", $adxp);
    $tabTest[] = $this->sample("Test avec un country correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec state correcte
     */

    $adxp = new CCDA_adxp_state();
    $this->append("state", $adxp);
    $tabTest[] = $this->sample("Test avec un state correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec county correcte
     */

    $adxp = new CCDA_adxp_county();
    $this->append("county", $adxp);
    $tabTest[] = $this->sample("Test avec un county correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec city correcte
     */

    $adxp = new CCDA_adxp_city();
    $this->append("city", $adxp);
    $tabTest[] = $this->sample("Test avec un city correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec postalCode correcte
     */

    $adxp = new CCDA_adxp_postalCode();
    $this->append("postalCode", $adxp);
    $tabTest[] = $this->sample("Test avec un postalCode correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec streetAddressLine correcte
     */

    $adxp = new CCDA_adxp_streetAddressLine();
    $this->append("streetAddressLine", $adxp);
    $tabTest[] = $this->sample("Test avec un streetAddressLine correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec houseNumber correcte
     */

    $adxp = new CCDA_adxp_houseNumber();
    $this->append("houseNumber", $adxp);
    $tabTest[] = $this->sample("Test avec un houseNumber correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec houseNumberNumeric correcte
     */

    $adxp = new CCDA_adxp_houseNumberNumeric();
    $this->append("houseNumberNumeric", $adxp);
    $tabTest[] = $this->sample("Test avec un houseNumberNumeric correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec direction correcte
     */

    $adxp = new CCDA_adxp_direction();
    $this->append("direction", $adxp);
    $tabTest[] = $this->sample("Test avec un direction correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec streetName correcte
     */

    $adxp = new CCDA_adxp_streetName();
    $this->append("streetName", $adxp);
    $tabTest[] = $this->sample("Test avec un streetName correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec streetNameBase correcte
     */

    $adxp = new CCDA_adxp_streetNameBase();
    $this->append("streetNameBase", $adxp);
    $tabTest[] = $this->sample("Test avec un streetNameBase correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec streetNameType correcte
     */

    $adxp = new CCDA_adxp_streetNameType();
    $this->append("streetNameType", $adxp);
    $tabTest[] = $this->sample("Test avec un streetNameType correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec additionalLocator correcte
     */

    $adxp = new CCDA_adxp_additionalLocator();
    $this->append("additionalLocator", $adxp);
    $tabTest[] = $this->sample("Test avec un additionalLocator correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec unitID correcte
     */

    $adxp = new CCDA_adxp_unitID();
    $this->append("unitID", $adxp);
    $tabTest[] = $this->sample("Test avec un unitID correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec unitType correcte
     */

    $adxp = new CCDA_adxp_unitType();
    $this->append("unitType", $adxp);
    $tabTest[] = $this->sample("Test avec un unitType correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec careOf correcte
     */

    $adxp = new CCDA_adxp_careOf();
    $this->append("careOf", $adxp);
    $tabTest[] = $this->sample("Test avec un careOf correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec censusTract correcte
     */

    $adxp = new CCDA_adxp_censusTract();
    $this->append("censusTract", $adxp);
    $tabTest[] = $this->sample("Test avec un censusTract correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deliveryAddressLine correcte
     */

    $adxp = new CCDA_adxp_deliveryAddressLine();
    $this->append("deliveryAddressLine", $adxp);
    $tabTest[] = $this->sample("Test avec un deliveryAddressLine correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deliveryInstallationType correcte
     */

    $adxp = new CCDA_adxp_deliveryInstallationType();
    $this->append("deliveryInstallationType", $adxp);
    $tabTest[] = $this->sample("Test avec un deliveryInstallationType correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deliveryInstallationArea correcte
     */

    $adxp = new CCDA_adxp_deliveryInstallationArea();
    $this->append("deliveryInstallationArea", $adxp);
    $tabTest[] = $this->sample("Test avec un deliveryInstallationArea correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deliveryInstallationQualifier correcte
     */

    $adxp = new CCDA_adxp_deliveryInstallationQualifier();
    $this->append("deliveryInstallationQualifier", $adxp);
    $tabTest[] = $this->sample("Test avec un deliveryInstallationQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deliveryMode correcte
     */

    $adxp = new CCDA_adxp_deliveryMode();
    $this->append("deliveryMode", $adxp);
    $tabTest[] = $this->sample("Test avec un deliveryMode correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deliveryModeIdentifier correcte
     */

    $adxp = new CCDA_adxp_deliveryModeIdentifier();
    $this->append("deliveryModeIdentifier", $adxp);
    $tabTest[] = $this->sample("Test avec un deliveryModeIdentifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec buildingNumberSuffix correcte
     */

    $adxp = new CCDA_adxp_buildingNumberSuffix();
    $this->append("buildingNumberSuffix", $adxp);
    $tabTest[] = $this->sample("Test avec un buildingNumberSuffix correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec postBox correcte
     */

    $adxp = new CCDA_adxp_postBox();
    $this->append("postBox", $adxp);
    $tabTest[] = $this->sample("Test avec un postBox correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec precinct correcte
     */

    $adxp = new CCDA_adxp_precinct();
    $this->append("precinct", $adxp);
    $tabTest[] = $this->sample("Test avec un precinct correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec useablePeriod incorrecte
     */

    $useable = new CCDASXCM_TS();
    $cs = new CCDA_base_ts();
    $cs->setData("TESTEST");
    $useable->setValue($cs);
    $this->append("useablePeriod", $useable);
    $tabTest[] = $this->sample("Test avec un useablePeriod incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec useablePeriod correcte
     */


    $cs->setData("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $useable->setValue($cs);
    $this->razListdata("useablePeriod");
    $this->append("useablePeriod", $useable);
    $tabTest[] = $this->sample("Test avec un useablePeriod correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
