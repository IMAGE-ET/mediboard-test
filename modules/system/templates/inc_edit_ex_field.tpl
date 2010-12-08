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
  var fields = {{$other_fields|@json}};
  ExFieldSpec.edit("{{$spec_type}}", '{{$ex_field->prop}}', 'CExObject', '{{$ex_field->name}}', fields);
  getForm("editField").elements.name.select();
});
</script>

<form name="editField" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_field->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_field}}
  {{mb_field object=$ex_field field=ex_class_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_field colspan="4"}}
    
    <tr>
      <th style="width: 12em;">{{mb_label object=$ex_field field=name}}</th>
      <td>{{mb_field object=$ex_field field=name}}</td>
      
      <th>{{mb_label object=$ex_field field=_locale}}</th>
      <td>{{mb_field object=$ex_field field=_locale}}</td>
    </tr>
    <tr>
      <th><label for="_type">Type</label></th>
      <td>
        <select name="_type" onchange="ExFieldSpec.edit($V(this), '{{$ex_field->prop}}', 'CExObject', '{{$ex_field->name}}', [])">
          {{foreach from="CMbFieldSpecFact"|static:classes item=_class key=_key}}
            <option value="{{$_key}}" {{if $_key == $spec_type}}selected="selected"{{/if}}>{{$_key}}</option>
          {{/foreach}}
        </select>
      </td>
      
      <th>{{mb_label object=$ex_field field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_field field=_locale_desc}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_field field=prop}}</th>
      <td>{{mb_field object=$ex_field field=prop readonly="readonly" size=35}}</td>
      
      <th>{{mb_label object=$ex_field field=_locale_court}}</th>
      <td>{{mb_field object=$ex_field field=_locale_court}}</td>
    </tr>
      
    <tr>
      <th></th>
      <td colspan="3">
        {{if $ex_field->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le champ ',objName:'{{$ex_field->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

<div id="fieldSpecEditor" style="white-space: normal;"></div>
