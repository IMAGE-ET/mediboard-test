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
 * Class CHPrimSanteORUR
 * Transmission du résultat d'un test - Liaisons entre cabinets de radiologie et établissements cliniques ou hospitaliers
 */
class CHPrimSanteORUR extends CHPrimSanteORU {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->type_liaison = "R";

    parent::__construct();
  }
}

