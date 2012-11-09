<?php

/**
 * Transfert de données d'admission - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21ADMC
 * Transfert de données d'admission - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
 */
class CHPrim21ADMC extends CHPrim21ADM {
  function __construct() {
    $this->type_liaison = "C";
    
    parent::__construct();
  }
}

?>