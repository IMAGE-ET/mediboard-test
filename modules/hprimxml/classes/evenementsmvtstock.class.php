<?php /* $Id: hprimxmlevenementmvtstock.class.php 7108 2009-10-21 16:10:46Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 7108 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenements");

class CHPrimXMLEvenementMvtStock extends CHPrimXMLEvenements {
  function __construct() {
    $this->evenement = "evt_mvtStock";
    
    $version = str_replace(".", "", CAppUI::conf('hprimxml evt_mvtStock version'));
    parent::__construct("mvtStock", "msgEvenementsMvtStocks$version");
  }
}
?>