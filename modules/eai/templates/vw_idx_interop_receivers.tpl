{{*
 * View Interop Receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  function refreshReceiver(receiver_guid, receiver_class_name){
	  var url = new Url("eai", "ajax_refresh_receiver");
	  url.addParam("receiver_guid", receiver_guid);
	  url.addParam("receiver_class_name", receiver_class_name);
	  url.requestUpdate("receiver");
  }

  function refreshReceivers(){
    var url = new Url("eai", "ajax_refresh_receivers");
    url.requestUpdate("receivers");
  }

  function refreshReceiverExchangesSources(receiver_guid){
    var url = new Url("eai", "ajax_refresh_receiver_exchanges_sources");
    url.addParam("receiver_guid", receiver_guid);
    url.requestUpdate("receiver_exchanges_sources");
  }
</script>

<table class="main">
  <tr>
    <td style="width:30%" rowspan="6" id="receivers">
      {{mb_include template=inc_receivers}}
    </td>
    <td class="halfPane" id="receiver">
      {{mb_include template=inc_receiver}}
    </td> 
  </tr>
</table>