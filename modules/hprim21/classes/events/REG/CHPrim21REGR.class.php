<?php

/**
 * Transfert de données de reglèment - Liaisons entre cabinets de radiologie et établissements cliniques ou hospitaliers
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REGR
 * Transfert de données de reglèment - Liaisons entre cabinets de radiologie et établissements cliniques ou hospitaliers
 */
class CHPrim21REGR extends CHPrim21REG {
  function __construct() {
    $this->type_liaison = "R";
    
    parent::__construct();
  }
}

