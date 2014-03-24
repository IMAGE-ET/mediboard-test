/**
 * JS function Sender SOAP EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

SenderSOAP = {  
  dispatch : function(sender_soap_id, message) {
    new Url("webservices", "ajax_dispatch_event")
      .addParam("sender_soap_id", sender_soap_id)
      .addParam("message", message)
      .requestUpdate("CSenderSOAP-utilities_dispatch");
  }
};