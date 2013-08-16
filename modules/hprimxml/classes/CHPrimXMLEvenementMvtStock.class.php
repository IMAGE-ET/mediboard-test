<?php

/**
 * Évènement lié aux mouvements de stock
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLEvenementMvtStock
 */
class CHPrimXMLEvenementMvtStock extends CHPrimXMLEvenements {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->evenement = "evt_mvtStock";
    
    $version = str_replace(".", "", CAppUI::conf('hprimxml evt_mvtStock version'));
    parent::__construct("mvtStock", "msgEvenementsMvtStocks$version");
  }
}
