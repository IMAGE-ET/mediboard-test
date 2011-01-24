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

  Main.add(function () {
    {{if count($receiver->_ref_exchanges_sources) > 0}}
      Control.Tabs.create('tabs-evenements-{{$receiver->_guid}}', true);
    {{/if}}

    $$('img.status').each(function(e) {
      if (!e.getAttribute('data-id')) {
        return;
      }
      
      e.src = "style/mediboard/images/icons/loading.gif";
    	var url = new Url("eai", "ajax_get_source_status");
    	url.addParam("source_guid", e.getAttribute('data-guid'));
      url.requestJSON(function(data) {
        if (data == "1") {
        	e.src = "images/icons/status_green.png";
        } else if (data == "0") {
        	e.src = "images/icons/status_red.png";
        }
      });
    });
  });
</script>

<table class="main">
  <tr>
    <td style="width:30%" rowspan="6">
      <table class="tbl">
        <tr>
          <th class="title" colspan="6">{{tr}}CInteropReceiver{{/tr}}</th>
        </tr>
        <tr>
          <th>{{tr}}CInteropReceiver-nom-court{{/tr}}</th>
          <th>{{tr}}CInteropReceiver-group_id-court{{/tr}}</th>
          <th>{{tr}}CInteropReceiver-actif-court{{/tr}}</th>
          <th>{{tr}}CInteropReceiver-_exchanges_sources_save-court{{/tr}}</th>
          <th>{{tr}}CInteropReceiver-_reachable-court{{/tr}}</th>
        </tr>
        {{foreach from=$receivers key=type_receiver item=_receivers}}
          <tr>
            <th class="category" colspan="6">
              <a style="float: right" class="button new notext" href="#" onclick="refreshReceiver(null, '{{$type_receiver}}');" title="Créer destinataire {{$type_receiver}}">
                {{tr}}{{$type_receiver}}-title-create{{/tr}}
              </a>
              {{tr}}{{$type_receiver}}{{/tr}}
            </th>
          </tr>
          {{foreach from=$_receivers item=_receiver}}
          <tr>
            <td>
              <a href="#" onclick="refreshReceiver('{{$_receiver->_guid}}', null);" title="Modifier le destinataire d'interopérabilité">
                {{mb_value object=$_receiver field="nom"}}
              </a>
            </td>
            <td>{{$_receiver->_ref_group->_view}}</td>
            <td>{{mb_value object=$_receiver field="actif"}}</td>
            <td>{{mb_value object=$_receiver field="_exchanges_sources_save"}} / {{$_receiver->_ref_exchanges_sources|@count}}</td>
            <td>
              {{foreach from=$_receiver->_ref_exchanges_sources item=_exchange_source}}
                <img class="status" data-id="{{$_exchange_source->_id}}" data-guid="{{$_exchange_source->_guid}}" src="images/icons/status_grey.png" />
              {{/foreach}}
            </td>
          </tr>
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane" id="receiver">
      {{mb_include template=inc_receiver}}
    </td> 
  </tr>
</table>