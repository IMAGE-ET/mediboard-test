{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $form_name && $ex_objects|@count == 0}}
  {{mb_return}}
{{/if}}

{{if !$form_name}}
  <button type="button" class="forms" {{if $ex_objects|@count == 0}}disabled{{/if}} onclick="ObjectTooltip.createDOM(this, $(this).next(), {duration: 0});">
    Form. ({{$count_available}})
  </button>
{{else}}
  <fieldset>
    <legend>Formulaire {{tr}}{{$object->_class}}-event-{{$event_name}}{{/tr}}</legend>
{{/if}}

<table class="layout" style="{{if !$form_name}} border: 1px solid #000; display: none; {{/if}} width: 400px; max-width: 700px;">
  {{foreach from=$ex_objects key=_ex_class_id item=_ex_objects}}
    <tr>
      <td style="text-align: right; {{if !$form_name}} font-weight: bold; vertical-align: middle; white-space: normal; min-width: 200px; {{/if}}">
        {{$ex_classes.$_ex_class_id->name}}
      </td>
      <td style="text-align: left; white-space: normal;">
        {{foreach from=$_ex_objects item=_ex_object}}
          {{if $_ex_object->_id}}
            <button type="button" class="edit" title="{{mb_value object=$_ex_object field=owner_id}}"
              onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event_name}}', '{{$_element_id}}')">
              {{mb_value object=$_ex_object field=datetime_create}}

              {{if $ex_classes.$_ex_class_id->_formula_field}}
                {{assign var=_formula_field value=$ex_classes.$_ex_class_id->_formula_field}}

                <strong>= {{$_ex_object->$_formula_field}}</strong>
              {{/if}}
            </button>
          {{else}}
            <button type="button" class="new" value="{{$_ex_class_id}}"
                    onclick="selectExClass(this, '{{$object->_guid}}', '{{$event_name}}', '{{$_element_id}}'{{if $form_name}}, '{{$form_name}}'{{/if}})">
              Nouveau
            </button>
          {{/if}}
        {{/foreach}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">{{tr}}CExClass.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

{{if $form_name}}
  </fieldset>
{{/if}}