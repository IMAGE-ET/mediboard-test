<?php

/**
 * Transfert de données de reglèment - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REGC
 * Transfert de données de reglèment - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
 */
class CHPrim21REGC extends CHPrim21REG {
  function __construct() {
    $this->type_liaison = "C";
  }
}

?>