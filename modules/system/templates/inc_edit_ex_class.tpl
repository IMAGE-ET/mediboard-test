{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{main}}
  ExClass.id = "{{$ex_class->_id}}";
{{/main}}

<table class="main form">
  {{mb_include module=system template=inc_form_table_header object=$ex_class colspan="2"}}

  <tr>
    <td colspan="2">
      <form name="editExClass" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_ex_class_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="callback" value="ExClass.edit" />
        {{mb_key object=$ex_class}}
        
        {{mb_field object=$ex_class field=host_class hidden=true}}
        {{mb_field object=$ex_class field=event hidden=true}}
        
        <table class="main form">
          <tr>
            <th>{{mb_label object=$ex_class field=event}}</th>
            <td>
              {{if !$ex_class->_id}}
              <select name="_event" class="notNull" onchange="ExClass.setEvent(this)">
                <option value=""> &ndash; Choisir </option>
                {{foreach from=$classes item=_events key=_class}}
                  <optgroup label="{{tr}}{{$_class}}{{/tr}}">
                    {{foreach from=$_events item=_params key=_event_name}}
                      <option value="{{$_class}}.{{$_event_name}}" {{if $_class == $ex_class->host_class && $_event_name == $ex_class->event}}selected="selected"{{/if}}>
                        {{tr}}{{$_class}}{{/tr}} - {{$_event_name}}
                        {{if array_key_exists("multiple", $_params) && $_params.multiple}}
                          (multiple)
                        {{/if}}
                      </option>
                    {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
              {{else}}
                {{tr}}{{$ex_class->host_class}}{{/tr}} - {{$ex_class->event}}
              {{/if}}
            </td>
            
            <th>{{mb_label object=$ex_class field=name}}</th>
            <td>{{mb_field object=$ex_class field=name}}</td>
            
            <td>
              {{if $ex_class->_id}}
                <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'la classe étendue ',objName:'{{$ex_class->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

{{if $ex_class->_id}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("ExClass-back", true);
});
</script>

<ul class="control_tabs" id="ExClass-back">
  <li><a href="#fields">{{tr}}CExClass-back-fields{{/tr}}</a></li>
  <li><a href="#constraints">{{tr}}CExClass-back-constraints{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<table class="main layout" id="fields" style="display: none;">
  <tr>
    <td style="width: 20em; padding-right: 5px;">
      <button type="button" class="new" style="float: right;" onclick="ExField.create({{$ex_class->_id}})">
        {{tr}}CExClassField-title-create{{/tr}}
      </button>

      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClassField field=name}}</th>
          {{*<th>{{mb_title class=CExClassField field=prop}}</th>*}}
        </tr>
        {{foreach from=$ex_class->_ref_fields item=_field}}
          <tr>
            <td title="{{$_field->name}}">
              <a href="#1" onclick="ExField.edit({{$_field->_id}})"><strong>
                {{if $_field->_locale}}
                  {{$_field->_locale}}
                {{else}}
                  [{{$_field->name}}]
                {{/if}}
              </strong></a>
            </td>
            {{*<td>{{$_field->prop}}</td>*}}
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2">{{tr}}CExClassField.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="exFieldEditor">
      <!-- exFieldEditor -->
    </td>
  </tr>
</table>

<table class="main layout" id="constraints" style="display: none;">
  <tr>
    <td style="width: 20em; padding-right: 5px;">
      <button type="button" class="new" style="float: right;" onclick="ExConstraint.create({{$ex_class->_id}})">
        {{tr}}CExClassConstraint-title-create{{/tr}}
      </button>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClassConstraint field=field}}</th>
          <th>{{mb_title class=CExClassConstraint field=operator}}</th>
          <th>{{mb_title class=CExClassConstraint field=value}}</th>
        </tr>
        {{foreach from=$ex_class->_ref_constraints item=_constraint}}
          <tr>
            <td>
              <a href="#1" onclick="ExConstraint.edit({{$_constraint->_id}})">
                <strong>{{mb_value object=$_constraint field=field}}</strong>
              </a>
            </td>
            <td>{{mb_value object=$_constraint field=operator}}</td>
            <td>{{mb_value object=$_constraint field=value}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="3">{{tr}}CExClassConstraint.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="exConstraintEditor">
      <!-- exConstraintEditor -->
    </td>
  </tr>
</table>
{{/if}}