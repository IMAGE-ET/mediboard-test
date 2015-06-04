{{*
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<style>
  #list_dest li button {
    border:none;
    padding:0;
    background: url('style/mediboard/images/buttons/delete-tiny.png') transparent no-repeat;
    width: 11px;
    height:11px;
  }
  #list_dest li {
    list-style: none;
  }
</style>

<script>
  {{if $usermessage->_can_edit}}
    Main.add(function() {
      var form = getForm("edit_usermessage");
      var element = form.elements._to_autocomplete_view;
      var url = new Url("system", "ajax_seek_autocomplete");
      url.addParam("object_class", "CMediusers");
      url.addParam("input_field", element.name);
      {{if $conf.messagerie.resctriction_level_messages == "group"}}
        url.addParam("ljoin[functions_mediboard]", "functions_mediboard.function_id = users_mediboard.function_id");
        url.addParam("where[group_id]", "{{$g}}");
      {{/if}}
      {{if $conf.messagerie.resctriction_level_messages == "function"}}
        url.addParam("where[function_id]", "{{$usermessage->_ref_user_creator->function_id}}");
      {{/if}}
      url.addParam("show_view", true);
      url.autoComplete(element, null, {
        minChars: 3,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field,selected){
          var id = selected.getAttribute("data-id");
          var name = selected.down('span.view').innerHTML;
          var function_color = selected.down('div', 0).getStyle('border-left');
          addDest(id, name, function_color);
          $V(element, '');
        }
      });
    });
  {{/if}}

  addDest = function(id, name, style) {
    var dest_list = $('list_dest');
    var existing = $("dest_"+id);
    if (existing) {
      return;
    }
    dest_list.insert('<li id="dest_'+id+'" style="border-left:'+style+';">'+name+'<input type="hidden" name="dest[]" value="'+id+'"/><button class="delete notext" type="button" style="display: inline; margin-left: 5px;" onclick="removeDest(\''+id+'\');"></button></li>');
  };

  removeDest = function(id) {
    $('dest_'+id).remove();
  };

</script>

<form method="post" action="?" name="edit_usermessage" onsubmit="$V(this.content, CKEDITOR.instances.htmlarea.getData()); return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="messagerie"/>
  <input type="hidden" name="dosql" value="do_usermessage_aed"/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_send" value="0" />
  <input type="hidden" name="_archive" value="0" />
  <input type="hidden" name="_readonly" value="{{if $usermessage->_can_edit}}0{{else}}1{{/if}}" />
  <input type="hidden" name="usermessage_id" value="{{$usermessage->_id}}" />
  <input type="hidden" name="in_reply_to" value="{{$usermessage->in_reply_to}}" />
  <input type="hidden" name="callback" value="callbackModalMessagerie" />

  <table class="main">
    <tr>
      <td id="message_area" style="width:75%;">
        <table class="form">
          <tr>
            <th class="narrow">{{mb_label object=$usermessage field=creator_id}}</th>
            <td>
              {{mb_field object=$usermessage field=creator_id hidden=1}}
              <div class="mediuser" style="border-color: #{{$usermessage->_ref_user_creator->_ref_function->color}};">
                {{$usermessage->_ref_user_creator}}
              </div>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$usermessage field=subject}}</th>
            <td>
              {{if !$usermessage->_can_edit}}
                {{mb_value object=$usermessage field=subject}}
              {{else}}
                {{mb_field object=$usermessage field=subject size=60}}
              {{/if}}
            </td>
          </tr>
          <tr>
            <td colspan="2" style="height: 300px">{{mb_field object=$usermessage field=content id="htmlarea"}}</td>
          </tr>
        </table>
      </td>
      <td id="dest_area">
        <h2>Destinataires</h2>
        {{if $usermessage->_can_edit}}
          <input type="text" name="_to_autocomplete_view" />
        {{/if}}
        <ul id="list_dest">
        {{foreach from=$usermessage->_ref_destinataires item=_dest}}
          <li id="dest_{{$_dest->_ref_user_to->_id}}">
            <span class="mediuser" style="border-color: #{{$_dest->_ref_user_to->_ref_function->color}};">
              {{$_dest->_ref_user_to}} {{if $_dest->datetime_read}}(lu){{/if}}
            </span>

            {{if $usermessage->_can_edit}}
              <input type="hidden" name="dest[]" value="{{$_dest->_ref_user_to->_id}}"/>
              <button class="delete notext" type="button" style="display: inline; margin-left: 5px;" onclick="removeDest('{{$_dest->_ref_user_to->_id}}');"></button>
            {{/if}}
          </li>
        {{/foreach}}
        </ul>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $usermessage->_can_edit}}
          <button type="button" onclick="$V(this.form._send, 1); this.form.onsubmit();">
            <i class="msgicon fa fa-send"></i>
            {{tr}}Send{{/tr}}
          </button>
          <button type="button" onclick="this.form.submit();window.parent.Control.Modal.close();">
            <i class="msgicon fa fa-save"></i>
            {{tr}}Save{{/tr}}
          </button>
          {{if $usermessage->_id}}
            <button onclick="$V(this.form.del, 1); window.parent.Control.Modal.close();">
              <i class="msgicon fa fa-save"></i>
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
        {{else}}
          <button type="button" onclick="window.parent.Control.Modal.close(); window.parent.UserMessage.create('{{$usermessage->creator_id}}', '{{$usermessage->_id}}');">
            <i class="msgicon fa fa-reply"></i>
            {{tr}}CUserMail-button-answer{{/tr}}
          </button>
        {{/if}}
        <button type="button" onclick="window.parent.Control.Modal.close();">
          <i class="msgicon fa fa-times"></i>
          {{tr}}Cancel{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>