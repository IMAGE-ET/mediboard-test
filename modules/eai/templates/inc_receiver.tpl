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
  InteropReceiver.receiver_guid = '{{$receiver->_guid}}';
</script>

{{if ($receiver->_class_name != "CInteropReceiver") && $can->edit}}
  <form name="edit{{$receiver->_guid}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
    {{mb_key object=$receiver}}
    {{mb_class object=$receiver}}
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="callback" value="InteropReceiver.refreshReceiversAndReceiver" />
                  
    <table class="form">
      <tr>
        {{if $receiver->_id}}
        <th class="title modify text" colspan="2">
          {{mb_include module=system template=inc_object_idsante400 object=$receiver}}
          {{mb_include module=system template=inc_object_history object=$receiver}}
          
          {{tr}}{{$receiver->_class_name}}-title-modify{{/tr}} '{{$receiver}}'
        </th>
        {{else}}
        <th class="title" colspan="2">
         {{tr}}{{$receiver->_class_name}}-title-create{{/tr}}
        </th>
        {{/if}}
      </tr>
      <tr>
        <th>{{mb_label object=$receiver field="nom"}}</th>
        <td>{{mb_field object=$receiver field="nom"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$receiver field="libelle"}}</th>
        <td>{{mb_field object=$receiver field="libelle"}}</td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$receiver field="group_id"}}</th>
        <td>{{mb_field object=$receiver field="group_id" form="edit`$receiver->_guid`" autocomplete="true,1,50,true,true"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$receiver field="message"}}</th>
        <td>{{mb_field object=$receiver field="message"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$receiver field="actif"}}</th>
        <td>{{mb_field object=$receiver field="actif"}}</td>
      </tr>
      
      {{mb_include module=$receiver->_ref_module->mod_name template="`$receiver->_class_name`_inc"}}
      
      <tr>
        <td class="button" colspan="2">
          {{if $receiver->_id}}
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$receiver->_view|smarty:nodefaults|JSAttribute}}',ajax:true})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
             <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
          {{/if}}
        </td>
      </tr>     
    </table>
  </form>
  
  <table class="form" id="receiver_exchanges_sources">  
    {{mb_include template=inc_receiver_exchanges_sources}}
  </table>
{{/if}}