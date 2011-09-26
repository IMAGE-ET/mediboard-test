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
    var url = new Url("webservices", "ajax_dispatch_event");
	url.addParam("sender_soap_id", sender_soap_id);
	url.addParam("message", message);
	url.requestUpdate("CSenderSOAP-utilities_dispatch");
  }
};