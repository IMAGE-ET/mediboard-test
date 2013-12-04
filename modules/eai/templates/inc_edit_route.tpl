{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
   Main.add(
    function() {
      Route.autocomplete_receiver();
      Route.autocomplete_sender()
    }
   )
</script>

<form name="editRoute" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  {{mb_key object=$route}}
  {{mb_class object=$route}}
  <table class="form">
    <tr>
      {{if $route->_id}}
        <th class="title modify text" colspan="2">
          {{mb_include module=system template=inc_object_idsante400 object=$route}}
          {{mb_include module=system template=inc_object_history object=$route}}
          {{tr}}{{$route->_class}}-title-modify{{/tr}} '{{$route}}'
      {{else}}
        <th class="title" colspan="2">
          {{tr}}{{$route->_class}}-title-create{{/tr}}
      {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_title object=$route field="sender_class"}}</th>
      <td>
        <select name="sender_class">
          {{foreach from=$list_sender item=_sender}}
            <option value="{{$_sender}}">{{tr}}{{$_sender}}{{/tr}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_title class=CInteropSender field="nom"}} {{tr}}CInteropSender{{/tr}}</th>
      <td>
        <input type="hidden" name="sender_id" value="{{if $route->_ref_sender}}{{$route->_ref_sender->_id}}{{/if}}">
        <input type="text" class="autocomplete" name="sender_id_autocomplete"
               value="{{if $route->_ref_sender}}{{$route->_ref_sender->nom}}{{/if}}">
      </td>
    </tr>
    <tr>
      <th>{{mb_title object=$route field="receiver_class"}}</th>
      <td>
        <select name="receiver_class">
          {{foreach from=$list_receiver item=_receiver}}
            <option value="{{$_receiver}}">{{tr}}{{$_receiver}}{{/tr}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_title class=CInteropReceiver field="nom"}} {{tr}}CInteropReceiver{{/tr}}</th>
      <td>
        <input type="hidden" name="receiver_id" value="{{if $route->_ref_receiver}}{{$route->_ref_receiver->_id}}{{/if}}">
        <input type="text" class="autocomplete" name="receiver_id_autocomplete"
               value="{{if $route->_ref_receiver}}{{$route->_ref_receiver->nom}}{{/if}}">
      </td>
    </tr>
    <tr>
      <th>{{mb_title object=$route field="active"}}</th>
      <td>{{mb_field object=$route field="active"}}</td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $route->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button"
                  onclick="confirmDeletion(this.form, {objName:'{{$route->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>