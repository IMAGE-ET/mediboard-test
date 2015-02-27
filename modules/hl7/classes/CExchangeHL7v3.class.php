<?php

/**
 * Exchange HL7v3
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeHL7v3
 * Exchange HL7v3
 */

class CExchangeHL7v3 extends CEchangeXML {
  static $messages = array(
    "PRPA" => "CPRPA",
    "XDSb" => "CXDSb",
    "SVS"  => "CSVS"
  );

  // DB Table key
  public $exchange_hl7v3_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'exchange_hl7v3';
    $spec->key   = 'exchange_hl7v3_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["receiver_id"]   = "ref class|CReceiverHL7v3";
    $props["initiateur_id"] = "ref class|CExchangeHL7v3";
    $props["object_class"]  = "enum list|CPatient|CSejour|COperation|CAffectation|CConsultation|CFile|CCompteRendu|CMbObject show|0";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CExchangeHL7v3 initiateur_id";

    return $backProps;
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();

    $this->loadRefNotifications();
  }

  /**
   * @see parent::loadRefNotifications()
   */
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
  }

  /**
   * @see parent::handle()
   */
  function handle() {
    //return COperatorHL7v3::event($this);
  }

  /**
   * @see parent::getFamily()
   */
  function getFamily() {
    return self::$messages;
  }

  /**
   * @see parent::getErrors()
   */
  function getErrors() {
  }

  /**
   * @see parent::getObservations()
   */
  function getObservations($display_errors = false) {
  }

  /**
   * @see parent::setObjectClassIdPermanent()
   */
  function setObjectClassIdPermanent(CMbObject $mbObject) {
  }
}
