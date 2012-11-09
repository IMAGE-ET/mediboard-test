<?php

/**
 * Transfert de données d'admission - Liaisons entre laboratoires
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21ADML
 * Transfert de données d'admission - Liaisons entre laboratoires
 */
class CHPrim21ADML extends CHPrim21ADM {
  function __construct() {
    $this->type_liaison = "L";
    
    parent::__construct();
  }
}

?>