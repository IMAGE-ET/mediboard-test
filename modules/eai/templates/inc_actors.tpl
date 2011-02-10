{{*
 * View Interop Senders EAI
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
	  $$('#{{$actor->_class_name}}s img.status').each(function(e) {
      InteropActor.resfreshImageStatus(e);
    });
  });
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float: right" class="button change notext" href="#" onclick="InteropActor.refreshActors('{{$actor->_class_name}}');">
        {{tr}}reload{{/tr}} {{tr}}{{$actor->_class_name}}{{/tr}}
      </a>
      {{tr}}{{$actor->_class_name}}{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$actor field="nom"}}</th>
    <th>{{mb_label object=$actor field="group_id"}}</th>
    <th>{{mb_label object=$actor field="actif"}}</th>
    <th>{{mb_label object=$actor field="_reachable"}}</th>
    <th>{{mb_label object=$actor field="_ref_last_message"}}</th>
  </tr>
  {{foreach from=$actors key=type_actor item=_actors}}
    <tr>
      <th class="category" colspan="6">
        <a style="float: right" class="button new notext" href="#" onclick="InteropActor.refreshActor(null, '{{$type_actor}}');" 
            title="Créer acteurs {{$type_actor}}">
          {{tr}}{{$type_actor}}-title-create{{/tr}}
        </a>
        {{tr}}{{$type_actor}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_actors item=_actor}}
    <tr>
      <td>
        <a href="#" onclick="InteropActor.refreshActor('{{$_actor->_guid}}', null);" title="Modifier l'acteur d'intégration">
          {{mb_value object=$_actor field="nom"}}
        </a>
      </td>
      <td>{{$_actor->_ref_group->_view}}</td>
      <td>{{mb_value object=$_actor field="actif"}}</td>
      <td>
        {{foreach from=$_actor->_ref_exchanges_sources item=_exchange_source}}
          <img class="status" data-id="{{$_exchange_source->_id}}" 
            data-guid="{{$_exchange_source->_guid}}" src="images/icons/status_grey.png" 
            title="{{$_exchange_source->name}}"/>
        {{/foreach}}
      </td>
      <td>
        {{assign var=last_message value=$_actor->_ref_last_message}}
        {{if isset($last_message|smarty:nodefaults)}}
          <a title="{{mb_value object=$last_message field="date_echange"}}" href="?m=eai&tab=vw_idx_exchange_data_format#exchange_class_name={{$last_message->_class_name}}&exchange_type={{$last_message->type}}">
            {{mb_value object=$last_message field="date_echange" format=relative}}
          </a>
        {{/if}}
      </td>
    </tr>
    {{/foreach}}
  {{/foreach}}
</table>