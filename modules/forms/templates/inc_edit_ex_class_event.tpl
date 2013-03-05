{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editClassEvent" method="post" action="?" onsubmit="return onSubmitFormAjax(this, ExClass.edit.curry({{$ex_class_event->ex_class_id}}))">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="del" value="0" />
  {{mb_class object=$ex_class_event}}
  {{mb_key object=$ex_class_event}}
  
  {{mb_field object=$ex_class_event field=ex_class_id hidden=true}}
  {{mb_field object=$ex_class_event field=host_class hidden=true}}
  {{mb_field object=$ex_class_event field=event_name hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_class_event colspan="2"}}

    <tr>
      <th>{{mb_label object=$ex_class_event field=event_name}}</th>
      <td>
        <select name="_event" class="notNull" onchange="ExClassEvent.setEvent(this)">
          <option value="" disabled="disabled" selected="selected"> &ndash; Choisir </option>
          {{foreach from=$classes item=_events key=_class}}
            <optgroup label="{{tr}}{{$_class}}{{/tr}}">
              {{foreach from=$_events item=_params key=_event_name}}
                <option value="{{$_class}}.{{$_event_name}}" {{if @$_params.auto}} data-auto="true" {{/if}}
                        {{if $_class == $ex_class_event->host_class && $_event_name == $ex_class_event->event_name}} selected="selected" {{/if}}
                        >
                  {{tr}}{{$_class}}{{/tr}} - {{tr}}{{$_class}}-event-{{$_event_name}}{{/tr}}
                  {{if @$_params.auto}} (déclench. auto){{/if}}
                </option>
              {{/foreach}}
            </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$ex_class_event field=disabled}}</th>
      <td>{{mb_field object=$ex_class_event field=disabled typeEnum=checkbox}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$ex_class_event field=unicity}}</th>
      <td>{{mb_field object=$ex_class_event field=unicity typeEnum=radio}}</td>
    </tr>
      
    <tr>
      <th></th>
      <td colspan="1">
        <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>

        {{if $ex_class_event->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'l\'évènement ',objName:'{{$ex_class_event->_view|smarty:nodefaults|JSAttribute}}'}, ExClass.edit.curry({{$ex_class_event->ex_class_id}}))">
            {{tr}}Delete{{/tr}}
          </button>
          <button type="button" class="search" onclick="ExObject.preview({{$ex_class_event->ex_class_id}}, '{{$ex_class_event->host_class}}-0')">
            {{tr}}Preview{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
  
{{if $ex_class_event->_id}}
  {{mb_include module=forms template=inc_edit_class_constraints constraints=$ex_class_event->_ref_constraints}}
{{/if}}
