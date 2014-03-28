<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CORU
 * Transfert de résultats d'analyses,
 */
class CORU extends CHPrimSante{

  static $evenements = array(
    // L - Liaisons entre laboratoires
    "L" => "CHPrimSanteORUL",
    // C - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
    "C" => "CHPrimSanteORUC",
    // R - Liaisons entre cabinets de radiologie et établissements cliniques ou hospitaliers
    "R" => "CHPrimSanteORUR",
  );

  /**
   * construct
   */
  function __construct() {
    $this->type = "ORU";
  }

  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object|null An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code = $exchange->code;
    //@todo voir pour la gestion
    $classname = "CHPrimSanteORU$code";

    return new $classname;
  }
}

