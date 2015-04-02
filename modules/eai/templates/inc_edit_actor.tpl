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

  confirmPurge = function (button, view) {
    var oForm = button.form;
    if (confirm("ATTENTION : Vous êtes sur le point de purger cet acteur")) {
      oForm._purge.value = "1";
      confirmDeletion(oForm, {
        typeName:'l\'acteur',
        objName:view
      } );
    }
  }
</script>

{{if (($actor->_class != "CInteropactor") || ($actor->_class != "CInteropSender")) && $can->edit}}
  <form name="edit{{$actor->_guid}}" action="?m={{$m}}" method="post"
        onsubmit="return onSubmitFormAjax(this, { onComplete: Control.Modal.close })">
    {{mb_key object=$actor}}
    {{mb_class object=$actor}}
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="parent_class" value="{{$actor->_parent_class}}" />
    <input type="hidden" name="_duplicate" value="" />
    <input type="hidden" name="_purge" value="0" />
    <input type="hidden" name="callback" value="InteropActor.refreshActorsAndActor" />
                  
    <table class="form">
      <tr>
        {{if $actor->_id}}
        <th class="title modify text" colspan="2">
          {{mb_include module=system template=inc_object_idsante400 object=$actor}}
          {{mb_include module=system template=inc_object_history object=$actor}}
          
          {{tr}}{{$actor->_class}}-title-modify{{/tr}} '{{$actor}}'
        </th>
        {{else}}
        <th class="title" colspan="2">
         {{tr}}{{$actor->_class}}-title-create{{/tr}}
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
                  
      {{mb_include module=eai template="`$actor->_parent_class`_inc"}}
        
      <tr>
        <td class="button" colspan="2">
          {{if $actor->_id}}
            <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
            {{if $actor instanceof CSenderMLLP}}
              <button type="button" class="duplicate"
                      onclick="$V(this.form._duplicate,1); this.form.onsubmit()">{{tr}}Duplicate{{/tr}}</button>
            {{/if}}
            <button type="button" class="trash"
                    onclick="confirmDeletion(this.form,
                      {typeName:'',objName:'{{$actor->_view|smarty:nodefaults|JSAttribute}}', ajax:true})">
              {{tr}}Delete{{/tr}}
            </button>

            {{if $can->admin}}
              <button type="button" class="cancel" onclick="confirmPurge(this, '{{$actor->_view|smarty:nodefaults|JSAttribute}}');">
                {{tr}}Purge{{/tr}}
              </button>
            {{/if}}
          {{else}}
             <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
          {{/if}}
        </td>
      </tr>  
    </table>
  </form>
{{/if}}