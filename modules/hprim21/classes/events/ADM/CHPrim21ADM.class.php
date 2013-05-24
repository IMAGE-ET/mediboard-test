<?php

/**
 * Transfert de données d'admission - H'2.1
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21ADM 
 * Transfert de données d'admission
 */
class CHPrim21ADM extends CHPREvent {
  function __construct() {
    $this->type = "ADM";
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);
    
    /* @todo Pas de création de message pour le moment */
  }
}

