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
                onchange="var s=this.options[this.selectedIndex];$('object_selector').setVisible(s.hasClassName('ref')); this.form._object_class.value = s.className.split('|')[1]">
          {{foreach from=$ex_constraint->_ref_ex_class->getAvailableFields() item=_field name=_constraint}}
            {{assign var=_object value=$ex_constraint->_ref_object}}
            {{assign var=_spec value=$_object->_specs.$_field}}
            
            {{if $_field != $ex_constraint->_ref_object->_spec->key}}
              <option {{if $_spec instanceof CRefSpec}}class="ref class|{{$_spec->class}}"{{/if}} value="{{$_field}}" {{if $ex_constraint->field==$_field}}selected="selected"{{/if}}>
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
      <td>{{mb_field object=$ex_constraint field=value tabIndex="3"}}
        {{$ex_constraint->_ref_object}}
        <div id="object_selector" {{if !$ex_constraint->_ref_object}}style="display: none"{{/if}}>
          <button type="button" class="search" onclick="ObjectSelector.init()">{{tr}}Search{{/tr}}</button>
          <input type="hidden" name="_object_class" value="" />
          <input type="text" name="_object_view" readonly="readonly" value="{{$ex_constraint->_ref_object}}" />
          <script type="text/javascript">
            ObjectSelector.init = function(){  
              this.sForm     = "editConstraint";
              this.sId       = "value";
              this.sView     = "_object_view";
              this.sClass    = "_object_class";
              this.onlyclass = "true";
              this.pop();
            } 
           </script>
         </div>
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
