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

{{mb_script module=system script=exchange_source}}

<table class="tbl">
  <tr>
    <th class="title" colspan="5">
      <a style="float: right" class="button change notext" href="#" onclick="InteropActor.refreshActors('{{$actor->_class}}');">
        {{tr}}reload{{/tr}} {{tr}}{{$actor->_class}}{{/tr}}
      </a>
      {{tr}}{{$actor->_class}}{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$actor field="nom"}}</th>
    <th>{{mb_label object=$actor field="group_id"}}</th>
    <th>{{mb_label object=$actor field="_reachable"}}</th>
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
    <tr {{if !$_actor->actif}} class="opacity-50" {{/if}}>
      <td>
        <a href="#" onclick="InteropActor.refreshActor('{{$_actor->_guid}}', null);" title="Modifier l'acteur d'intégration">
          {{$_actor->_view}}
        </a>
      </td>
      <td>{{$_actor->_ref_group->_view}}</td>
      <td>
        {{foreach from=$_actor->_ref_exchanges_sources item=_exchange_source}}
          {{if !$_actor instanceof CSenderSOAP && !$_actor instanceof CSenderMLLP}}
            {{mb_include module=system template=inc_img_status_source exchange_source=$_exchange_source}}
          {{/if}}
        {{/foreach}}
      </td>
    </tr>
    {{/foreach}}
  {{/foreach}}
</table>