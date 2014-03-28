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
 * Class CADM
 * Transfert de données d'admission
 */
class CADM extends CHPrimSante {

  static $evenements = array(
    // L - Liaisons entre laboratoires
    "L" => "CHPrimSanteADML",
    // C - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
    "C" => "CHPrimSanteADMC",
    // R - Liaisons entre cabinets de radiologie et établissements cliniques ou hospitaliers
    "R" => "CHPrimSanteADMR",
  );

  /**
   * construct
   */
  function __construct() {
    $this->type = "ADM";
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
    $classname = "CHPrimSanteADM$code";

    return new $classname;
  }
}

