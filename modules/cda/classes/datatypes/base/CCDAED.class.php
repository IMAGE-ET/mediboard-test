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
 * Data that is primarily intended for human interpretation
 * or for further machine processing is outside the scope of
 * HL7. This includes unformatted or formatted written language,
 * multimedia data, or structured information as defined by a
 * different standard (e.g., XML-signatures.)  Instead of the
 * data itself, an ED may contain
 * only a reference (see TEL.) Note
 * that the ST data type is a
 * specialization of the ED data type
 * when the ED media type is text/plain.
 */
class CCDAED extends CCDABIN {

  /**
   * A telecommunication address (TEL), such as a URL
   * for HTTP or FTP, which will resolve to precisely
   * the same binary data that could as well have been
   * provided as inline data.
   * @var CCDATEL
   */
  public $reference;

  /**
   * @var CCDAthumbnail
   */
  public $thumbnail;

  /**
   * Identifies the type of the encapsulated data and
   * identifies a method to interpret or render the data.
   * @var CCDACS
   */
  public $mediaType;

  /**
   * For character based information the language property
   * specifies the human language of the text.
   * @var CCDACS
   */
  public $language;

  /**
   * Indicates whether the raw byte data is compressed,
   * and what compression algorithm was used.
   * @var CCDACompressionAlgorithm
   */
  public $compression;

  /**
   * The integrity check is a short binary value representing
   * a cryptographically strong checksum that is calculated
   * over the binary data. The purpose of this property, when
   * communicated with a reference is for anyone to validate
   * later whether the reference still resolved to the same
   * data that the reference resolved to when the encapsulated
   * data value with reference was created.
   * @var CCDAbin
   */
  public $integrityCheck;

  /**
   * Specifies the algorithm used to compute the
   * integrityCheck value.
   * @var CCDAIntegrityCheckAlgorithm
   */
  public $integrityCheckAlgorithm;

  /**
   * Setter compressionAlgorithm
   *
   * @param \CCDACompressionAlgorithm $compression \CCDACompressionAlgorithm
   *
   * @return void
   */
  public function setCompression($compression) {
    $this->compression = $compression;
  }

  /**
   * Getter compressionAlgorithm
   *
   * @return \CCDACompressionAlgorithm
   */
  public function getCompression() {
    return $this->compression;
  }

  /**
   * Setter integrityCheck
   *
   * @param \CCDA_base_bin $integrityCheck \CCDA_base_bin
   *
   * @return void
   */
  public function setIntegrityCheck($integrityCheck) {
    $this->integrityCheck = $integrityCheck;
  }

  /**
   * Getter integrityCheck
   *
   * @return \CCDA_base_bin
   */
  public function getIntegrityCheck() {
    return $this->integrityCheck;
  }

  /**
   * Setter integrityCheckAlgorithm
   *
   * @param \CCDAIntegrityCheckAlgorithm $integrityCheckAlgorithm \CCDAIntegrityCheckAlgorithm
   *
   * @return void
   */
  public function setIntegrityCheckAlgorithm($integrityCheckAlgorithm) {
    $this->integrityCheckAlgorithm = $integrityCheckAlgorithm;
  }

  /**
   * Getter integrityCheckAlgorithm
   *
   * @return \CCDAIntegrityCheckAlgorithm
   */
  public function getIntegrityCheckAlgorithm() {
    return $this->integrityCheckAlgorithm;
  }

  /**
   * Setter language
   *
   * @param \CCDA_base_cs $language \CCDA_base_cs
   *
   * @return void
   */
  public function setLanguage($language) {
    $this->language = $language;
  }

  /**
   * Getter language
   *
   * @return \CCDA_base_cs
   */
  public function getLanguage() {
    return $this->language;
  }

  /**
   * Setter mediaType
   *
   * @param \CCDA_base_cs $mediaType \CCDA_base_cs
   *
   * @return void
   */
  public function setMediaType($mediaType) {
    $this->mediaType = $mediaType;
  }

  /**
   * Getter mediaType
   *
   * @return \CCDA_base_cs
   */
  public function getMediaType() {
    return $this->mediaType;
  }

  /**
   * Setter reference
   *
   * @param \CCDATEL $reference \CCDATEL
   *
   * @return void
   */
  public function setReference($reference) {
    $this->reference = $reference;
  }

  /**
   * Getter reference
   *
   * @return \CCDATEL
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * Setter thumbnail
   *
   * @param \CCDAthumbnail $thumbnail \CCDAthumbnail
   *
   * @return void
   */
  public function setThumbnail($thumbnail) {
    $this->thumbnail = $thumbnail;
  }

  /**
   * Getter thumbnail
   *
   * @return \CCDAthumbnail
   */
  public function getThumbnail() {
    return $this->thumbnail;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["reference"] = "CCDATEL xml|element max:1";
    $props["thumbnail"] = "CCDAthumbnail xml|element max:1";
    $props["mediaType"] = "CCDA_base_cs xml|attribute default:text/plain";
    $props["language"] = "CCDA_base_cs xml|attribute";
    $props["compression"] = "CCDACompressionAlgorithm xml|attribute";
    $props["integrityCheck"] = "CCDA_base_bin xml|attribute";
    $props["integrityCheckAlgorithm"] = "CCDAintegrityCheckAlgorithm xml|attribute default:SHA-1";
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
     * Test avec un language incorrecte
     *
     */

    $language = new CCDA_base_cs();
    $language->setData(" ");
    $this->setLanguage($language);

    $tabTest[] = $this->sample("Test avec un language incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un language correcte
     *
     */

    $language->setData("test");
    $this->setLanguage($language);

    $tabTest[] = $this->sample("Test avec un language correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) !== "CCDAED") {
      return $tabTest;
    }

    /**
     * Test avec un mediaType incorrecte
     *
     */

    $codeTest = new CCDA_base_cs();
    $codeTest->setData(" ");
    $this->setMediaType($codeTest);

    $tabTest[] = $this->sample("Test avec un mediaType incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un mediaType correcte
     *
     */


    $codeTest->setData("test");
    $this->setMediaType($codeTest);

    $tabTest[] = $this->sample("Test avec un mediaType correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un compression incorrecte
     *
     */

    $compression = new CCDACompressionAlgorithm();
    $compression->setData(" ");
    $this->setCompression($compression);

    $tabTest[] = $this->sample("Test avec une compression incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un compression correcte
     *
     */

    $compression->setData("GZ");
    $this->setCompression($compression);

    $tabTest[] = $this->sample("Test avec une compression correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un integrityCheck incorrecte
     *
     */

    $integrity = new CCDA_base_bin();
    $integrity->setData("111111111");
    $this->setIntegrityCheck($integrity);

    $tabTest[] = $this->sample("Test avec un integrityCheck incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un integrityCheck correcte
     *
     */

    $integrity->setData("JVBERi0xLjUNCiW1tbW1DQoxIDAgb2Jq");
    $this->setIntegrityCheck($integrity);

    $tabTest[] = $this->sample("Test avec un integrityCheck correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un integrityCheckAlgorithm incorrecte
     *
     */

    $integrityalgo = new CCDAintegrityCheckAlgorithm();
    $integrityalgo->setData("SHA-25");
    $this->setIntegrityCheckAlgorithm($integrityalgo);

    $tabTest[] = $this->sample("Test avec un integrityCheck incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un integrityCheckAlgorithm correcte
     *
     */

    $integrityalgo = new CCDAintegrityCheckAlgorithm();
    $integrityalgo->setData("SHA-256");
    $this->setIntegrityCheckAlgorithm($integrityalgo);

    $tabTest[] = $this->sample("Test avec un integrityCheck correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une reference incorrecte
     *
     */

    $tel = new CCDATEL();
    $com = new CCDAset_TelecommunicationAddressUse();
    $addruse = new CCDATelecommunicationAddressUse();
    $addruse->setData("test");
    $com->addData($addruse);
    $tel->setUse($com);
    $this->setReference($tel);

    $tabTest[] = $this->sample("Test avec une reference incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une reference correcte
     *
     */

    $tel = new CCDATEL();
    $com = new CCDAset_TelecommunicationAddressUse();
    $addruse = new CCDATelecommunicationAddressUse();
    $addruse->setData("MC");
    $com->addData($addruse);
    $tel->setUse($com);
    $this->setReference($tel);

    $tabTest[] = $this->sample("Test avec une reference correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) !== "CCDAED") {
      return $tabTest;
    }

    /**
     * Test avec un thumbnail incorrecte
     *
     */

    $thum = new CCDAthumbnail();
    $integrityalgo = new CCDAintegrityCheckAlgorithm();
    $integrityalgo->setData("SHA-25");
    $thum->setIntegrityCheckAlgorithm($integrityalgo);

    $this->setThumbnail($thum);

    $tabTest[] = $this->sample("Test avec un thumbnail incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un thumbnail correcte
     *
     */

    $integrityalgo->setData("SHA-256");
    $thum->setIntegrityCheckAlgorithm($integrityalgo);

    $this->setThumbnail($thum);

    $tabTest[] = $this->sample("Test avec un thumbnail correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
