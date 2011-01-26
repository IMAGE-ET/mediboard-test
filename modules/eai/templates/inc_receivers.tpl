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
  Main.add(function () {
    $$('img.status').each(function(e) {
    	InteropReceiver.resfreshImageStatus(e);
    });
  });
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float: right" class="button change notext" href="#" onclick="InteropReceiver.refreshReceivers();">
        {{tr}}reload{{/tr}} {{tr}}CInteropReceiver{{/tr}}
      </a>
      {{tr}}CInteropReceiver{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{tr}}CInteropReceiver-nom-court{{/tr}}</th>
    <th>{{tr}}CInteropReceiver-group_id-court{{/tr}}</th>
    <th>{{tr}}CInteropReceiver-actif-court{{/tr}}</th>
    <th>{{tr}}CInteropReceiver-_reachable-court{{/tr}}</th>
  </tr>
  {{foreach from=$receivers key=type_receiver item=_receivers}}
    <tr>
      <th class="category" colspan="6">
        <a style="float: right" class="button new notext" href="#" onclick="InteropReceiver.refreshReceiver(null, '{{$type_receiver}}');" 
            title="Créer destinataire {{$type_receiver}}">
          {{tr}}{{$type_receiver}}-title-create{{/tr}}
        </a>
        {{tr}}{{$type_receiver}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_receivers item=_receiver}}
    <tr>
      <td>
        <a href="#" onclick="InteropReceiver.refreshReceiver('{{$_receiver->_guid}}', null);" title="Modifier le destinataire d'interopérabilité">
          {{mb_value object=$_receiver field="nom"}}
        </a>
      </td>
      <td>{{$_receiver->_ref_group->_view}}</td>
      <td>{{mb_value object=$_receiver field="actif"}}</td>
      <td>
        {{foreach from=$_receiver->_ref_exchanges_sources item=_exchange_source}}
          <img class="status" data-id="{{$_exchange_source->_id}}" 
            data-guid="{{$_exchange_source->_guid}}" src="images/icons/status_grey.png" 
            title="{{$_exchange_source->name}}"/>
        {{/foreach}}
      </td>
    </tr>
    {{/foreach}}
  {{/foreach}}
</table>