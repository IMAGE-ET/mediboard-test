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
 * Class CHPrimSanteADML
 * Transfert de données d'admission - Liaisons entre laboratoires
 */
class CHPrimSanteADML extends CHPrimSanteADM {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->type_liaison = "L";

    parent::__construct();
  }
}

