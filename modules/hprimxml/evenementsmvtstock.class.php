<?php /* $Id: hprimxmlevenementmvtstock.class.php 7108 2009-10-21 16:10:46Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 7108 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementMvtStock extends CHPrimXMLDocument {
  function __construct() {
    $this->evenement = "evt_mvtStock";
    
    $version = CAppUI::conf('hprimxml evt_mvtStock version');
    if ($version == "1.01") {
      parent::__construct("mvtStock", "msgEvenementsMvtStocks101");
    } 
  }
}
?>