<?php

/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * CHL7v3EventSVSRetrieveValueSet
 * Retrieve Value Set
 */
class CHL7v3EventSVSRetrieveValueSet extends CHL7v3EventSVS implements CHL7EventSVSRetrieveValueSet {
  /** @var string */
  public $_event_name = "RetrieveValueSet";

  /**
   * Build Retrieve Value Set event
   *
   * @param CMbObject $object compte rendu
   *
   * @see parent::build()
   *
   * @throws CMbException
   * @return void
   */
  function build($object) {
    parent::build($object);

    $data   = $object->_data;

    $dom = new CHL7v3MessageXML("utf-8", $this->version);
    //$dom->addNameSpaces();

    $RetrieveValueSetRequest = $dom->addElement($dom, "RetrieveValueSetRequest");

    $ValueSet = $dom->addElement($RetrieveValueSetRequest, "ValueSet");
    $dom->addValueSet($ValueSet, "id"     , "OID"     , $data);
    $dom->addValueSet($ValueSet, "version", "version" , $data);
    $dom->addValueSet($ValueSet, "lang"   , "language", $data);

    $this->message = $dom->saveXML();

    $this->updateExchange(false);
  }
}