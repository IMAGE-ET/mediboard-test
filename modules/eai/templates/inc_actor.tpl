{{*
 * View Interop Actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  InteropActor.actor_guid = '{{$actor->_guid}}';
</script>

{{if (($actor->_class_name != "CInteropactor") || ($actor->_class_name != "CInteropSender")) && $can->edit}}
  <form name="edit{{$actor->_guid}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
    {{mb_key object=$actor}}
    {{mb_class object=$actor}}
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="parent_class_name" value="{{$actor->_parent_class_name}}" />
    <input type="hidden" name="callback" value="InteropActor.refreshActorsAndActor" />
                  
    <table class="form">
      <tr>
        {{if $actor->_id}}
        <th class="title modify text" colspan="2">
          {{mb_include module=system template=inc_object_idsante400 object=$actor}}
          {{mb_include module=system template=inc_object_history object=$actor}}
          
          {{tr}}{{$actor->_class_name}}-title-modify{{/tr}} '{{$actor}}'
        </th>
        {{else}}
        <th class="title" colspan="2">
         {{tr}}{{$actor->_class_name}}-title-create{{/tr}}
        </th>
        {{/if}}
      </tr>
      <tr>
        <th>{{mb_label object=$actor field="nom"}}</th>
        <td>{{mb_field object=$actor field="nom"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$actor field="libelle"}}</th>
        <td>{{mb_field object=$actor field="libelle"}}</td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$actor field="group_id"}}</th>
        <td>{{mb_field object=$actor field="group_id" form="edit`$actor->_guid`" autocomplete="true,1,50,true,true"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$actor field="actif"}}</th>
        <td>{{mb_field object=$actor field="actif"}}</td>
      </tr>
                  
      {{mb_include module=eai template="`$actor->_parent_class_name`_inc"}}
      
      <tr>
        <td class="button" colspan="2">
          {{if $actor->_id}}
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$actor->_view|smarty:nodefaults|JSAttribute}}',ajax:true})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
             <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
          {{/if}}
        </td>
      </tr>     
    </table>
  </form>
  
  <table class="form" id="actor_exchanges_sources">  
    {{mb_include template=inc_actor_exchanges_sources}}
  </table>
{{/if}}