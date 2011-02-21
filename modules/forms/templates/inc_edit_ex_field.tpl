{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var fields = {{$other_fields|@json}};
	var form = getForm("editField");
  ExFieldSpec.edit(form.elements._type, '{{$ex_field->prop}}', 'CExObject', '{{$ex_field->name}}', fields, "{{$ex_field->_id}}");
  form.elements._locale.select();
});

checkExField = function(form) {
  if (!checkForm(form)) return false;
	
	var prop = $V(form.elements.prop);
	
	if (prop.indexOf("enum") == 0 && prop.indexOf("list|") == -1) {
	  alert("Un champ de type Liste de choix nécessite une liste d'options");
		$(getForm("editFieldSpec").elements["list[]"][0]).tryFocus();
	  return false;
	}
	
	return true;
}

updateInternalName = function(e){
  var form = e.form;
	var str = ExField.slug($V(e));
	$V(form.elements.name, str);
}
</script>

<form name="editField" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {check: checkExField{{if $ex_field->ex_class_id}},onComplete: ExClass.edit.curry({{$ex_field->ex_class_id}}){{/if}}})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_enum_translation" value="" />
	
	{{if !$ex_field->ex_class_id}}
	  <input type="hidden" name="callback" value="ExConcept.editCallback" />
	{{/if}}
	
  {{mb_key object=$ex_field}}
  {{mb_field object=$ex_field field=ex_class_id hidden=true}}
  {{mb_field object=$ex_field field=concept_id hidden=true}}
  
  <table class="form">
  	
    {{assign var=object value=$ex_field}}
		<tr>
		  {{if $object->_id}}
		  <th class="title modify" colspan="4">
		    {{mb_include module=system template=inc_object_notes}}
		    {{mb_include module=system template=inc_object_idsante400}}
		    {{mb_include module=system template=inc_object_history}}
		    {{tr}}{{$object->_class_name}}{{if !$object->ex_class_id}}.concept{{/if}}-title-modify{{/tr}} 
		    '{{$object}}'
		  </th>
		  {{else}}
		  <th class="title" colspan="4">
		    {{tr}}{{$object->_class_name}}{{if !$object->ex_class_id}}.concept{{/if}}-title-create{{/tr}} 
		  </th>
		  {{/if}}
		</tr>
    
    <tr>
      <th style="width: 8em;">{{mb_label object=$ex_field field=_locale}}</th>
      <td>
      	{{if $ex_field->_id}}
          {{mb_field object=$ex_field field=_locale size=40}}
				{{else}}
      	  {{mb_field object=$ex_field field=_locale onkeyup="updateInternalName(this)" size=40}}
				{{/if}}
			</td>
      <th>{{mb_label object=$ex_field field=name}}</th>
      <td>{{mb_field object=$ex_field field=name}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_field field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_field field=_locale_desc tabIndex="2" size=40}}</td>
      
      <th>{{mb_label object=$ex_field field=prop}}</th>
      <td>{{mb_field object=$ex_field field=prop readonly="readonly" size=30}}</td>
    </tr>
    <tr>
      <th><label for="_type">Type</label></th>
      <td>
        <select {{if $ex_field->_id}}disabled="disabled"{{/if}} name="_type" onchange="ExFieldSpec.edit(this, '{{$ex_field->prop}}', 'CExObject', '{{$ex_field->name}}', [], '{{$ex_field->_id}}')">
          {{* Only non-concepts can have a concept *}}
					{{if $ex_field->ex_class_id}}
					<optgroup label="Concepts">
						{{foreach from=$list_concepts item=_concept}}
	            <option value="{{$_concept->_guid}}" {{if $_concept->_guid == $ex_field->concept_id}}selected="selected"{{/if}}>{{$_concept}}</option>
	          {{foreachelse}}
						  <option disabled="disabled">{{tr}}None{{/tr}}</option>
						{{/foreach}}
					</optgroup>
					{{/if}}
					
					<optgroup label="Types">
	          {{foreach from="CMbFieldSpecFact"|static:classes item=_class key=_key}}
	            <option value="{{$_key}}" {{if $_key == $spec_type && !$ex_field->concept_id}}selected="selected"{{/if}}>{{tr}}CMbFieldSpec.type.{{$_key}}{{/tr}}</option>
	          {{/foreach}}
					</optgroup>
        </select>
      </td>
      
      <th>{{mb_label object=$ex_field field=_locale_court}}</th>
      <td>{{mb_field object=$ex_field field=_locale_court tabIndex="3" size=30}}</td>
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
