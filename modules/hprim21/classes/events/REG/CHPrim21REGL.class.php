<?php

/**
 * Transfert de données de reglèment - Liaisons entre laboratoires
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REGL
 * Transfert de données de reglèment - Liaisons entre laboratoires
 */
class CHPrim21REGL extends CHPrim21REG {
  function __construct() {
    $this->type_liaison = "L";
    
    parent::__construct();
  }
}

?>