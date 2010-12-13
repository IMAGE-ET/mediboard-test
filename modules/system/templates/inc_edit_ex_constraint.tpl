{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  getForm("editConstraint").elements.field.select();
});
</script>

<form name="editConstraint" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_constraint->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_constraint_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_constraint}}
  {{mb_field object=$ex_constraint field=ex_class_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_constraint colspan="4"}}
    
    <tr>
      <th style="width: 12em;">{{mb_label object=$ex_constraint field=field}}</th>
      <td>
        <select name="field" class="{{$ex_constraint->_props.field}}" style="max-width: 15em;" tabIndex="1"
                onchange="$('object_selector_button').setVisible(this.options[this.selectedIndex].hasClassName('ref'))">
          {{foreach from=$ex_constraint->_ref_ex_class->getAvailableFields() item=_field name=_constraint}}
            {{if !$smarty.foreach._constraint.first}}
            <option {{if $_field}}class="ref"{{/if}} value="{{$_field}}" {{if $ex_constraint->field==$_field}}selected="selected"{{/if}}>
              {{tr}}{{$ex_constraint->_ref_ex_class->host_class}}-{{$_field}}{{/tr}}
            </option>
            {{/if}}
          {{/foreach}}
        </select>
      </td>
      
      <th>{{mb_label object=$ex_constraint field=_locale}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale tabIndex="4"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_constraint field=operator}}</th>
      <td>{{mb_field object=$ex_constraint field=operator tabIndex="2"}}</td>
      
      <th>{{mb_label object=$ex_constraint field=_locale_court}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale_court tabIndex="5"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_constraint field=value}}</th>
      <td>
        {{mb_field object=$ex_constraint field=value tabIndex="3"}}
        <!--<button id="object_selector_button" class="search">{{tr}}Search{{/tr}}</button>-->
      </td>
      
      <th>{{mb_label object=$ex_constraint field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale_desc tabIndex="6"}}</td>
    </tr>
      
    <tr>
      <th></th>
      <td colspan="3">
        {{if $ex_constraint->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'la contrainte ',objName:'{{$ex_constraint->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
